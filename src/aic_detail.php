<?php
namespace aic;

use aic\models\Instrument;
use aic\models\Reserve;
use aic\models\User;
use aic\models\KsuCode;
use aic\models\Util;

$inst_id = $_GET['id'];
// $date_curr = '240327';  //本番なら date("ymd");
$date_curr = date("ymd");
$selected_ymd = isset($_GET['d']) ? $_GET['d'] : $date_curr;
$_start = \DateTime::createFromFormat('ymd', $selected_ymd);
$date_start = $_start->format('Y-m-d');
$date_end = date("Y-m-d", strtotime("+1 days", strtotime($date_start)));
$jpdate = Util::jpdate($date_start);

/////// MODEL /////////////////////////////////
$items = (new Reserve)->getItems($inst_id, $date_start, $date_end);
$instrument = (new Instrument)->getDetail($inst_id);
$fname = $instrument['fullname'];
$code = $instrument['code'];
$groups = [['id'=>$inst_id, 'content'=>$fname]];

/////// VIEW /////////////////////////////////
$url = 'img/instrument/'. $inst_id .'.webp';
if (!@GetImageSize($url)){
  $url = 'img/dummy-image-square1.webp' ; 
}   
echo '<p><img src="'. $url .'" height="240px" width="320px" class="m-1 rounded"></p>' . PHP_EOL;
echo '<h3 class="">'. $fname.'</h3>' . PHP_EOL;
echo '<p>' .$instrument['detail'].'</p>' . PHP_EOL;
echo '<h4>' . $jpdate . $fname . ' の予約一覧</h4>' . PHP_EOL;
$rows =  (new Reserve)->getListByInst($inst_id, $date_start, $date_start);
if (count($rows) > 0){
  echo '<table class="table table-boxed">' . PHP_EOL;
  echo  '<tr><th width="20%">開始日時</th><th width="20%">終了日時</th>' . PHP_EOL;
  echo  '<th>責任者</th><th>目的</th><th>申請状態</th></tr>' . PHP_EOL;
  foreach ($rows as $row) {
    echo  '<tr>' . PHP_EOL;
    $e = $row['status'];
    echo  '<td>' . Util::jpdate($row['stime'], true) . '</td>';
    echo  '<td>' . Util::jpdate($row['etime'], true)  . '</td>';
    echo  '<td>' . $row['master_name'] . '</td>';
    echo  '<td>' . $row['purpose'] . '</td>';
    echo  '<td>' . KsuCode::RSV_STATUS[$e] . '</td>' . PHP_EOL;
    echo  '</tr>' . PHP_EOL;
  }
  echo  '</table>' . PHP_EOL;
}else{
  echo '<p class="text-info">本日は、まだ予約はありません</p>';
}

$navbar = ['-7'=>'1週間前','-1'=>'前の日', '+1'=>'次の日','+7'=>'1週間後'];
echo '<div class="text-left">'. PHP_EOL;
foreach ($navbar as $delta => $label){
  $ymd = date("ymd", strtotime($delta . " days", strtotime($date_start)));
  $link='<a href="?do=aic_detail&id=%d&d=%s" class="btn btn-outline-primary m-1">%s</a>' . PHP_EOL;
  printf($link, $inst_id, $ymd, $label);
} 
$can_reserve = (new User)->canReserve();
if (ENV=='development' or $can_reserve){
  $link = '<a href="?do=rsv_input&inst=%d&d=%s" class="btn btn-outline-info float-right m-1">予約する</a>' . PHP_EOL;
  printf($link, $inst_id, $selected_ymd);
}
echo '</div>' . PHP_EOL;
?>
<div id="visualization"></div>
<?php
echo '<div class="pb-2 m-2">' . PHP_EOL . 
 '<a href="?do=inst_list" class="btn btn-outline-info m-1">機器設備一覧へ</a>' . PHP_EOL .  
 '<a href="?do=aic_list" class="btn btn-outline-info m-1">空き状況一覧へ</a>' . PHP_EOL ; 
echo '</div>';
?>
<script type = "text/javascript">
  const items = <?=json_encode($items)?>;
  const groups = <?=json_encode($groups)?>;
  const start = "<?=$date_start.' 8:00'?>";
  const end = "<?=$date_start.' 23:59'?>";
  const step = 3; // step in hours for time-axis
  make_timeline('visualization', items, groups, start, end, step);   
</script>