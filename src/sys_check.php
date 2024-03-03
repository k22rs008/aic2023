<?php
namespace aic;

use aic\models\User;
use aic\models\Member;
use aic\models\Staff;

use aic\views\Html;

if (isset($_POST['uid'], $_POST['pass'])){
    $uid = htmlspecialchars($_POST['uid']);
    $upass = htmlspecialchars($_POST['pass']);
    $login_member = null;
    $new_user = false;

    $row = (new User)->check($uid, $upass);
    if ($row) {
        $_SESSION['uid'] = $uid;
        $_SESSION['urole'] = $row['urole'];
        $_SESSION['uname'] = $row['uname'];
        $member= (new Member)->getList("uid='$uid'");
        if ($member){
            $login_member = $member[0];        
        }
    }else{
        $ldap_info = [];
        $ldap_info = (new User)->ldap_check($uid, $upass);
        if (!$ldap_info){ // something wrong, login again
            echo '<p class="text-danger">ログインが失敗しました！</p>';
            echo '<a class="btn btn-primary" href="?do=sys_login">戻る</a>';
        }else{
            $login_member= (new Member)->getList("uid='$uid'");
            $_SESSION['uid'] = $ldap_info['uid'];
            $_SESSION['urole'] = $ldap_info['sid'];
            if (!$login_member){
                $new_user = true;
                $login_member = (new User)->addLdapUser($ldap_info);
                echo '<h3 class="text-info">新規ユーザでログイン成功しました！</h3>';
                echo Html::toList($ldap_info, User::LDAP_NAMES); 
                echo '<p class="text-primary">上記のアカウントがシステムに登録されました。</p>';
                echo '<a href="?do=aic_home" class="btn btn-primary">続く</a>';
            }
        }
    }
    if ($login_member){
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