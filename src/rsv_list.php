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
$selected_t = 30;
if (isset($_POST['id'], $_POST['status'], $_POST['y'], $_POST['m'])){
  $selected_d = isset($_POST['d']) ? $_POST['d'] : $selected_d;
  $selected_t = isset($_POST['t']) ? $_POST['t'] : $selected_t;
  $_SESSION['selected_inst'] = $_POST['id'];
  $_SESSION['selected_status'] = $status = $_POST['status'];
  $_SESSION['selected_year'] = $selected_y = $_POST['y'];
  $_SESSION['selected_month'] = $selected_m = $_POST['m'];
  $_SESSION['selected_day'] = $selected_d;
  $_SESSION['selected_timespan'] = $selected_t; //period
}else if(isset($_SESSION['selected_inst'],$_SESSION['selected_status'])){
  $inst_id = $_SESSION['selected_inst'];
  $status = $_SESSION['selected_status'];
  $selected_y = $_SESSION['selected_year'];
  $selected_m = $_SESSION['selected_month'];
  $selected_d = $_SESSION['selected_day'];
  $selected_t = $_SESSION['selected_timespan'];
}
$date1 = $date2 = null;
if ($selected_y > 0 and $selected_m > 0){
  $day = $selected_d > 0 ? $selected_d : 1;
  $date = new \DateTimeImmutable($selected_y .'-'.$selected_m.'-'.$day);
  $def = [1=>'P1D', 7=>'P1W', 30=>'P1M',];
  $period = new \DateInterval($def[$selected_t]); 
  $date1 = $date->format('Y-m-d 00:00'); 
  $date2 = $date->add($period)->format('Y-m-d 00:00');
  // echo $date1, ', ', $date2;
}
echo '<h3>申請状況一覧</h3>' . PHP_EOL;
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
$options = [1=>'１日間', 7=>'1週間', 30=>'1ヶ月間',];
echo Html::select($options, 't', [$selected_t]);
$rsv_status = KsuCode::RSV_STATUS;
$rsv_status[0] = 'すべて';
echo Html::select($rsv_status, 'status', [$status], 'radio');
echo '<button type="submit" class="btn btn-outline-primary mt-1 mb-1 mr-2">絞込</button>' . PHP_EOL; 
echo '<span class="float-right ">
  <a class="btn btn-outline-success" href="?do=rsv_summary">集計</a></span>' . PHP_EOL;
echo '<span class="float-right ">
  <a class="btn btn-outline-success" href="?do=rsv_excel">出力</a></span>' . PHP_EOL;
echo '</div>'. PHP_EOL;
echo '</form>'. PHP_EOL;
echo '</div>' . PHP_EOL;

// pagination on top
$num_rows = (new Reserve)->getNumRows($inst_id, $date1, $date2, $status);
echo Html::pagination($num_rows, KsuCode::PAGE_ROWS, $page);

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

// pagination at bottom
echo Html::pagination($num_rows, KsuCode::PAGE_ROWS, $page);
