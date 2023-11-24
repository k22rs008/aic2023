<?php
require_once('db_config.php');
require_once('lib/KsuCode.php');
use kst\KsuCode;

$id = $_GET['id'];
echo '<table class="table table-hover">';
echo '<tr><td>利用申請者</td>';
$SQL = "SELECT * FROM tbl_reserve_test WHERE tbl_reserve_test.id=$id"; //予約テーブルを検索
$rs = $conn->query($SQL);
$row = $rs->fetch_assoc();
$SQL_username = "SELECT * FROM tbl_user, tbl_reserve_test
                 WHERE tbl_reserve_test.uid = tbl_user.uid AND tbl_reserve_test.id=$id";
$run = $conn->query($SQL_username);
$roun = $run->fetch_assoc();

echo '<td>' . $roun['uname'] . '</td>';
echo '<td>学籍番号</td>';
if($roun['urole'] == 1){
    $SQL_stnum = "SELECT * FROM tbl_student, tbl_reserve_test
                  WHERE tbl_reserve_test.uid = tbl_student.uid AND tbl_reserve_test.id=$id"; 
    $rstn = $conn->query($SQL_stnum);
    $rostn = $rstn->fetch_assoc();
    echo '<td colspan=2>' . $rostn['stid'] . '</td></tr>';
}else{
    echo '<td></td></tr>';
}
echo '<tr><td>利用責任者氏名</td>';
$SQL_master_name = "SELECT * FROM tbl_teacher, tbl_reserve_test
                    WHERE tbl_reserve_test.master_user = tbl_teacher.uid 
                    AND tbl_reserve_test.id = $id";
$rmn = $conn->query($SQL_master_name);
$romn = $rmn->fetch_assoc();
if(strlen($romn['fdid']) == 2){
    echo '<td>'. $romn['name'] . '</td><td>学部学科</td><td>' . 
     KsuCode::FACULTY_DEPT[$romn['fdid']] . 
     '</td><td>TEL. ' . $romn['tel'] . '</td></tr>';
}else{
    echo $romn['name'] . '</td><td>学部学科</td><td>' . 
     KsuCode::GRADUATE_SCHL[$romn['fdid']] . 
     '</td><td>TEL. ' . $romn['tel'] . '</td></tr>';
}

$SQL_reserve_user = 
    "SELECT * FROM tbl_reserve_user, tbl_reserve_test
    WHERE tbl_reserve_user.reserve_id = tbl_reserve_test.id
    AND tbl_reserve_user.reserve_id = $id";
$rmu = $conn->query($SQL_reserve_user);
$romu = $rmu->fetch_assoc();

$SQL_reserve_student = 
    "SELECT * FROM tbl_reserve_user, tbl_student
    WHERE tbl_reserve_user.reserve_user = tbl_student.uid
    AND tbl_reserve_user.reserve_id = $id";
$rrs = $conn->query($SQL_reserve_student);

$SQL_reserve_teacher = 
    "SELECT * FROM tbl_reserve_user, tbl_teacher
    WHERE tbl_reserve_user.reserve_user = tbl_teacher.uid
    AND tbl_reserve_user.reserve_id = $id";
$rrt = $conn->query($SQL_reserve_teacher);

$SQL_reserve_student_count = 
    "SELECT COUNT(*) AS student_count FROM tbl_reserve_user, tbl_student
    WHERE tbl_reserve_user.reserve_user = tbl_student.uid
    AND tbl_reserve_user.reserve_id = $id";
$rrsc = $conn->query($SQL_reserve_student_count);
$rorsc = $rrsc->fetch_assoc();

$SQL_reserve_teacher_count = 
    "SELECT COUNT(*) AS teacher_count FROM tbl_reserve_user, tbl_teacher
    WHERE tbl_reserve_user.reserve_user = tbl_teacher.uid
    AND tbl_reserve_user.reserve_id = $id";
$rrtc = $conn->query($SQL_reserve_teacher_count);
$rortc = $rrtc->fetch_assoc();

$chk = "SELECT *
FROM tbl_reserve_test
WHERE stime < '" . $row['etime'] . "'". " AND etime > '" . $row['stime'] . "'" . "AND decided = 2";
$rchk = $conn->query($chk);
$rrchk = $rrsc->fetch_assoc();
echo (isset($rrchk));

$count = 0;
$teacher_num = 0;
$student_num = 0;

echo '<tr><td>利用代表者氏名</td>';
while($romu){
    if($count == 0){
        if($romu['urole'] == 1){
            $rors = $rrs->fetch_assoc();
            echo '<td>' . $rors['stid'] . '</td><td>' . $rors['name'] . 
            '</td><td colspan=2>' . $rors['tel'] . '</td></tr>';
            $count += 1;
        }else{//教員ならば
            $rort = $rrt->fetch_assoc();
            echo '<td></td><td>' .  $rort['name'] . 
            '</td><td colspan=2>' . $rort['tel'] . '</td></tr>';
            $count += 1;
        }
    }else{
        if($romu['urole'] == 1){ //学生ならば
            $rors = $rrs->fetch_assoc();
            echo '<tr><td></td><td>' . $rors['stid'] . '</td><td>' . $rors['name'] . 
            '</td><td colspan=2>' . $rors['tel'] . '</td></tr>';
            $count += 1;
        }else{//教員ならば
            $rort = $rrt->fetch_assoc();
            echo '<tr><td></td><td></td><td>' .  $rort['name'] . 
            '</td><td colspan=2>' . $rort['tel'] . '</td></tr>';
            $count += 1;
        }
    }
    $romu = $rmu->fetch_assoc();
}

echo '<tr><td>その他利用者</td><td colspan=4>' . $row['other'] . '</td></tr>';
echo '<tr><td>教職員人数</td><td>' . $rortc['teacher_count'] . 
     '人</td><td>学生人数</td><td colspan=4>' . $rorsc['student_count'] . '人</td></tr>';

$SQL_facility_name =
    "SELECT * FROM tbl_facility, tbl_reserve_test
    WHERE tbl_facility.id = tbl_reserve_test.facility_id
    AND tbl_reserve_test.id =$id";
$rfn = $conn->query($SQL_facility_name);
$rofn = $rfn->fetch_assoc();

echo '<tr><td>希望利用機器</td><td colspan=4>' . $rofn['fname'] . '</td></tr>';
echo '<tr><td>希望利用日時</td><td colspan=4>' . substr($row['stime'], 0, 16)
     . ' ~ ' . substr($row['etime'], 0, 16) . '</td></tr>'; 

$SQL_sample_name = 
    "SELECT * FROM tbl_sample_test, tbl_reserve_test
    WHERE tbl_sample_test.reserve_id = tbl_reserve_test.id
    AND tbl_reserve_test.id = $id";
$rsn = $conn->query($SQL_sample_name);
$rosn = $rsn->fetch_assoc();

$states = array(1=> '固体', 2=> '液体', 3=> '気体');
$saname = $sachara = '';
$state = ''; 
if ($rosn) {
    $saname =  $rosn['saname'];
    $_i  = $rosn['sastate'];
    $state = $states[$_i];
    $sachara = $rosn['sachara'];
}
echo '<tr><td>試料名</td><td colspan=4>' . $saname . '</td></tr>';

echo '<tr><td>状態</td><td colspan=4>' . $state . '</td></tr>';
echo '<tr><td>特性</td><td colspan=4>' . $sachara . '</td></tr>';

$xray = $row['xraychk'];
$check = array(0=> '無', 1=> '有');
echo '<tr><td>X線取扱者登録の有無</td><td>' . $check[$xray] . 
    '</td><td>登録者番号</td><td colspan=2>' . $row['xraynum'] . '</td></tr>';
echo '<tr><td>備考</td><td colspan=4>' . $row['note'] . '</td></tr>';

echo '<form action="?do=rsv_decide" method="post">';
echo '<tr><td>コメント</td><td colspan=4>';
echo '<textarea name="comment" rows=4 class="form-control"></textarea></td></tr>';
echo '</table>';

echo '<input type="hidden" name="id" value="' . $id . '">';
echo '<input type="submit" value="承認" name="judge" class="btn btn-primary"></input>';
echo '&nbsp;&nbsp;<input type="submit" value="保留" name="judge" class="btn btn-warning"></input>';
echo '</form>';

echo '&nbsp;&nbsp;<a href="?do=rsv_list"><button class="btn btn-info">戻る</button></a>';//戻るボタンを追加

?>