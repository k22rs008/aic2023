<?php
// namespace ksu/aic;

require_once('Model.php');

class Reserve extends Model{
    protected $table = "tb_reserve";
    private $user_table = 'tb_user';
    private $facility_table = 'tb_instrument';
    const status = [1=>'申請中', 2=>'審査中', 3=>'承認済', 9=>'拒否'];
    const style = [1=>'red', 2=>'green', 3=>'blue', 9=>'black'];
    const sample_property = [1=>'爆発性',2=>'毒性',3=>'腐食性', 9=>'その他'];
    const sample_state = [1=>'気体',2=>'液体',3=>'固体'];
    
    function getDetail($id)
    {
        $rsv = parent::getDetail($id);
        if ($rsv) return false;
        $apply_user = (new User)->getDetail($rsv['uid']);
        $master_user = (new Staff)->getDetail($rsv['master_user']);
    }

    function getListDetail($fid=0, $status=9)
    {
        global $conn;        

        $sql = "SELECT r.*, f.fname, f.fshortname,u1.uname AS apply_uname, u2.uname AS master_uname
          FROM %s r, %s f, %s u1, %s u2 WHERE r.uid=u1.uid AND r.master_user=u2.uid AND f.id=r.facility_id ";
        $sql = sprintf($sql, $this->table, $this->facility_table, $this->user_table, $this->user_table);
        if ($fid){ // $fid= 0 for all, or 1~ for one specific ficility 
            $sql .= " AND r.facility_id=$fid"; 
        }
        if ($status < 9){ // $status=9 for all, or 1~ for one specific status
            $sql .= " AND r.status=$status"; 
        }
        $sql .= ' ORDER BY facility_id, stime, etime';
        
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC);
    }
    /**
     * $fid: int, , facility id, 0 for all facility
     */
    function getListByFid($fid=0, $date1=null, $date2=null)
    {
        global $conn;
        $sql = "SELECT r.*, u.uname FROM %s r, %s u WHERE r.master_user=u.uid";
        $sql = sprintf($sql, $this->table, $this->user_table);
        if ($fid){
            $sql .= " AND r.facility_id=$fid"; 
        }
        if ($date1 and $date2){
            $sql .= " AND GREATEST(stime, '{$date1} 00:00') <= LEAST(etime, '{$date2} 23:59')"; 
        }elseif($date1 and !$date2){
            $sql .= " AND etime > '{$date1}'";
        }

        $sql .= ' ORDER BY facility_id, stime, etime';
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC);
    }
  
    function getItems($fid, $date1=null, $date2=null)
    {
        $rows = $this->getListByFid($fid, $date1, $date2);
        return self::toItems($rows);
    }

    static function toItems($rows)
    {
        $items = [];
        foreach ($rows as $row){
            $e = $row['status'];
            $items[] = [
              'id' => $row['id'],
              'group'=>$row['facility_id'],
              'title'=>$row['purpose'] .'（'. self::status[$e] . '）'. $row['uname'],
              'className'=> isset(self::style[$e]) ? self::style[$e] : 'black', 
              'start'=> $row['stime'],
              'end'=> $row['etime'],
            ];
        }
        return $items;
    }

}