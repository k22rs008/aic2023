<?php
namespace aic;

use aic\models\Member;
use aic\models\Staff;
use aic\models\User;
// use aic\models\Util;

$is_admin = (new User)->isAdmin();
if (ENV=='deployment' and !$is_admin){
    die('<p class="text-danger">この機能は管理者以外利用できません。</p>');
}

if (isset($_GET['id'])){
    $mbr_id = $_GET['id'];
    (new Member)->delete($mbr_id);
    $staff = (new Staff)->getList('member_id='.$mbr_id);
    if (count($staff)>0){
        $staff_id = $staff[0]['id'];
        (new Staff)->delete($staff_id);
    }
    header('Location:?do=mbr_list');
}else{
    echo '<h3 class="text-danger">IDが指定されていないため、削除できません！</h3>';
}