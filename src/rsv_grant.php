<?php
namespace aic;

use aic\models\Reserve;
use aic\models\User;

$is_admin = (new User)->isAdmin();
if (ENV=='deployment' and !$is_admin){
    die('<p class="text-danger">この機能は管理者以外利用できません。</p>');
}

$id = $_GET['id'];
$rsv = (new Reserve)->getDetail($id);
$status = ($rsv['status']== 1 or $rsv['status']==3) ? 2 : 3;
$data = ['id'=>$id, 'status'=>$status, 'approved'=>date('Y-m-d H:i')];
(new Reserve)->write($data);
// header('Location:?do=rsv_detail&id=' . $id);
header('Location: ' . $_SERVER['HTTP_REFERER']);
