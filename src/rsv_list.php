<?php
namespace aic;

use aic\models\Reserve;
use aic\models\Instrument;
use aic\models\KsuCode;
use aic\models\Util;

use aic\views\Html;

$page = isset($_GET['page']) ? $_GET['page'] : 1; 

if (isset($_POST['id'], $_POST['s'], $_POST['y'], $_POST['m'])){
  $inst_id = $_POST['id'];
  $status = $_POST['s'];
  $selected_y = $_POST['y'];
  $selected_m = $_POST['m'];
  $_SESSION['selected_inst'] = $inst_id;
  $_SESSION['selected_status'] = $status;
  $_SESSION['selected_year'] = $selected_y;
  $_SESSION['selected_month'] = $selected_m;
}else if(isset($_SESSION['selected_inst'],$_SESSION['selected_status'])){
  $inst_id = $_SESSION['selected_inst'];
  $status = $_SESSION['selected_status'];
  $selected_y = $_SESSION['selected_year'];
  $selected_m = $_SESSION['selected_month'];
}else{
  $inst_id = 0; 
  $status = 0;
  $selected_y = date('Y');
  $selected_m = date('m');
}
$date1 = $date2 = null;
if ($selected_y>0 and $selected_m>0){
  $time = mktime(0, 0, 0, $selected_m, 1, $selected_y);
  $date1 = date('Y-m-d 00:00', $time); 
  $date2 = date('Y-m-t 23:59', $time);
  // echo $date1, ', ', $date2;
}
echo '<h3>申請状況一覧</h3>' . PHP_EOL;
echo '<div class="text-left">' . PHP_EOL;
echo '<form method="post" action="?do=rsv_list" class="form-inline">'. PHP_EOL;
echo '<div class="form-group mb-2">'. PHP_EOL;
$rows = (new Instrument)->getList();
$options = [0=>'～全ての機器～'];
foreach ($rows as $row){
  $options[$row['id']] = $row['shortname'];
}
ksort($options);
$_yrange1 = range(date('Y') - 1, date('Y') + 2);
$_yrange2 = array_map(fn($v):string=>$v. '年', $_yrange1);
$yrange = array_combine($_yrange1, $_yrange2);
$_mrange = array_map(fn($v):string=>$v. '月', range(1, 12));
$mrange = array_combine(range(1, 12), $_mrange);

echo Html::select($options, 'id', [$inst_id]);
echo Html::select($yrange, 'y', [$selected_y]);
echo Html::select($mrange, 'm', [$selected_m]);
echo '</div>'. PHP_EOL;

echo '<div class="form-group mx-sm-3 mb-2">'. PHP_EOL;
$rsv_status = KsuCode::RSV_STATUS;
$rsv_status[9] = 'すべて';
foreach ($rsv_status as $s=>$label){
  $disable = ($s==$status) ? 'disabled' : '';
  echo '<button type="submit" name="s" value="' . $s .
  '" class="btn btn-outline-primary '. $disable.' mt-1 mb-1 mr-1">'.$label.'</button>' . PHP_EOL; 
}
echo '</div>' . PHP_EOL;
echo '</form>'. PHP_EOL;
echo '</div>' . PHP_EOL;

// pagination
$num_rows = (new Reserve)->getNumRows($inst_id, $date1, $date2, $status);
echo Html::pagination($num_rows, KsuCode::PAGE_ROWS, $page);
// end of pagination

echo '<table class="table table-hover">';
echo '<tr><th>申請日時</th><th>申請者</th><th>利用機器名</th><th>利用予定日</th>
    <th>利用時間帯</th><th>利用代表者</th><th>承認状態</th><th>詳細</th></tr>';

$rows= (new Reserve)->getListByInst($inst_id, $date1, $date2, $status, $page);
foreach ($rows as $row){ //予約テーブルにある予約の数だけ繰り返す
    echo '<tr><td>' . Util::jpdate($row['reserved']) . '</td>' . PHP_EOL; //申請日時を表示
    echo '<td>' . $row['apply_name'] . '</td>' . PHP_EOL; //申請者氏名を表示
    echo '<td>' . $row['shortname'] . '</td>' . PHP_EOL; //利用機器名(省略)を表示
    echo '<td>' . Util::jpdate($row['stime']) . '</td>' . PHP_EOL; //利用日を表示
    echo '<td>' . substr($row['stime'], 10,6) . '～' . substr($row['etime'], 10,6) . '</td>'; //利用時間帯を表示
    echo '<td>' . $row['master_name'] . '</td>';//利用代表者氏名を表示
    $i = $row['status'];
    echo '<td>' . $rsv_status[$i] . '</td>';//利用代表者氏名を表示
    echo '<td>' .
    '<a class="btn btn-sm btn-outline-success" href="?do=rsv_detail&id='.$row['id'].'">詳細</a>' .
    '</td>';
    echo '</tr>' . PHP_EOL;
}
echo '</table>';

// pagination　again
echo Html::pagination($num_rows, KsuCode::PAGE_ROWS, $page);
