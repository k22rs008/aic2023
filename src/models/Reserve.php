<?php
// namespace ksu/aic;

require_once('Model.php');
require_once('Member.php');
require_once('RsvSample.php');
require_once('RsvMember.php');
require_once('lib/func.php');

class Reserve extends Model{
    protected $table = "tb_reserve";
    protected $inst_table = 'tb_instrument';
    protected $member_table = 'tb_member';
    
    function getDetail($id)
    {
        $rsv = parent::getDetail($id);        
        if (!$rsv){ // return an empty blank  
            $filefds = $this->getFileds();
            foreach ($filefds as $f){
                $key = $f['Field'];
                $rsv[$key] = '';
            }
            $rsv['id'] = 0;
            $rsv['apply_mid'] = 1; //$_SESSION['member_id'];
            $rsv['xray_chk'] = 0;
            $rsv['apply_member'] = (new Member)->getDetail($rsv['apply_mid']);
            $rsv['rsv_member'] = $rsv['sample_natures'] = [];  
            $rsv['sample_other']='';     
            $rsv['stime'] = $rsv['etime'] = date('Y-m-d H:i');
            return $rsv;
        }

        $instrument = (new Instrument)->getDetail($rsv['instrument_id']); 
        $rsv['instrument_name'] = $instrument['fullname'];
        $rsv['apply_member'] = (new Member)->getDetail($rsv['apply_mid']);
        $rsv['master_member'] = (new Member)->getDetail($rsv['master_mid']);
        $_dept_code = $rsv['master_member']['dept_code'];
        $rsv['dept_name'] = KsuCode::FACULTY_DEPT[$_dept_code];

        $rsv['rsv_member'] = (new RsvMember)->getList('reserve_id='.$id);
        $students = array_filter($rsv['rsv_member'], function($a){ return $a['category']==1; });
        $rsv['student_n'] = count($students);
        $rsv['staff_n'] = count($rsv['rsv_member'])- count($students); 

        $rsv['sample_state_value'] = KsuCode::SAMPLE_STATE[$rsv['sample_state']];
        $rsv['xray_chk_value'] = KsuCode::YESNO[$rsv['xray_chk']]; 

        $samples = (new RsvSample)->getList('reserve_id='.$id);
        $selected = [];
        $other = '';
        foreach ($samples as $sample){
            $selected[] = $sample['nature'];
            if ($sample['nature']==4) $other= $sample['other'];
        }
        $rsv['sample_other'] = $other;
        $rsv['sample_natures'] = $selected;
        $_natures = array_slice_by_index(KsuCode::SAMPLE_NATURE, $selected);
        $rsv['sample_nature_value'] = implode(', ', $_natures);
        return $rsv;
    }

    function getListDetail($inst_id=0, $status=9)
    {
        global $conn;        

        $sql = "SELECT r.*, f.fullname, f.shortname,m1.ja_name AS apply_name, m2.ja_name AS master_name
          FROM %s r, %s f, %s m1, %s m2 WHERE r.apply_mid=m1.id AND r.master_mid=m2.id AND f.id=r.instrument_id ";
        $sql = sprintf($sql, $this->table, $this->inst_table, $this->member_table, $this->member_table);
        if ($inst_id){ // $inst_id= 0 for all, or 1~ for one specific instrument 
            $sql .= " AND r.instrument_id=$inst_id"; 
        }
        if ($status < 9){ // $status=9 for all, or 1~ for one specific status
            $sql .= " AND r.status=$status"; 
        }
        $sql .= ' ORDER BY instrument_id, stime, etime';
        
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC);
    }
    /**
     * $inst_id: int, , instrument id, 0 for all instrument
     */
    function getListByInst($inst_id=0, $date1=null, $date2=null)
    {
        global $conn;
        $sql = "SELECT r.*, m.ja_name as master_name FROM %s r, %s m WHERE r.master_mid=m.id";
        $sql = sprintf($sql, $this->table, $this->member_table);
        if ($inst_id){
            $sql .= " AND r.instrument_id=$inst_id"; 
        }
        if ($date1 and $date2){
            $sql .= " AND GREATEST(stime, '{$date1} 00:00') <= LEAST(etime, '{$date2} 23:59')"; 
        }elseif($date1 and !$date2){
            $sql .= " AND etime > '{$date1}'";
        }

        $sql .= ' ORDER BY instrument_id, stime, etime';
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC);
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
            $e = $row['status'];
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
