<?php
namespace aic\models;

use aic\models\Member;
use aic\models\RsvSample;
use aic\models\RsvMember;
use aic\models\Util;

class Reserve extends Model {
    protected $table = "tb_reserve";
    protected $rsv_view = "vw_reserve";
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
            $rsv['apply_mid'] = 1; //$_SESSION['member_id'];
            $rsv['xray_chk'] = 0;
            $rsv['apply_member'] = (new Member)->getDetail($rsv['apply_mid']);
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

     // $inst_id= 0 for all, or 1~ for one specific instrument 
    // $status=9 for all, or 1~ for one specific status
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
        $sql .= ' ORDER BY instrument_id, stime, etime';
        if ($page>0){
            $n = KsuCode::PAGE_ROWS;
            $sql .= sprintf(' LIMIT %d OFFSET %d', $n, ($page-1) * $n);
        }
        // echo $sql;
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC);
    }

    function getReport($inst_id=0, $date1=null, $date2=null)
    {
        $report=[];
        $rows = (new Instrument)->getList(1, 'code');
        foreach ($rows as $row){
            $id = $row['id'];
            $room_id = $row['room_id'];
            $room = (new Room)->getDetail($room_id);
            $report[$id] = ['rsv'=>[], 'room_no'=>$room['room_no'], 'shortname'=>$row['shortname']];
        }
        $_date1 = new \DateTimeImmutable($date1);
        $_date2 = new \DateTimeImmutable($date2);
        $interval = \DateInterval::createFromDateString('1 day');
        $daterange = new \DatePeriod($_date1, $interval ,$_date2);
        $data = [];
        foreach($daterange as $date){
            $d = $date->format('Y/m/d');
            $w = $date->format('w');
            $data[$d]=['weekday'=>KsuCode::WEEKDAY[$w]];
        }

        $rows = $this->getListByInst($inst_id, $date1, $date2);
        foreach ($rows as $row){
            $id = $row['instrument_id'];
            $_stime = new \DateTimeImmutable($row['stime']);
            $_etime = new \DateTimeImmutable($row['etime']);
            $d = $_stime->format('Y/m/d');
            //TODO: combine all data
        }

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
