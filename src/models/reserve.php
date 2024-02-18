<?php
require_once('db_config.php');
require_once('Model.php');

class Reserve extends Model{
    protected $table = "tbl_reserve_test";

    function getAll($date1=null, $date2=null)
    {
        return $this->getListByFid(0, $date1, $date2);
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
}