<?php
require_once('models/User.php');
if (isset($_POST['uid'], $_POST['pass'])){
    $uid = htmlspecialchars($_POST['uid']);
    $upass = htmlspecialchars($_POST['pass']);
    $row= (new User)->check($uid, $upass);


    if ($row){ // Login succeeded
        $_SESSION['uid']   = $row['uid'];
        $_SESSION['uname'] = $row['uname'];
        $_SESSION['urole'] = $row['urole'];
        // header('Location:index.php'); 
    }else{  // ldap authentication
        $row= (new User)->ldap_check($uid, $upass);
        if ($row){
            echo '<pre>'; print_r($row); echo '</pre>';
            // header('Location:index.php'); 
        }else{
            header('Location:?do=sys_login');
        }
    }
}else{
    echo '<p class="text-danger">ログインが失敗しました！</p>';
}

