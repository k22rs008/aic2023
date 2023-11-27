<?php
require_once('db_config.php');
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
        header('Location:?do=sys_login');
    }
}

