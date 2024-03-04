<?php
namespace aic;

use aic\models\Staff;
use aic\models\Security;

(new Security)->require('admin');

$id = $_GET['id'];
$record = (new Staff)->getDetail($id);
$mbr_id = $record['member_id'];
$responsible = $record['responsible']== 0 ? 1 : 0;
$data=['id'=>$id, 'responsible'=>$responsible];
(new Staff)->write($data);
header('Location:?do=stf_detail&id=' . $mbr_id);
