<?php
namespace aic;

use aic\models\Reserve;
use aic\models\Security;

(new Security)->require('admin');

$id = $_GET['id'];
$rsv = (new Reserve)->getDetail($id);
$status = ($rsv['process_status']== 1 or $rsv['process_status']==3) ? 2 : 3;
$data = ['id'=>$id, 'process_status'=>$status, 'approved'=>date('Y-m-d H:i:s')];
(new Reserve)->write($data);

header('Location: ' . $_SERVER['HTTP_REFERER']);
