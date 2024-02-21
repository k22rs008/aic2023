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

    function check($userid, $passwd)
    {
        global $conn;
        $userid = htmlspecialchars($userid);
        $passwd = htmlspecialchars($passwd);
        $sql = "SELECT * FROM tbl_user WHERE uid= '{$userid}'  AND upass='{$passwd}'";
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        $row = $rs->fetch_assoc();
        // if ($row) return $row; else return ldap_check()
        return $row;
    }
    function ldap_check($userid, $passwd)
    {
        $host = "ldap1.ip.kyusan-u.ac.jp";
        $base = "ou=userall,dc=kyusan-u,dc=ac,dc=jp";
        $dn = "uid=" . $userid . "," . $base;
        $ldap = ldap_connect($host);
        if(!$ldap) {echo 'connect failed!'; return false;}
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);        
        $ldap_bind = @ldap_bind($ldap, $dn, $passwd);
        if(!$ldap_bind) {echo 'bind failed!'; return false;}
        $target = 'k21rs034'; // ユーザ情報の取得 
        // $target = $userid;// 本人認証 
        $filter = "uid={$target}";
        $result = ldap_search($ldap, $base, $filter);
        if (ldap_count_entries($ldap, $result) == 0){
            echo 'no entries'; return false;
        }
        $info = ldap_get_entries($ldap, $result);
        /*
        mail;
        jao
        */
        return $info;        
    }
}