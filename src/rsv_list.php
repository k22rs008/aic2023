<?php
namespace aic;

use aic\models\Reserve;
use aic\models\User;
use aic\models\KsuCode;
use aic\models\Util;

use aic\views\Html;

$page = isset($_GET['page']) ? $_GET['page'] : 1; 

echo '<h3>申請状況一覧</h3>' . PHP_EOL;
include 'include/_rsv_search.inc.php';
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
  $i = $row['process_status'];
  echo '<td>' . $rsv_status[$i] . '</td>';//申請状態を表示
  $rsv_id = $row['id'];
  $status = $row['process_status'];
  $label = ($status==1 or $status==3) ? '承認' : '却下';
  echo '<td>';
  $is_admin = (new User)->isAdmin();
  if ($is_admin){
    echo '<a class="btn btn-sm btn-outline-info" href="?do=rsv_grant&id='.$rsv_id.'">'.$label.'</a>' . PHP_EOL;
  }
  echo '<a class="btn btn-sm btn-outline-success" href="?do=rsv_detail&id='.$row['id'].'">詳細</a>' .
    '</td></tr>' . PHP_EOL;
}
echo '</table>';

// pagination at bottom
echo Html::pagination($num_rows, KsuCode::PAGE_ROWS, $page);
