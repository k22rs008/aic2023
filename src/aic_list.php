<?php
namespace aic;

use aic\models\Instrument;
use aic\models\Reserve;

// $date_curr = '240327';  //本番なら date("ymd");
$date_curr = date("ymd");
$ymd = isset($_GET['d']) ? $_GET['d'] : $date_curr;

$start = \DateTime::createFromFormat('ymd', $ymd);
$date_start = $start->format('Y-m-d');
$date_end = date("Y-m-d", strtotime("+1 days", strtotime($date_start)));

////// MODEL /////////////////
$items =  (new Reserve)->getItems(0, $date_start, $date_start); //0 means all items 

$groups = [];
$rows = (new Instrument)->getList();
foreach ($rows as $row){
  $inst_id = $row['id'];
  $link = '<a class="btn btn-info" href="%s?do=aic_detail&id=%d&d=%s">%s</a>'. PHP_EOL;
  $content = sprintf($link, $_SERVER['PHP_SELF'], $inst_id, $ymd, $row['fullname']);
  $groups[] = ['id'=>$inst_id, 'content'=>$content];
}

////// VIEW ///////////////
$navbar = ['-7'=>'1週間前','-1'=>'前の日', '+1'=>'次の日','+7'=>'1週間後'];
echo '<div class="text-left">'. PHP_EOL;
foreach ($navbar as $delta => $label){
  $ymd = date("ymd", strtotime($delta . " days", strtotime($date_start)));
  $link='<a href="?do=aic_list&d=%s" class="btn btn-outline-primary m-1">%s</a>' . PHP_EOL;
  printf($link, $ymd, $label); 
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