<?php
namespace aic;

use aic\models\User;
use aic\models\Member;
use aic\views\Html;

if (isset($_POST['uid'], $_POST['pass'])){
    $uid = htmlspecialchars($_POST['uid']);
    $upass = htmlspecialchars($_POST['pass']);
    $login_time = null;
    $login_member = null;
    $new_user = false;

    $member= (new Member)->getList("uid='$uid'");
    if ($member){
        $login_member = $member[0];        
    }
    $row = (new User)->check($uid, $upass);
    if ($row) {
        $login_time = date('Y-m-d H:i:s'); 
        $_SESSION['uid'] = $uid;
        $_SESSION['urole'] = $row['urole'];
        $_SESSION['uname'] = $row['uname'];
    }else{
        $ldap_info = [];
        $ldap_info = (new User)->ldap_check($uid, $upass);
        if (!$ldap_info){ // something wrong, login again
            echo '<p class="text-danger">ログインが失敗しました！</p>';
            echo '<a class="btn btn-primary" href="?do=sys_login">戻る</a>';
        }else{
            $login_time = date('Y-m-d H:i:s'); 
            $_SESSION['uid'] = $ldap_info['uid'];
            $urole = 4;
            if ($ldap_info['category']=='一般学生') {
                $urole = 1;
            }
            if ($ldap_info['category']=='教育職員') {
                $urole = 2;
            }
            if ($ldap_info['category']=='事務職員') {
                $urole = 3;
            }
            $_SESSION['urole'] = $urole;
            $_SESSION['uname'] = $ldap_info['ja_name'];
            if (!$login_member){
                $new_user = true;
                $login_member = (new User)->addLdapUser($ldap_info);
                echo '<h3 class="text-info">新規ユーザでログイン成功しました！</h3>';
                echo '<a href="?do=aic_home" class="btn btn-primary m-1">先へ進む</a>';
                echo Html::toList($ldap_info, User::LDAP_NAMES); 
                echo '<p class="text-primary">上記の項目が会員情報として登録されました。</p>';
            }
        }
    }
    if ($login_time){
        (new User)->updateLoginTime($uid, $login_time);
    }
    if ($login_member){
        $_SESSION['sid'] = $login_member['sid'];
        $_SESSION['member_id'] = $login_member['id'];
        $_SESSION['member_name'] = $login_member['ja_name'];
        $_SESSION['member_category'] = $login_member['category'];
        $_SESSION['member_authority'] = $login_member['authority'];
        if (!$new_user) header('Location:?do=aic_home');
    }
    // TODO: 会員情報が存在しない場合の対応
}else{
    header('Location:?do=sys_login');
}