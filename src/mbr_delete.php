<?php
require_once 'models/Member.php';
require_once 'models/Staff.php';

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