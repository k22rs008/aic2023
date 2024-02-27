<h3>申請状況一覧</h3>
<?php
require_once('models/Reserve.php');
require_once('models/Instrument.php');
include 'views/Html.php';
include 'lib/func.php';

$rsv_status = KsuCode::RSV_STATUS;

$rsv_status[9] = 'すべて';
$inst_id = isset($_POST['id'])? $_POST['id']: 0;
$status = isset($_POST['s'])? $_POST['s']: 9;

echo '<div class="text-left">'. PHP_EOL;
$rows = (new Instrument)->getList();
$options=[0=>'～機器選択～'];
foreach ($rows as $row){
  $options[$row['id']] = $row['shortname'];
}
echo '<form method="post" action="?do=rsv_list" class="form-inline">'. PHP_EOL;
echo '<div class="form-group mb-2">'. PHP_EOL;
echo Html::select($options, 'id', [$inst_id]);
echo '</div>'. PHP_EOL;
echo '<div class="form-group mx-sm-3 mb-2">'. PHP_EOL;
foreach ($rsv_status as $s=>$label){
  $disable = ($s==$status) ? 'disabled' : '';
  echo '<button type="submit" name="s" value="' . $s .
  '" class="btn btn-outline-primary '. $disable.' mt-1 mb-1 mr-1">'.$label.'</button>' . PHP_EOL; 
}
echo '</div>' . PHP_EOL;
echo '</form>'. PHP_EOL;
echo '</div>' . PHP_EOL;

$rows= (new Reserve)->getListDetail($inst_id, $status);
echo '<table class="table table-hover">';
echo '<tr><th>申請日時</th><th>申請者</th><th>利用機器名</th><th>利用予定日</th>
    <th>利用時間帯</th><th>利用代表者</th><th>承認状態</th><th>詳細</th></tr>';

foreach ($rows as $row){ //予約テーブルにある予約の数だけ繰り返す
    echo '<tr><td>' . jpdate($row['reserved']) . '</td>' . PHP_EOL;; //申請日時を表示
    echo '<td>' . $row['apply_name'] . '</td>' . PHP_EOL;; //申請者氏名を表示
    echo '<td>' . $row['shortname'] . '</td>' . PHP_EOL;; //利用機器名(省略)を表示
    echo '<td>' . jpdate($row['stime']) . '</td>' . PHP_EOL;; //利用日を表示
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