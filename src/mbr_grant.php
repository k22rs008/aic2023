<?php
namespace aic;

use aic\models\Member;
use aic\models\Security;

(new Security)->require('admin');

$id = $_GET['id'];
$member = (new Member)->getDetail($id);
$authority = $member['authority']== 0 ? 1 : 0;
$data=['id'=>$id, 'authority'=>$authority, 'granted'=>date('Y-m-d H:i')];
(new Member)->write($data);
header('Location:?do=mbr_detail&id=' . $id);
