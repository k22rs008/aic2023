<?php
// namespace ksu\aic;

require_once('models/User.php');
require_once('models/Member.php');
require_once('models/Staff.php');
include_once('views/Html.php');

if (isset($_POST['uid'], $_POST['pass'])){
    $uid = htmlspecialchars($_POST['uid']);
    $upass = htmlspecialchars($_POST['pass']);
    $row= (new User)->check($uid, $upass);
    $authed = false;
    $login_member = null;

    if ($row) {
        $authed = true;
        $_SESSION['uid'] = $uid;
        $_SESSION['urole'] = $row['urole'];
        $login_member= (new Member)->getList("uid='$uid'");
        if ($member){
            $login_member = $member[0];        
        }
    }
    $ldap_info = [];
    if (!$authed){ // try ldap authentication
        $ldap_info = (new User)->ldap_check($uid, $upass);
        if ($ldap_info) $authed = true;
    }
    if (!$authed){ // something wrong, login again
        echo '<p class="text-danger">ログインが失敗しました！</p>';
        echo '<a class="btn btn-primary" href="?do=sys_login">戻る</a>';
    }else if($ldap_info){
        echo '<h3 class="text-danger">新規ユーザ！</h3>';
        echo Html::toList($ldap_info); 
        echo '<h3 class="text-primary">上記の情報はシステムに登録されます。</h3>';
        $category = 4; // その他職員
        $urole = 0;
        if ($ldap_info['category']=='一般学生') {
            $category = $urole = 1;
        }
        if ($ldap_info['category']=='教育職員') {
            $category = $urole = 2;
        }
        if ($ldap_info['category']=='事務職員') {
            $category = $urole = 3;
        }
        $uid = $ldap_info['uid'];
        $sid = $ldap_info['sid'];
        $_SESSION['uid'] = $uid;
        $_SESSION['urole'] = $urole;
    
        $student = KsuCode::parseSid($sid);
        if ($student){
            $dept_code = $student['dept_code'];
            $dept_name = $student['dept_name'];
        }else{
            $dept_code = 'NA';
            $dept_name = $ldap_info['dept'];
        }
      
        $user = [
            'uid'=>$uid, 'uname'=>$ldap_info['ja_name'], 'urole'=>$urole,
            'last_login'=>date('Y-m-d H:i')
        ];
        (new User)->write($user);

        $member = [
            'id'=>0,
            'uid'=>$uid, 'sid'=>$sid,'email'=>$ldap_info['email'],
            'dept_code'=>$dept_code,'dept_name'=>$dept_name,
            'ja_name'=>$ldap_info['ja_name'],'ja_yomi'=>$ldap_info['ja_yomi'],
            'en_name'=>$ldap_info['en_name'],'en_yomi'=>$ldap_info['en_yomi'], 
            'category'=>$urole,
        ];
        $member_id = (new Member)->write($member);
        $login_member = (new Member)->getDetail($member_id);

        if ($urole!=1){
            $staff = [
                'id'=>0,
                'member_id'=>$member_id, 'title'=>$ldap_info['title'],'rank'=>$ldap_info['rank'],
            ];
            (new Staff)->write($staff);
        }
    }
    if ($login_member){
        $_SESSION['member_id'] = $login_member['id'];
        $_SESSION['member_name'] = $login_member['ja_name'];
        // print_r($_SESSION);
        header('Location:?do=aic_home');
    }else{
        echo '<p class="text-danger">ログインが失敗しました！</p>';
        echo '<a class="btn btn-primary" href="?do=sys_login">戻る</a>';
    }
}else{
    header('Location:?do=sys_login');
}

