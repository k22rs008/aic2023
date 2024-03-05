<?php
namespace aic\models;

use aic\models\Member;
use aic\models\User;
use aic\models\RsvSample;
use aic\models\RsvMember;
use aic\models\Util;
use aic\views\Html;

class Reserve extends Model {
    protected $table = "tb_reserve";
    protected $rsv_view = "vw_reserve";
    protected $rsv_report = "vw_report";
    protected $rsv_seq = "seq_reserve";
    protected $inst_table = 'tb_instrument';
    protected $member_table = 'tb_member';
    
    function getDetail($id)
    {
        $rsv = parent::getDetail($id);        
        if (!$rsv){ // prepare a dummy reservation for insertion  
            $filefds = $this->getFileds();
            foreach ($filefds as $f){
                $key = $f['Field'];
                $rsv[$key] = '';
            }
            $rsv['id'] = 0;      
            $rsv['xray_chk'] = 0;
            $userid = (new User)->getLoginUid();
            $member = (new Member)->getList("uid='$userid'");
            $rsv['apply_member'] = (count($member)>0) ? $member[0] : null;
            $rsv['rsv_member'] = $rsv['sample_nature'] = [];  
            $rsv['sample_other']='';    
            $rsv['sample_state']=1;
            $rsv['stime'] = $rsv['etime'] = date('Y-m-d H:i');
            return $rsv;
        }
        // real reservation for edit or show
        $inst_id = $rsv['instrument_id'];        
        $instrument = (new Instrument)->getDetail($inst_id); 
        $room_id = $instrument['room_id'];
        $room = (new Room)->getDetail($room_id);
        $rsv['room_no'] =$room['room_no'];
        $rsv['room_name'] =$room['room_name'];
        $rsv['instrument_fullname'] = $instrument['fullname'];
        $rsv['instrument_shortname'] = $instrument['shortname'];
        $rsv['apply_member'] = (new Member)->getDetail($rsv['apply_mid']);
        $rsv['master_member'] = (new Member)->getDetail($rsv['master_mid']);
        $_dept_code = $rsv['master_member']['dept_code'];
        $rsv['dept_name'] = KsuCode::FACULTY_DEPT[$_dept_code];

        $rsv['rsv_member'] = (new RsvMember)->getList('reserve_id='.$id);
        $students = array_filter($rsv['rsv_member'], function($a){ return $a['category']==1; });
        $rsv['student_n'] = count($students);
        $rsv['staff_n'] = count($rsv['rsv_member'])- count($students); 

        $rsv['sample_state_str'] = KsuCode::SAMPLE_STATE[$rsv['sample_state']];
        $rsv['xray_chk_str'] = KsuCode::YESNO[$rsv['xray_chk']]; 

        $samples = (new RsvSample)->getList('reserve_id='.$id);
        $selected = [];
        $other = '';
        foreach ($samples as $sample){
            $selected[] = $sample['nature'];
            if ($sample['nature'] == 4) $other = $sample['other'];
        }
        $rsv['sample_other'] = $other;
        $rsv['sample_nature'] = $selected;
        $_natures = Util::array_slice_by_index(KsuCode::SAMPLE_NATURE, $selected);
        $rsv['sample_nature_str'] = implode(', ', $_natures);
        $status = $rsv['status'];
        $rsv['status_name'] = KsuCode::RSV_STATUS[$status];
        return $rsv;
    }

    public function nextCode()
    {
        $sql = sprintf("UPDATE %s SET id=0, y=YEAR(CURRENT_DATE) WHERE NOT y=YEAR(CURRENT_DATE)", $this->rsv_seq); 
        $this->db->query($sql);
        $sql = sprintf("UPDATE %s SET id=LAST_INSERT_ID(id + 1)", $this->rsv_seq);
        $this->db->query($sql);
        $sql = sprintf("SELECT LAST_INSERT_ID() as id, y FROM %s", $this->rsv_seq);
        $rs = $this->db->query($sql);
        if (!$rs) die('エラー: ' . $this->db->error);
        $row = $rs->fetch_assoc();
        if (!$row) die('エラー: 自動採番が失敗しました。');         
        return sprintf("%d%04d", $row['y'], $row['id']);
    }

    // $inst_id= 0 for all, or 1~ for one specific instrument 
    // $status=0 for all, or 1~ for one specific status
    function getNumRows($inst_id=0, $date1=null, $date2=null, $status=0)
    {
        $conn = $this->db; 
        $sql = "SELECT *  FROM %s WHERE 1 ";
        $sql = sprintf($sql, $this->table, $this->inst_table, $this->member_table, $this->member_table);
        if ($inst_id){  
            $sql .= " AND instrument_id=$inst_id"; 
        }
        if ($date1 and $date2){
            $sql .= " AND GREATEST(stime, '{$date1} 00:00') <= LEAST(etime, '{$date2} 23:59')"; 
        }elseif($date1 and !$date2){
            $sql .= " AND etime >= '{$date1}'";
        }
        if ($status > 0){ 
            $sql .= " AND status=$status"; 
        }
        // echo $sql;
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->num_rows;
    }
   
    function getListByInst($inst_id=0, $date1=null, $date2=null, $status=0, $page=0)
    {
        $conn = $this->db; 
        $sql = sprintf("SELECT * FROM %s WHERE 1 ", $this->rsv_view);
        if ($inst_id){ 
            $sql .= " AND instrument_id=$inst_id"; 
        }
        if ($date1 and $date2){
            $sql .= " AND GREATEST(stime, '{$date1} 00:00') <= LEAST(etime, '{$date2} 23:59')"; 
        }elseif($date1 and !$date2){
            $sql .= " AND etime>'{$date1}'";
        }
        if ($status > 0){ 
            $sql .= " AND status=$status"; 
        }
        $sql .= ' ORDER BY room_id, stime, etime';
        if ($page>0){
            $n = KsuCode::PAGE_ROWS;
            $sql .= sprintf(' LIMIT %d OFFSET %d', $n, ($page-1) * $n);
        }
        // echo $sql;
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC);
    }

    function getReport($inst_id=0, $date1=null, $date2=null, $status=0)
    {
        $report=[];
        $others=[];
        $total = ['student_n'=>0, 'staff_n'=>0, 'other_n'=>0];
        $conn = $this->db; 
        $sql = sprintf("SELECT * FROM %s WHERE 1 ", $this->rsv_report);
        if ($inst_id){ 
            $sql .= " AND instrument_id=$inst_id"; 
        }
        if ($date1 and $date2){
            $sql .= " AND GREATEST(stime, '{$date1} 00:00') <= LEAST(etime, '{$date2} 23:59')"; 
        }elseif($date1 and !$date2){
            $sql .= " AND etime>'{$date1}'";
        }
        if ($status > 0){ 
            $sql .= " AND status=$status"; 
        }
        $sql .= ' ORDER BY stime, etime';
        
        // echo $sql;
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        $rows = $rs->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $row){
            $date = Util::jpdate($row['stime'], false, false);//without year and time
            if (!isset($report[$date])){
                $report[$date] = $total;
            }
            if (!isset($others[$date])){
                $others[$date] = [];
            }
                   
            $student_n = $row['student_n'];
            $staff_n = $row['staff_n']; 
            $other_n = $row['other_num'];
            $other_user = $row['other_user'];
            if (strlen($other_user)>1){
                $others[$date] = array_merge($others[$date], [$other_user]);
            }
            foreach (array_keys($total) as $key){
                $report[$date][$key] += $$key;   
            }
            // printf('%s, %d, %d, %d, %s<br>',$date, $student_n, $staff_n, $other_n, $other_user);
        }
        ksort($report);
        return ['report'=>$report, 'other'=>$others];

    }
      
    function getItems($inst_id, $date1=null, $date2=null)
    {
        $rows = $this->getListByInst($inst_id, $date1, $date2);
        return self::toItems($rows);
    }

    static function toItems($rows)
    {
        $items = [];
        foreach ($rows as $row){
            $e = isset($row['status']) ? $row['status'] : 1;
            $items[] = [
              'id' => $row['id'],
              'group'=>$row['instrument_id'],
              'title'=>$row['purpose'] .'（'. KsuCode::RSV_STATUS[$e] . '）'. $row['master_name'],
              'className'=> isset(KsuCode::RSV_STYLE[$e]) ? KsuCode::RSV_STYLE[$e] : 'black', 
              'start'=> $row['stime'],
              'end'=> $row['etime'],
            ];
        }
        return $items;
    }
}
