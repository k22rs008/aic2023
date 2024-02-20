<?php
include 'models/facility.php';
$status= Facility::status; 
$category = Facility::category;
$selected = 0;
$where = 'status=1';
$orderby = 'code';
if (isset($_GET['category'])){
  $where = 'category=' . $_GET['category'];
  $selected = $_GET['category'];
}

echo '<div class="text-left">'. PHP_EOL;
foreach ($category as $c=>$label){
  $disable = ($c==$selected) ? 'disabled' : '';
  echo "<a href=\"?do=inst_list&category={$c}\" class=\"btn btn-primary\" $disable>{$label}</a>" . PHP_EOL; 
} 
echo '</div>' . PHP_EOL;

$rows= (new Facility)->getList($where, $orderby);
echo '<table class="table table-hover">';
foreach($rows as $row) {
  $url = 'img/facility/'. $row['code'] .'.webp';
  if (!@GetImageSize($url)){
    $url = 'img/dummy-image-square1.webp' ; 
  }   
  echo '<tr><td width="30%"><img src="' . $url . '" height="240" width="320" class="img-rounded"></td>'. PHP_EOL;;
  echo '<td><h3 class="bg-infor mt-0">'. $row['fname'].'</h3>';
  echo '<div>主な用途: ', $row['purpose'] , '</div>';
  echo '<div>メーカー・型式: ',$row['maker'], ' ' ,$row['model'], '</div>';
  echo '<div>',$row['detail'], '</div>'; 
  echo '<div class="align-bottom"><a class="btn btn-primary" href="?do=inst_detail&id='.$row['id'].'">詳細</a></div>';  
  echo '</td></tr>'. PHP_EOL;;
}
echo '</table>' . PHP_EOL;