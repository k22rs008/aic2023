<?php
require_once('db_config.php');
require_once('Model.php');

class User extends Model{
    protected $table = "tbl_user";
    function getDetail($id)
    {
        global $conn;
        $sql = sprintf("SELECT * FROM %s WHERE uid='{$id}'", self::$table);
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_assoc(); 
    }

    function getList($where=1, $orderby='uid')
    {
        return parent::getList($where, $orderby);
    }

    function check($uid, $upass)
    {
        global $conn;
        $uid = htmlspecialchars($uid);
        $upass = htmlspecialchars($upass);
        $sql = "SELECT * FROM tbl_user WHERE uid= '{$uid}'  AND upass='{$upass}'";
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        $row = $rs->fetch_assoc();
        // if ($row) return $row; else return ldap_check()
        return $row;
    }
}