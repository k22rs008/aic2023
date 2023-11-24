<h2>総合機器センター機器設備利用申請</h2>
<?php

require_once('lib/KsuCode.php');
require_once ('db_config.php');
use kst\KsuCode;

$uid  = $_SESSION['uid'];//セッション変数に登録された、ログイン中のユーザID
$id = $_GET['facility_id'];
$date = $_GET['date'];

$SQL_login_user_info = "SELECT * FROM tbl_user
                       WHERE tbl_user.uid='{$uid}'"; //ログインしている者の情報を取得する
$rlui = $conn->query($SQL_login_user_info);
$rolui = $rlui->fetch_assoc();
if($uid == ""){
    $alert = "<script type='text/javascript'>alert('ログインして下さい．');</script>";
    echo $alert;
    echo '<script>location.href="?do=aic_home" ;</script>';
}else if($rolui['urole'] == 1){ //ログインしている者が学生であった場合，学籍番号を取得するためのSQL
    $SQL_get_student_info = "SELECT * FROM tbl_student
                             WHERE tbl_student.uid='{$uid}'";
    $rgs = $conn->query($SQL_get_student_info);
    $rogs = $rgs->fetch_assoc();
}else{
    $SQL_get_teacher_info = "SELECT * FROM tbl_teacher
                             WHERE tbl_teacher.uid='{$uid}'";
    $rgt = $conn->query($SQL_get_teacher_info);
    $rogt = $rgt->fetch_assoc();
}
//echo $rolui['uname'];
echo '<form action="?do=rsv_confirm" method="post">';
echo '<input type="hidden" name="facility_id" value="' .$id. '">';
echo '<table class="table table-condensed">';
echo '<tr><td>申請者氏名</td><td>';
echo $rolui['uname'] .'</td><td></td>';
echo "<td>学籍番号</td><td>";
if($rolui['urole']== 1){
    echo  $rogs['stid'] . "</td></tr>";
}

echo '<tr><td>利用責任者氏名※</td><td>';
echo '<input type="text" name="mastername" class="form-control" required></td><td></td>';
echo "<td>学部学科</td>";
if($rolui['urole'] == 1){
    if(strlen($rogs['fdid']) == 2){
        echo "<td>".KsuCode::FACULTY_DEPT[$rogs['fdid']]."</td></tr>";
    }else{
        echo "<td>".KsuCode::GRADUATE_SCHL[$rogs['fdid']]."</td></tr>"; 
    }
}else{
    if(strlen($rogt['fdid']) == 2){
        echo "<td>".KsuCode::FACULTY_DEPT[$rogt['fdid']]."</td></tr>";
    }else{
        echo "<td>".KsuCode::GRADUATE_SCHL[$rogt['fdid']]."</td></tr>";
    }
}
echo "<tr><td>TEL</td>";

if($rolui['urole'] == 1){
    echo '<td>'.$rogs['tel'].'</td></tr>';
}else{
    echo '<td>'.$rogt['tel'].'</td></tr>';
}


//変数仮組み
echo '<tr><td rowspan="2">利用代表者</td>';
echo '<td><input type="text" name="adname[1]" placeholder="(例)21LL001" class="form-control"></td>';
echo '<td><input type="text" name="adname[2]" placeholder="(例)21LL002" class="form-control"></td>';
echo '<td><input type="text" name="adname[3]" placeholder="(例)21LL003" class="form-control"></td></tr>';
//変数仮組み
/*
echo "<td>教職員人数</td>";
echo '<td><input type="number" min="0" value="0" name="tcount"></td></tr>';
*/
echo '<tr><td><input type="text" name="adname[4]" placeholder="(例)21LL004" class="form-control"></td>';
echo '<td><input type="text" name="adname[5]" placeholder="(例)21LL005" class="form-control"></td>';
echo '<td><input type="text" name="adname[6]" placeholder="(例)21LL006" class="form-control"></td></tr>';

//echo '<tr><td rowspan="2">その他利用者</td>';
echo '<tr><td>その他利用者</td>';
echo '<td colspan="3 "><textarea name="other" placeholder="○○高校見学 ○名参加" class="form-control"></textarea></td></tr>';

//echo '<td>氏名</td><td><input type="text" name="other" value="" placeholder="佐藤太郎"></td>';
//echo '<td>所属</td><td><input type="text" name="other_from"  value="" placeholder="会社名"></td></tr>';

//echo "<td>学生人数</td>";
//echo '<td><input type="number" min="0"  value="0" name="scount"></td></tr>';

//echo "<td>TEL</td>";
//echo'<td><input type="text" name="other_tel" value="" placeholder="999-99-9999"></td></tr>';


$SQL_facility = "SELECT * FROM tbl_facility
                 WHERE '{$id}'=tbl_facility.id";
$rf = $conn->query($SQL_facility);
$rof = $rf->fetch_assoc();

echo "<tr><td>希望利用機器※</td>";
echo '<td colspan=4>'. $rof['fname'] . '</td></tr>';

echo "<tr><td>希望利用日時※</td>";
// $_date = new DateTimeImmutable($date);
// $wdays = ['日','月','火','水','木','金','土'];
// $w = $_date->format('w');
// echo '<td class="text-info">',$_date->format('Y年n月d日'),'(' ,$wdays[$w],')</td>';
echo '<td><input type="date" name="date" class="form-control" required></td>';
echo '<td><input type="time" name="stime" class="form-control" required></td>';
echo '<td><input type="time" name="etime" class="form-control" required></td>';
echo '</tr>';

echo "<tr><td>試料名</td>";
echo '<td colspan=3><input type="text" name="saname" class="form-control"></td></tr>';

echo "<tr><td>状態</td>";
echo '<td><input type="radio" name="sastate" value="1" class="form-check-input" checked>固体</td>';
echo '<td><input type="radio" name="sastate" value="2" class="form-check-input">液体</td>';
echo '<td><input type="radio" name="sastate" value="3" class="form-check-input">気体</td></tr>';

echo "<tr><td>特性</td>";
echo '<td><input type="radio" name="sachara" value="1" class="form-check-input" checked>爆発性</td>';
echo '<td><input type="radio" name="sachara" value="2" class="form-check-input">毒性</td>';
echo '<td><input type="radio" name="sachara" value="3" class="form-check-input">腐食性</td>';
echo '<td><input type="radio" name="sachara" value="4" class="form-check-input">その他
    <input type="text" name="other_chara" class="form-control"></td></tr>';
//echo '<input type="text" name="other_chara"></tr>';

echo "<tr><td>ｘ線取扱者の有無<br>（ｘ線機器利用者のみ）</td>";
echo '<td><input type="radio" name="xraychk" value="1" class="form-check-input">有</td>';
echo '<td><input type="radio" name="xraychk" value="0" class="form-check-input" checked>無</td>';
echo '<td>登録者番号<input type="text" name="xraynum"  class="form-control"></td></tr>';

echo '<tr><td>備考</td>';
echo '<td colspan=3><textarea name="note" class="form-control"></textarea></td>';
?>  
</td></tr>
</table>
<input type="submit" value="登録" class="btn btn-primary">
&nbsp;
<input type="reset" value="取消" class="btn btn-secondary">
</form>

<br>
<br>
<br>