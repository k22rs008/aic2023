<?php
require_once('db_config.php');
include_once('sys_ldap.php');
if (isset($_POST['uid'], $_POST['pass'])){
    $uid = htmlspecialchars($_POST['uid']);
    $upass = htmlspecialchars($_POST['pass']);
    // authentication by querying local database
    $sql = "SELECT * FROM tbl_user WHERE uid= '{$uid}'  AND upass='{$uppass}'";
    $rs = $conn->query($sql);
    if (!$rs) die('エラー: ' . $conn->error);
    $row= $rs->fetch_assoc();
    if ($row){ //Login succeeded
        $_SESSION['uid']   = $row['uid'];
        $_SESSION['uname'] = $row['uname'];
        $_SESSION['urole'] = $row['urole'];
        header('Location:index.php'); 
    }else{  // ldap authentication
        //$ldap_info = kind_ldap($uid, $upass);
        $ldap_info = [];
        if ($ldap_info){  
            /**
             * 'uid'(ログインID)、'en_name'(英語名), 'ja_name'（漢字氏名）、
             * 'ja_yomi'（日本語読み）、'email'(メールアドレス)、'dept'（学部・学科名)
             * 'category'（大学教職員、学生）、'subcategory'(教育職員) 
             *
             * TODO: identify student/staff in database based on uid & email
             *  For students: 
             *      uid pattern: "k\d{2}[a-z]{2}[0-9a-z]\d{2}", 
             *      email pattern  "@st.kyusan-u.ac.jp"
             */
        } 
        header('Location:?do=sys_login');
    }
}

