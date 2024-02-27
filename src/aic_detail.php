<?php
require_once('models/Instrument.php');
require_once('models/Reserve.php');
include ('lib/func.php');

$inst_id = $_GET['id'];
// $date_curr = '240327';  //本番なら date("ymd");
$date_curr = date("ymd");
$ymd = isset($_GET['d']) ? $_GET['d'] : $date_curr;
$_start = DateTime::createFromFormat('ymd', $ymd);
$date_start = $_start->format('Y-m-d');
$date_end = date("Y-m-d", strtotime("+1 days", strtotime($date_start)));
$jpdate = jpdate($date_start);

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
echo '<p><img src="'. $url .'" height="240px" width="320px" class="m-1 rounded"></p>';
echo '<h3 class="">'. $fname.'</h3>';
echo '<p>' .$instrument['detail'].'</p>';
echo '<h4>' . $jpdate . $fname . ' の予約一覧</h4>';
echo '<table class="table table-boxed">';
echo  '<tr><th width="20%">開始日時</th><th width="20%">終了日時</th>';
echo  '<th>責任者</th><th>目的</th><th>承認</th></tr>';
$rows =  (new Reserve)->getListByInst($inst_id, $date_start, $date_start);
foreach ($rows as $row) {
  echo  '<tr>';
  $e = $row['status'];
  echo  '<td>' . jpdate($row['stime'], true) . '</td>';
  echo  '<td>' . jpdate($row['etime'], true)  . '</td>';
  echo  '<td>' . $row['master_name'] . '</td>';
  echo  '<td>' . $row['purpose'] . '</td>';
  echo  '<td>' . KsuCode::RSV_STATUS[$e] . '</td>';
  echo  '</tr>';
}
echo  '</table>';

$navbar = ['-7'=>'1週間前','-1'=>'前の日', '+1'=>'次の日','+7'=>'1週間後'];
echo '<div class="text-left">'. PHP_EOL;
foreach ($navbar as $delta => $label){
  $ymd = date("ymd", strtotime($delta . " days", strtotime($date_start)));
  echo "<a href=\"?do=aic_detail&id={$inst_id}&d={$ymd}\" class=\"btn btn-outline-primary m-1\">{$label}</a>" . PHP_EOL; 
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
  const step = 1; // step in hours for time-axis
  make_timeline('visualization', items, groups, start, end, step);   
</script>