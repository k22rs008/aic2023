<?php
require_once('models/facility.php');
require_once('models/reserve.php');
include ('lib/func.php');

$date_curr = '2023-11-27';  //本番なら date("Y-m-d");
$date_start = isset($_GET['date'])?$_GET['date'] : $date_curr;
$date_end = date("Y-m-d", strtotime("+1 days", strtotime($date_start)));
$jpdate = jpdate($date_start);

$fid = $_GET['fid'];
$items = $groups = [];
$status = [0=>'申請中', 1=>'審査中', 2=>'承認済', 9=>'拒否'];
$class  = [0=>'red', 1=>'green', 2=>'blue', 9=>'black'];

/////////////////////////////////////
$facility = (new Facility)->getDetail($fid);
$fname = $facility['fname'];
$groups = [['id'=>$fid, 'content'=>$facility['fname']]];

echo '<p><img src="img/facility/'. $fid .'.webp" height="240" class="img-rounded"></p>';
echo '<h3 class="bg-info">'. $fname.'</h3>';
echo '<p>' .$facility['note'].'</p>';
///////////////////////////////
$rows =  (new Reserve)->getListByFid($fid, $date_start, $date_end);
if ($rows) {
  echo '<h4>' . $fname . ' の予約一覧 (' . $date_start . ')</h4>';
  echo '<table class="table table-boxed">';
  echo  '<tr><th width="20%">開始日時</th><th width="20%">終了日時</th>';
  echo  '<th>責任者</th><th>目的</th><th>承認</th>';
  //echo  '<th>予約</th>';
  echo  '</tr>';

  foreach ($rows as $row) {
      echo  '<tr>';
      $e = $row['decided'];
      echo  '<td>' . jpdate($row['stime'], true) . '</td>';
      echo  '<td>' . jpdate($row['etime'], true)  . '</td>';
      echo  '<td>' . $row['uname'] . '</td>';
      echo  '<td>' . $row['purpose'] . '</td>';
      echo  '<td>' . $status[$e] . '</td>';
      // echo  '<td><a href="?do=rsv_input" class ="btn btn-secondary">予約</a></td>';
      echo  '</tr>';
      $e = $row['decided'];  
      $items[] = [
        'id' => $row['id'],
        'group'=>$row['facility_id'],
        'title'=>$row['purpose'] .'（'. $status[$e] . '）'. $row['uname'],
        'className'=> isset($class[$e]) ? $class[$e] : 'black', 
        'start'=> $row['stime'],
        'end'=> $row['etime'],
      ];
  }
  echo  '</table>';
}else{
  // 予約がない場合、予約ボタンを表示
  echo '<p class="bg-warning">' . $fname. ' ' . $jpdate . ' の予約はありません</p>';
  //echo '<a href="?do=rsv_input&facility_id='. $facility_id .'&date=' . $selectedDate . '"><button onclick="reserveEquipment(' . $facility_id . ', \'' . $selectedDate . '\')" class = btn btn-secondary>予約する</button></a>';
}

/////////////////////
$selected_time = strtotime($date_start);
$jumpbar = ['-7'=>'1週間前','-1'=>'前の日', '+1'=>'次の日','+7'=>'1週間後'];
echo '<div class="text-left">'. PHP_EOL;
foreach ($jumpbar as $delta => $label){
  $date = date("Y-m-d", strtotime($delta . " days", $selected_time));
  echo "<a href=\"?do=aic_availn&fid={$fid}&date={$date}\" class=\"btn btn-primary\">{$label}</a>" . PHP_EOL; 
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
    timeAxis: {scale: 'hour', step: 1},
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