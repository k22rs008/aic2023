<?php
// namespace ksu;

use kst\KsuStudent;

require_once('Model.php');
include 'KsuStudent.php';

class User extends Model{
    protected $table = "tb_user";
    const urole = [1=>'学生', 5=>'教員', 9=>'管理者'];

    const LDAP_ENTRIES = [
        #LDAP ENTRY => New NAME
        'uid'=>'uid',//※ユーザID
        'sambasid'=>'sid',//※学籍番号・職員番号
        'mail'=>'email',//※メールアドレス
        'jadisplayname'=>'ja_name',//※日本語氏名
        'jasn' =>'ja_yomi',//※日本語読み
        'cn'=>'en_name',//※英語氏名
        'sn'=>'en_yomi',//※英語読み
        'jagivenname'=>'faculty', //所属学部。例、理工、芸術
        'jao'=>'dept',//所属学科。学生の場合。例、情報科学科
        'description'=>'category', //カテゴリ。学生の場合。 例：一般学生、
        'labeleduri'=>'rank', // 役職1。教職員の場合。例：教授、准教授
        'initials'=>'title', //役職2。教職員の場合。例：学部長、学科主任、大学教育職、その他職員
        'businesscategory'=>'category',//教職員の場合。例：教育職員、事務職員、業務特別契約職員
        'carlicense'=>'dept',//所属。教職員の場合。例：理工学部情報科学科、産学連携支援室
    ];
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
        if(!$ldap) return false;
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);        
        $ldap_bind = @ldap_bind($ldap, $dn, $passwd);
        if(!$ldap_bind)  return false;
        $target = 'k23gjk03'; // 他ユーザ情報の取得 
        // $target = $userid;// 本人認証 
        $filter = "uid={$target}";
        $result = ldap_search($ldap, $base, $filter);
        $record = [];
        if (ldap_count_entries($ldap, $result) > 0){
            $info = ldap_get_entries($ldap, $result);
            $info = $info[0];            
            foreach (self::LDAP_ENTRIES as $key=>$item){
                if (isset($info[$key])){
                    $record[$item]= $info[$key][0];
                } 
            }
        }
        // if (isset($record['sid'])){
        //     $detail = KsuStudent::parseSid($record['sid']);
        //     $record = array_merge($record, $detail);
        // }
        return $record;                
    }
}