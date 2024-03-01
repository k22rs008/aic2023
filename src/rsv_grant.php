<?php
namespace aic;

use aic\models\Reserve;

$id = $_GET['id'];
$rsv = (new Reserve)->getDetail($id);
$status = ($rsv['status']== 1 or $rsv['status']==3) ? 2 : 3;
$data = ['id'=>$id, 'status'=>$status, 'approved'=>date('Y-m-d H:i')];
(new Reserve)->write($data);
// header('Location:?do=rsv_detail&id=' . $id);
header('Location: ' . $_SERVER['HTTP_REFERER']);
