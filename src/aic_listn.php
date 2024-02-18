<?php
require_once('models/facility.php');
require_once('models/reserve.php');

$date_curr = '2023-11-27';  //本番なら date("Y-m-d");
$date_start = isset($_GET['date']) ? $_GET['date'] : $date_curr;
$date_end = date("Y-m-d", strtotime("+1 days", strtotime($date_start)));
list($date_y,$date_m,$date_d) = explode('-', $date_start);

$items = $groups = $facility = [];
$status = [0=>'申請中', 1=>'審査中', 2=>'承認済', 9=>'拒否'];
$class  = [0=>'red', 1=>'green', 2=>'blue', 9=>'black'];

/////////////////////////////////////
$rows = (new Facility)->getList();
foreach ($rows as $row){
  $fid = $row['id'];
  $fmt = '<a class="btn btn-info" href="%s?do=aic_availn&fid=%d&date=%s">%s</a>';
  $facility[$fid] = $link = sprintf($fmt, $_SERVER['PHP_SELF'], $fid,$date_start,$row['fname']);
}

///////////////////////////////
$rows =  (new Reserve)->getAll($date_start, $date_end);
foreach ($rows as $row){
  $e = $row['decided'];
  $fid = $row['facility_id'];
  if (!isset($facility[$fid])) continue;// skip bad row  
  $items[] = [
    'id' => $row['id'],
    'group'=>$row['facility_id'],
    'title'=>$row['purpose'] .'（'. $status[$e] . '）'. $row['uname'],
    'className'=> isset($class[$e]) ? $class[$e] : 'black', 
    'start'=> $row['stime'],
    'end'=> $row['etime'],
  ];
}
foreach ($facility as $fid=>$content){
  $groups[] = ['id'=>$fid, 'content'=> $content];
}
/////////////////////
$selected_time = strtotime($date_start);
$jumpbar = ['-7'=>'1週間前','-1'=>'前の日', '+1'=>'次の日','+7'=>'1週間後'];
echo '<div class="text-left">'. PHP_EOL;
foreach ($jumpbar as $delta => $label){
  $date = date("Y-m-d", strtotime($delta . " days", $selected_time));
  echo "<a href=\"?do=aic_listn&date={$date}\" class=\"btn btn-primary\">{$label}</a>" . PHP_EOL; 
} 
echo '</div>' . PHP_EOL;
?>
<div id="visualization"></div>
<script type = "text/javascript">
  function nengo(year){
    return year<1988? year: (year < 2019 ? '平成'+(year-1988) : '令和'+(year-2018))
  }

  const container = document.getElementById('visualization');
  const items = <?=json_encode($items)?>;
  const groups = <?=json_encode($groups)?>;

  moment.locale("ja");
  const options = {
    start: "<?=$date_start.' 0:00'?>",  // timeline軸が表す期間の範囲の開始日
    end: "<?=$date_start.' 23:59'?>",    // （同）範囲の終了日
    width: '100%', //timelineの表示
    horizontalScroll: false,
    zoomable: false,    // timeline chartのzoomを無効にする 
    moveable: false,    // timeline アイテムの移動を無効にする
    orientation: 'top',   // timeline軸(見出し行）を上側に表示する
    showCurrentTime: false,
    stack: true,
    timeAxis: {scale: 'hour', step: 2},
    format: {
      minorLabels: {
        hour: 'H',
      },        
      majorLabels: function (date, scale, step) { 
        var year = date.format('YYYY');
        return nengo(year) + date.format('年M月D日(dd)');
      }
    },
  };
  const timeline = new vis.Timeline(container, items, groups, options);
</script>