<?php
namespace aic;

use aic\models\Reserve;
use aic\models\Instrument;
use aic\models\KsuCode;
use aic\models\Util;

use aic\views\Html;

$page = isset($_GET['page']) ? $_GET['page'] : 1; 

$inst_id = $status = 0;
$selected_y = date('Y');
$selected_m = date('m');
$selected_d = 0;
if (isset($_POST['id'], $_POST['status'], $_POST['y'], $_POST['m'])){
  $inst_id = $_POST['id'];
  $status = $_POST['status'];
  $selected_y = $_POST['y'];
  $selected_m = $_POST['m'];
  $selected_d = isset($_POST['d']) ? $_POST['d'] : $selected_d;
  $_SESSION['selected_inst'] = $inst_id;
  $_SESSION['selected_status'] = $status;
  $_SESSION['selected_year'] = $selected_y;
  $_SESSION['selected_month'] = $selected_m;
  $_SESSION['selected_day'] = $selected_d;
}else if(isset($_SESSION['selected_inst'],$_SESSION['selected_status'])){
  $inst_id = $_SESSION['selected_inst'];
  $status = $_SESSION['selected_status'];
  $selected_y = $_SESSION['selected_year'];
  $selected_m = $_SESSION['selected_month'];
  $selected_d = $_SESSION['selected_day'];
}
$date1 = $date2 = null;
if ($selected_y > 0 and $selected_m > 0){
  $time = mktime(0, 0, 0, $selected_m, 1, $selected_y);
  $day1 = $selected_d > 0 ? $selected_d : 1; //// one day or one month from day 1
  $day2 = $selected_d > 0 ? $selected_d : date('t', $time); // one day or one month until last day
  $date1 = sprintf('%d-%d-%d 00:00', $selected_y, $selected_m, $day1); 
  $date2 = sprintf('%d-%d-%d 23:59', $selected_y, $selected_m, $day2);
  // echo $date1, ', ', $date2;
}
echo '<h3>申請状況集計</h3>' . PHP_EOL;
echo '<div class="text-left">' . PHP_EOL;
echo '<form method="post" action="?do=rsv_list" class="form-inline">'. PHP_EOL;
echo '<div class="form-group mb-2">'. PHP_EOL;
$rows = (new Instrument)->getList();
$options = Html::toOptions($rows, 'id', 'shortname', [0=>'～全ての機器～']);
echo Html::select($options, 'id', [$inst_id]);
$options = Html::rangeOptions(date('Y')-1, date('Y')+1, '年');
echo Html::select($options, 'y', [$selected_y]);
$options = Html::rangeOptions(1, 12, '月');
echo Html::select($options, 'm', [$selected_m]);
$options = Html::rangeOptions(1, 31, '日', [0=>'日選択']);
echo Html::select($options, 'd', [$selected_d]);
$rsv_status = KsuCode::RSV_STATUS;
$rsv_status[0] = 'すべて';
echo Html::select($rsv_status, 'status', [$status], 'radio');
echo '<button type="submit" class="btn btn-outline-primary mt-1 mb-1 mr-2">絞込</button>' . PHP_EOL; 
echo '</div>'. PHP_EOL;
echo '</form>'. PHP_EOL;
echo '</div>' . PHP_EOL;

// pagination
$num_rows = (new Reserve)->getNumRows($inst_id, $date1, $date2, $status);
echo Html::pagination($num_rows, KsuCode::PAGE_ROWS, $page);
// end of pagination

echo '<table class="table table-hover">'. PHP_EOL;
echo '<tr><th>部屋No.</th><th>利用機器名</th><th>利用目的</th><th>利用予定日</th>
  <th>利用時間帯</th><th>利用責任者</th><th>承認状態</th><th>操　作</th></tr>'. PHP_EOL;

$rows= (new Reserve)->getListByInst($inst_id, $date1, $date2, $status, $page);
foreach ($rows as $row){ //予約テーブルにある予約の数だけ繰り返す
  echo '<tr>'. 
    '<td>' . $row['room_no'] . '</td>' . PHP_EOL . 
    //'<td>' . Util::jpdate($row['reserved']) . '</td>' . PHP_EOL .  //申請日時を表示
    //'<td>' . $row['apply_name'] . '</td>' . PHP_EOL . //申請者氏名を表示
    //'<td>' . $row['fullname'] . '</td>' . PHP_EOL . //利用機器名を表示
    '<td>' . $row['shortname'] . '</td>' . PHP_EOL . //利用機器名(省略)を表示
    '<td>' . $row['purpose'] . '</td>' . PHP_EOL;
  $date1 = Util::jpdate($row['stime']) ;
  $date2 = Util::jpdate($row['etime']) ;
  echo '<td>' . $date1 . '</td>' . PHP_EOL; //利用日を表示
  $time2 = ($date1==$date2) ? substr($row['etime'], 10,6) : '';//日をまかがった予約は終了時刻表示なし
  echo '<td>' . substr($row['stime'], 10,6) . '～' . $time2 . '</td>'; //利用時間帯を表示
  echo '<td>' . $row['master_name'] . '</td>';//利用責任者者氏名を表示
  $i = $row['status'];
  echo '<td>' . $rsv_status[$i] . '</td>';//申請状態を表示
  $rsv_id = $row['id'];
  $status = $row['status'];
  $label = ($status==1 or $status==3) ? '承認' : '却下';
  echo '<td>' .
    '<a class="btn btn-sm btn-outline-info" href="?do=rsv_grant&id='.$rsv_id.'">'.$label.'</a>' . PHP_EOL .
    '<a class="btn btn-sm btn-outline-success" href="?do=rsv_detail&id='.$row['id'].'">詳細</a>' .
    '</td>';
  echo '</tr>' . PHP_EOL;
}
echo '</table>';

// pagination　again
echo Html::pagination($num_rows, KsuCode::PAGE_ROWS, $page);
