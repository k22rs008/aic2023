<?php
require_once('db_config.php');
require_once('Model.php');

class Reserve extends Model{
    protected $table = "tbl_reserve_test";
    const status = [0=>'申請中', 1=>'審査中', 2=>'承認済', 9=>'拒否'];
    const class = [0=>'red', 1=>'green', 2=>'blue', 9=>'black'];


    function getAll($date1=null, $date2=null)
    {
        return $this->getListByFid(0, $date1, $date2);
    }

    function getAllItems($date1=null, $date2=null)
    {
        $rows = $this->getListByFid(0, $date1, $date2);
        return self::toItems($rows);
    }

    function getItems($fid, $date1=null, $date2=null)
    {
        $rows = $this->getListByFid($fid, $date1, $date2);
        return self::toItems($rows);
    }

    function getListByFid($fid, $date1=null, $date2=null)
    {
        global $conn;
        $sql = "SELECT r.*, u.uname FROM tbl_reserve_test r, tbl_user u WHERE r.master_user=u.uid";
        if ($fid){
            $sql .= " AND r.facility_id=$fid"; 
        }
        if ($date1 and $date2){
            $sql .= " AND GREATEST(stime, '{$date1}') <= LEAST(etime, '{$date2}')"; 
        }elseif($date1 and !$date2){
            $sql .= " AND etime > '{$date1}'";
        }
        $sql .= ' ORDER BY facility_id, stime, etime';
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC);
    }

    function getListByDate($fid, $date1=null, $date2=null)
    {
        $rows = $this->getListByFid($fid, $date1, $date2);
    }
    
    static function toItems($rows)
    {
        $items = [];
        foreach ($rows as $row){
            $e = $row['decided'];
            $items[] = [
              'id' => $row['id'],
              'group'=>$row['facility_id'],
              'title'=>$row['purpose'] .'（'. self::status[$e] . '）'. $row['uname'],
              'className'=> isset($class[$e]) ? self::class[$e] : 'black', 
              'start'=> $row['stime'],
              'end'=> $row['etime'],
            ];
        }
        return $items;
    }

}