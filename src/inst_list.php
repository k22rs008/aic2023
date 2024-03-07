<?php
namespace aic;

use aic\models\Instrument;
use aic\models\User;
use aic\models\Room;
use aic\models\KsuCode;

$selected = 0;
$where = '1';
$orderby = 'room_id';
if (isset($_GET['c'])){
  $where = 'category=' . $_GET['c'];
  $selected = $_GET['c'];
}
echo '<div class="text-left">'. PHP_EOL;
foreach (KsuCode::INST_CATEGORY as $c=>$label){
  $disabled = ($c==$selected) ? 'disabled' : '';
  $link = '<a href="?do=inst_list&c=%d" class="btn btn-outline-primary %s m-1">%s</a>' . PHP_EOL;
  printf($link,  $c, $disabled, $label);
} 
echo '</div>' . PHP_EOL;
// order by recently/frequently used
// $member_id = (new User)->getLoginMemberId();
// if ($member_id){
//   $rows= (new Instrument)->getListRFU($member_id, 'mfru', $where);
// }else{
//   $rows= (new Instrument)->getList($where, $orderby);
// }
$rows= (new Instrument)->getList($where, $orderby);
foreach($rows as $row){
  $url = 'img/instrument/'. $row['id'] .'.webp';
  if (!@GetImageSize($url)){// use dummy image for instrument w/o image
    $url = 'img/dummy-image-square1.webp' ; 
  }   
  $room = (new Room)->getDetail($row['room_id']);
  echo '<div class="row border border-bottom-0 m-1">';
  echo '<div class="col-md-4 pl-0">';
  echo '<img src="' . $url . '" height="200px" width="280px" class="rounded">'. PHP_EOL;
  echo '</div>';
  echo '<div class="col-md-8">';
  echo '<h4 class="mt-0">'. $row['fullname'].'</h4>',
   '<div><span class="badge badge-hill badge-secondary">主な用途</span> ', $row['purpose'] , '</div>',
   '<div><span class="badge badge-hill badge-secondary">メーカー・型式</span> ',$row['maker'], ' ' ,$row['model'], '</div>',
   '<div><span class="badge badge-hill badge-secondary">設置場所</span> 〔',$room['room_no'] , '〕', $room['room_name'],'</div>',
   '<div class="small">',$row['detail'], '</div>',
   '<div class="align-self-end">',
   '<a class="btn btn-sm btn-outline-danger m-1" href="?do=inst_detail&id='.$row['id'].'">詳細</a>';
  echo '<a class="btn btn-sm btn-outline-danger m-1" href="?do=aic_detail&id='.$row['id'].'">空き状況</a>'; 
  $can_reserve = (new User)->canReserve();
  if ($can_reserve)
  echo '<a class="btn btn-sm btn-outline-success m-1" href="?do=rsv_input&inst='.$row['id'].'">予約</a>';
  echo '</div>';
  echo '</div>'. PHP_EOL;
  echo '<hr class="">';
  echo '</div>' . PHP_EOL;
}
