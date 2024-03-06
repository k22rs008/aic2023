<?php
namespace aic;

use aic\models\Reserve;
use aic\models\Util;
use aic\views\Html;

$inst_id = $status = 0;
$y = date('Y');
$m = date('m');
$d = date('d');
$t = 7;

if (isset($_SESSION['selected_inst'], $_SESSION['selected_status'])){
  // Html::debug_msg($_SESSION);
  $inst_id = $_SESSION['selected_inst'];
  $status = $_SESSION['selected_status'];
  $y = $_SESSION['selected_year'];
  $m = $_SESSION['selected_month'];
  $d = $_SESSION['selected_day'];
  $t = $_SESSION['selected_timespan'];
}
$date = new \DateTimeImmutable($y .'-'.$m.'-'.$d);
$def = [1=>'P1D', 7=>'P1W', 30=>'P1M',];
$period = new \DateInterval($def[$t]); 
$date1 = $date->format('Y-m-d 00:00'); 
$date2 = $date->add($period)->format('Y-m-d 00:00');

$data = (new Reserve)->getReport($inst_id, $date1, $date2, $status);
// Html::debug_msg($data['report']);
$total = ['student_n'=>0, 'staff_n'=>0, 'other_n'=>0];

echo '<h3>申請状況集計</h3>' . PHP_EOL;
echo '期　間：'. Util::jpdate($date1,true) . '～' . Util::jpdate($date2,true);
echo '<table class="table table-hover table-bordered">'. PHP_EOL;
echo '<tr><th>日付</th><th>学生利用者数</th><th>教職員利用者数</th><th>その他利用者数</th></tr>'. PHP_EOL;
foreach ($data['report'] as $date=>$arr){
  echo '<tr>' . PHP_EOL;
  printf('<td>%s</td>', $date);
  foreach ($arr as $key=>$val){
    $total[$key] += $val;
    printf('<td>%d</td>', $val);
  }
  echo '</tr>' . PHP_EOL;
}
vprintf('<tr><th>小計</th><th>%d</th><th>%d</th><th>%d</th></tr>'. PHP_EOL, $total);

echo '</table>'. PHP_EOL;

echo '<h4 class="float-right">合計：', array_sum($total), '</h4>';

echo '<a href="?do=rsv_list" class="btn btn-outline-info m-2" onclick="history.back();">戻る</a> ';