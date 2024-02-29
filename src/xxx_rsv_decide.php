<?php
namespace aic;

use aic\models\Reserve;

$id = $_POST['id'];
$decide = $_POST['judge'];
//print_r($decide);
if($decide=='承認'){
    $SQL = "UPDATE tbl_reserve_test SET decided=2, reserved=reserved
            WHERE tbl_reserve_test.id=$id";
    $rs = $conn->query($SQL);
    if(!$rs) die('エラー：' . $conn->error);
}else if($decide == '保留'){
    $SQL_keep = "UPDATE tbl_reserve_test SET decided=1, reserved=reserved
                 WHERE tbl_reserve_test.id=$id";
    $rsk = $conn->query($SQL_keep);
    if(!$rsk) die('エラー：' . $conn->error);
}

header('Location:?do=rsv_list');
