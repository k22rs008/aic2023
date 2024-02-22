<?php
include 'models/Facility.php';
if (isset($_GET['id'])){
    $f_id = $_GET['id'];
    (new Facility)->delete($f_id);
    header('Location:?do=inst_list');
}else{
    echo '<h3 class="text-danger">IDが指定されていないため、削除できません！</h3>';
}