<?php
require_once('models/Instrument.php');
require_once('models/Reserve.php');

// $date_curr = '240327';  //本番なら date("ymd");
$date_curr = date("ymd");
$ymd = isset($_GET['d']) ? $_GET['d'] : $date_curr;
$start = DateTime::createFromFormat('ymd', $ymd);
$date_start = $start->format('Y-m-d');
$date_end = date("Y-m-d", strtotime("+1 days", strtotime($date_start)));

////// MODEL /////////////////
$items =  (new Reserve)->getItems(0, $date_start, $date_start); //0 means all items 

$groups = [];
$rows = (new Instrument)->getList();
foreach ($rows as $row){
  $fid = $row['id'];
  $fmt = '<a class="btn btn-info" href="%s?do=aic_detail&id=%d&d=%s">%s</a>';
  $content = sprintf($fmt, $_SERVER['PHP_SELF'], $fid, $ymd, $row['fullname']);
  $groups[] = ['id'=>$fid, 'content'=>$content];
}

////// VIEW ///////////////
$navbar = ['-7'=>'1週間前','-1'=>'前の日', '+1'=>'次の日','+7'=>'1週間後'];
echo '<div class="text-left">'. PHP_EOL;
foreach ($navbar as $delta => $label){
  $ymd = date("ymd", strtotime($delta . " days", strtotime($date_start)));
  echo "<a href=\"?do=aic_list&d={$ymd}\" class=\"btn btn-outline-primary m-1\">{$label}</a>" . PHP_EOL; 
} 
echo '</div>' . PHP_EOL;
?>
<div id="visualization"></div>
<script type = "text/javascript">
  const items = <?=json_encode($items)?>;
  const groups = <?=json_encode($groups)?>;
  const start = "<?=$date_start.' 8:00'?>";
  const end = "<?=$date_start.' 23:59'?>";
  const step = 1; // step in hours for time-axis
  make_timeline('visualization', items, groups, start, end, step);   
</script>