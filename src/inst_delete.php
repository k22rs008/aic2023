<?php
include 'models/Instrument.php';
if (isset($_GET['id'])){
    $inst_id = $_GET['id'];
    (new Instrument)->delete($inst_id);
    header('Location:?do=inst_list');
}else{
    echo '<h3 class="text-danger">IDが指定されていないため、削除できません！</h3>';
}