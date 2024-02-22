<?php
include 'models/Facility.php';
// $f_status= Facility::status; 
$f_category = Facility::category;
$selected = 0;
$where = 'status=1';
$orderby = 'code';
if (isset($_GET['category'])){
  $where = 'category=' . $_GET['category'];
  $selected = $_GET['category'];
}
echo '<div class="text-left">'. PHP_EOL;
foreach ($f_category as $c=>$label){
  $disable = ($c==$selected) ? 'disabled' : '';
  echo "<a href=\"?do=inst_list&category={$c}\" class=\"btn btn-outline-primary {$disable} m-1\">{$label}</a>" . PHP_EOL; 
} 
echo '</div>' . PHP_EOL;

$rows= (new Facility)->getList($where, $orderby);
echo '<table class="table table-hover">';
foreach($rows as $row) {
  $url = 'img/facility/'. $row['id'] .'.webp';
  if (!@GetImageSize($url)){
    $url = 'img/dummy-image-square1.webp' ; 
  }   
  echo '<tr><td width="30%">',
   '<img src="' . $url . '" height="240px" width="320px" class="img-rounded"></td>'. PHP_EOL;
  echo '<td><div style="height:240px;">',
   '<h3 class="bg-infor mt-0">'. $row['fname'].'</h3>',
   '<div><span class="badge badge-hill badge-secondary">主な用途</span> ', $row['purpose'] , '</div>',
   '<div><span class="badge badge-hill badge-secondary">メーカー・型式</span> ',$row['maker'], ' ' ,$row['model'], '</div>',
   '<div class="h-50">',$row['detail'], '</div>',
   '<div class="align-self-end">',
   '<a class="btn btn-sm btn-outline-danger m-1" href="?do=inst_detail&id='.$row['id'].'">詳細</a>',
   '<a class="btn btn-sm btn-outline-success m-1" href="?do=aic_detail&id='.$row['id'].'">予約</a>',
   '</div></div>';
  echo '</td></tr>'. PHP_EOL;;
}
echo '</table>' . PHP_EOL;
?>