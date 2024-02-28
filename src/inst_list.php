<?php
include 'models/Instrument.php';

$selected = 0;
$where = 'state=1';
$orderby = 'code';
if (isset($_GET['category'])){
  $where = 'category=' . $_GET['category'];
  $selected = $_GET['category'];
}
echo '<div class="text-left">'. PHP_EOL;
foreach (KsuCode::INST_CATEGORY as $c=>$label){
  $disable = ($c==$selected) ? 'disabled' : '';
  echo "<a href=\"?do=inst_list&category={$c}\" class=\"btn btn-outline-primary {$disable} m-1\">{$label}</a>" . PHP_EOL; 
} 
echo '</div>' . PHP_EOL;

$rows= (new Instrument)->getList($where, $orderby);
foreach($rows as $row){
  $url = 'img/instrument/'. $row['id'] .'.webp';
  if (!@GetImageSize($url)){// use dummy image for instrument w/o image
    $url = 'img/dummy-image-square1.webp' ; 
  }   
  echo '<div class="row border border-bottom-0 m-1">';
  echo '<div class="col-md-4 pl-0">';
  echo '<img src="' . $url . '" height="200px" width="280px" class="rounded">'. PHP_EOL;
  echo '</div>';
  echo '<div class="col-md-8">';
  echo '<h4 class="mt-0">'. $row['fullname'].'</h4>',
   '<div><span class="badge badge-hill badge-secondary">主な用途</span> ', $row['purpose'] , '</div>',
   '<div><span class="badge badge-hill badge-secondary">メーカー・型式</span> ',$row['maker'], ' ' ,$row['model'], '</div>',
   '<div class="small">',$row['detail'], '</div>',
   '<div class="align-self-end">',
   '<a class="btn btn-sm btn-outline-danger m-1" href="?do=inst_detail&id='.$row['id'].'">詳細</a>',
   '<a class="btn btn-sm btn-outline-success m-1" href="?do=rsv_input&inst='.$row['id'].'">予約</a>',
   '</div>';
  echo '</div>'. PHP_EOL;
  echo '<hr class="">';
  echo '</div>' . PHP_EOL;
}
