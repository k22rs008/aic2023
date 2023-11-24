<?php
require_once('db_config.php');
if (isset($_POST['uid'], $_POST['pass'])){
    $u = htmlspecialchars($_POST['uid']);
    $p = htmlspecialchars($_POST['pass']);
    // TODO: authentication by querying KSU ldap service
    
    // authentication by querying local database
    $sql = "SELECT * FROM tbl_user WHERE uid= '{$u}'  AND upass='{$p}'";
    $rs = $conn->query($sql);
    if (!$rs) die('エラー: ' . $conn->error);
    $row= $rs->fetch_assoc();
    if ($row){ //Login succeeded
    $_SESSION['uid']   = $row['uid'];
    $_SESSION['uname'] = $row['uname'];
    $_SESSION['urole'] = $row['urole'];
    header('Location:index.php');   
    }else{
        header('Location:?do=sys_login');   
    }
}

