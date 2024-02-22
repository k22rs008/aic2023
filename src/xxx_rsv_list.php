<?php
require_once('db_config.php');
require_once('lib/func.php');
require_once('lib/KsuCode.php');
require_once('lib/KsuStudent.php');
use kst\KsuStudent;
use kst\KsuCode;
?>
<h3>申請状況一覧</h3>

<label><input type="radio" name="reserve_check" value="all" id="reserve_check_all" checked>全て</label>
<label><input type="radio" name="reserve_check" value="unapproved" id="reserve_check0">未承認</label>
<label><input type="radio" name="reserve_check" value="keep" id="reserve_check1">審査中</label>
<label><input type="radio" name="reserve_check" value="approved" id="reserve_check2">承認済み</label>
<!-- <button id="sort_btn">絞り込み</button> -->

<?php
// 教員の学科IDを調べておく
$rs = $conn->query("SELECT * FROM tbl_teacher");
if (!$rs) die('エラー: ' . $conn->error);
$teachers = [];
while($row=$rs->fetch_assoc()){
    $uid = $row['uid'];
    $teachers[$uid] = $row['fdid'];
}
// print_r($teachers);

// 予約関連情報を調べる。JOINでSQL一つ
$sql = "SELECT f.*,r.*, u.uname, u.urole, t.uid as master_uid , t.name as master_name 
    FROM tbl_facility f, tbl_reserve_test r, tbl_user u, tbl_teacher t 
    WHERE r.facility_id = f.id AND r.uid=u.uid AND t.uid=master_user ORDER BY stime DESC"; 
    //予約テーブルを検索
$rs = $conn->query($sql);
if (!$rs) die('エラー: ' . $conn->error);
echo '<table class="table table-hover">';
echo '<tr><th>申請日時</th><th>利用責任者</th><th>利用機器名</th><th>利用予定日</th>
    <th>利用時間帯</th><th>利用代表者</th><th>学部学科</th><th>承認状態</th><th>詳細</th></tr>';

while($row = $rs->fetch_assoc()){ //予約テーブルにある予約の数だけ繰り返す
    echo '<tr><td>' . jpdate($row['reserved']) . '</td>'; //申請日時を表示
    echo '<td>' . $row['master_name'] . '</td>'; //利用責任者氏名を表示
    echo '<td>' . $row['fshortname'] . '</td>'; //利用機器名(省略)を表示
    echo '<td>' . jpdate($row['stime']) . '</td>'; //利用日を表示
    echo '<td>' . substr($row['stime'], 10,6) . ' ~' . substr($row['etime'], 10,6) . '</td>'; //利用時間帯を表示
    echo '<td>' . $row['uname'] . '</td>';//利用代表者氏名を表示
    $uid = $row['uid'];
    $dept = KsuStudent::parseSid(substr($uid, 1)); //1文字目をとって学籍番号を認識する
    if ($dept){
        echo '<td>' , $dept[5], '</td>';
    }else{
        $fdid = $teachers[$uid];
        $fdname = KsuCode::FACULTY_DEPT[$fdid]; 
        echo '<td>' , explode(' ',$fdname )[1], '</td>';
    }
    $decided = $row['decided'];
    $judgment = array(0=> '未承認', 1=>'審査中', 2=> '承認済み');
    echo '<td>' . $judgment[$decided] . '</td>'; //申請状況を表示
    if($_SESSION['urole'] == 9){
        echo '<td><a href="?do=rsv_detail&id=' . $row['id'] . '"><button class="btn btn-info">詳細</button></a></td>';//承認ボタンを追加
    }
}
echo '</table>';