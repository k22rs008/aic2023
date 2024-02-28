<?php
include 'models/Member.php';

$id = $_GET['id'];
$member = (new Member)->getDetail($id);
$authority = $member['authority']== 0 ? 1 : 0;
$data=['id'=>$id, 'authority'=>$authority, 'granted'=>date('Y-m-d H:i')];
(new Member)->write($data);
header('Location:?do=mbr_detail&id=' . $id);
