<?php
// namespace ksu/aic;

require_once('Model.php');
require_once('Member.php');
require_once('RsvSample.php');
require_once('RsvMember.php');

class Reserve extends Model{
    protected $table = "tb_reserve";
    protected $inst_table = 'tb_instrument';
    protected $member_table = 'tb_member';

    const status = [1=>'申請中', 2=>'審査中', 3=>'承認済', 9=>'拒否'];
    const style = [1=>'red', 2=>'green', 3=>'blue', 9=>'black'];
    
    function getDetail($id)
    {
        $rsv = parent::getDetail($id);
        if (!$rsv) return null;
        $reserve_id = $rsv['id'];
        $instrument = (new Instrument)->getDetail($rsv['instrument_id']); 
        $apply_user = (new Member)->getDetail($rsv['apply_mid']);
        $master_user = (new Member)->getDetail($rsv['master_mid']);
        $rsv['instrument_name'] = $instrument['fullname'];
        $rsv['apply_uname'] = $apply_user['ja_name'];
        $rsv['master_uname'] = $master_user['ja_name'];
        $rsv_members = (new RsvMember)->getList('reserve_id='.$reserve_id);
        $rsv_samples = (new RsvSample)->getList('reserve_id='.$reserve_id);
        $rsv['rsv_sample'] = $rsv_samples;
        $rsv['rsv_member'] = $rsv_members;
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
              'title'=>$row['purpose'] .'（'. self::status[$e] . '）'. $row['master_name'],
              'className'=> isset(self::style[$e]) ? self::style[$e] : 'black', 
              'start'=> $row['stime'],
              'end'=> $row['etime'],
            ];
        }
        return $items;
    }

}
