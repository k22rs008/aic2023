<?php
include 'models/facility.php';
$f_id = $_GET['id'];
$row= (new Facility)->getDetail($f_id);
if ($row) {  
    $url = 'img/facility/'. $row['code'] .'.webp';
    if (!@GetImageSize($url)){
        $url = 'img/dummy-image-square1.webp' ; 
    }   
    echo '<p><img src="'. $url . '" height="240" width="320" class="img-rounded"></p>';
    echo '<h3 class="bg-info">'. $row['fname'].'</h3>';
    echo '<table class="table table-hover">';
    echo '<tr><th width="20%">機器ID</th><td>' . $row['code'] . '</td></tr>';
    echo '<tr><th>機器名称</th><td>' . $row['fname']. '</td></tr>';
    echo '<tr><th>略称</th><td>' . $row['fshortname']. '</td></tr>';
    $i  = $row['status']; 
    $status= Facility::status; 
    echo '<tr><th>ユーザ種別</th><td>' . $status[$i]. '</td></tr>';
    $i  = $row['category']; 
    $category = Facility::category;
    echo '<tr><th>カテゴリ</th><td>' . $category[$i] . '</td></tr>';
    echo '<tr><th>メーカー</th><td>' . $row['maker'] . '</td></tr>';
    echo '<tr><th>型式</th><td>' . $row['model'] . '</td></tr>';
    echo '<tr><th>導入年月</th><td>' . $row['iyear'] . '</td></tr>';
    echo '<tr><th>詳細</th><td>' . $row['detail'] . '</td></tr>';
    echo '</table>';
    echo '<a class="btn btn-primary" href="?do=inst_edit&id='.$f_id.'">編集</a>';
    echo '<a href="?do=inst_list" class="btn btn-info">戻る</a>';  
}else{
    echo 'この機器は存在しません！';
}
