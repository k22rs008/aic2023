<?php
require_once('db_config.php');
require_once('lib/KsuCode.php');
require_once('lib/KsuStudent.php');
use kst\KsuCode;
use kst\KsuStudent;
//print_r($_POST); // デバッグ：送信されたデータを表示し、内容を目視で確認する
$date = $_POST['date'];
$uname = $_SESSION['uname'];
$uid = $_SESSION['uid'];
$mastername = $_POST['mastername'];
$adname = $_POST['adname'];
//$tcount = $_POST['tcount'];
//$scount = $_POST['scount'];
$other = $_POST['other'];
//$other_from = $_POST['other_from'];
//$other_tel = $_POST['other_tel'];
$fid = $_POST['facility_id'];
// $stime_h = $_POST['stime_hour'];
// $stime_m = $_POST['stime_minute'];
// $etime_h = $_POST['etime_hour'];
// $etime_m = $_POST['etime_minute'];
$stime = $_POST['stime'];
$etime = $_POST['etime'];

$saname = $_POST['saname'];
$sastate = $_POST['sastate'];
$sachara = $_POST['sachara'];
$other_chara = $_POST['other_chara'];
$xraychk = $_POST['xraychk'];
$xraynum = $_POST['xraynum'];
$note = $_POST['note'];
$master_id = '';
$sample_state = '';
$sample_chara = '';

$ng_flag = false;//入力データに問題があるのかをチェックするための変数

$SQL_login_user_info = "SELECT * FROM tbl_user
                WHERE tbl_user.uid = '{$uid}'"; //ログインしている者の情報を取得する
$rlui = $conn->query($SQL_login_user_info);
$rolui = $rlui->fetch_assoc();
if($rolui['urole'] == 1){ //ログインしている者が学生であった場合，学籍番号を取得するためのSQL
    $SQL_get_student_info = "SELECT * FROM tbl_student
                                WHERE tbl_student.uid = '{$uid}'";
    $rgs = $conn->query($SQL_get_student_info);
    $rogs = $rgs->fetch_assoc();
    echo '<input type="hidden" name="reserve_stid[]" value="' . $rogs['uid'] .'">';
}else{
    $SQL_get_teacher_info = "SELECT * FROM tbl_teacher
        WHERE tbl_teacher.uid = '{$uid}'";
    $rgt = $conn->query($SQL_get_teacher_info);
    $rogt = $rgt->fetch_assoc();
}

$SQL_check_teacher = "SELECT * FROM tbl_teacher
        WHERE '{$mastername}' = tbl_teacher.name
        OR '{$mastername}' = tbl_teacher.uid";
$rct = $conn->query($SQL_check_teacher);
$roct = $rct->fetch_assoc();

//print_r($adname);

?>
<h2>予約情報照会画面</h2>
<form action="?do=rsv_save" method="post">
<table class="table">
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if($rolui['urole'] == 1){
        // データを表示する
        echo '<h3>入力されたデータ</h3>';
        echo '<tr><td>申請者氏名: </td><td>' . $rogs['name'] . '</td><td></td>';
        echo '<td>学籍番号: </td><td>' . $rogs['stid'] . '</td></tr>';
        echo '<tr><td>利用責任者氏名: </td><td>';
        if(!$roct){
            echo '正しい教職員名を入力して下さい</td><td></td>';
            $ng_flag = true;
        }else{
            echo $roct['name'] . '</td><td></td>';
            echo '<input type="hidden" name="master_id" value="' . $roct['uid'] . '">';
            echo '<input type="hidden" name="reserve_teacher[]" value="' . $roct['uid'] . '">';
        }
        echo "<td>学部学科</td>";
        if(strlen($rogs['fdid']) == 2){
            echo "<td>".KsuCode::FACULTY_DEPT[$rogs['fdid']]."</td></tr>";
        }else{
            echo "<td>".KsuCode::GRADUATE_SCHL[$rogs['fdid']]."</td></tr>"; 
        }
        echo '<td>TEL: </td><td>' . $rogs['tel'] . '</td></tr>';
    }else{
        echo '<h3>入力されたデータ</h3>';
        echo '<tr><td>申請者氏名: </td><td>' . $rogt['name'] . '</td><td></td>';
        echo '<input type="hidden" name="reserve_teacher[]" value="' . $rogt['uid'] . '">';
        echo '<td>学籍番号: </td><td></td></tr>';
        echo '<tr><td>利用責任者氏名: </td><td>';
        if(!$roct){
            echo '正しい教職員名を入力して下さい</td><td></td>';
            $ng_flag = true;
        }else if($roct['uid'] == $rogt['uid']){
            echo $rogt['name'] . '</td><td></td>';
            echo '<input type="hidden" name="master_id" value="' . $roct['uid'] . '">';
        }else{
            echo $roct['name'] . '</td><td></td>';
            echo '<input type="hidden" name="master_id" value="' . $roct['uid'] . '">';
            echo '<input type="hidden" name="reserve_teacher[]" value="' . $roct['uid'] . '">';
        }
        echo "<td>学部学科</td>";
        if(strlen($rogt['fdid']) == 2){
            echo "<td>".KsuCode::FACULTY_DEPT[$rogt['fdid']]."</td></tr>";
        }else{
            echo "<td>".KsuCode::GRADUATE_SCHL[$rogt['fdid']]."</td></tr>";
        }
        echo '<td>TEL: </td><td>' . $rogt['tel'] . '</td></tr>';
    }  

    $count = 0;
    if($rolui['urole'] == 1){
        for($i = 1; $i <= count($adname); $i+=1){
            $SQL_reserve_user_student = "SELECT * FROM tbl_student
                                        WHERE '{$adname[$i]}' = tbl_student.stid
                                        AND '{$adname[$i]}' <> '{$rogs['stid']}'";
            $rrus = $conn->query($SQL_reserve_user_student);
            $rorus = $rrus->fetch_assoc();
            $SQL_reserve_user_teacher = "SELECT * FROM tbl_teacher
                                        WHERE '{$adname[$i]}' = tbl_teacher.tid
                                        OR '{$adname[$i]}' = tbl_teacher.name";
            $rrut = $conn->query($SQL_reserve_user_teacher);
            $rorut = $rrut->fetch_assoc();
            if($rorus || $rorut){
                $count += 1;
            }
        }
    }else{
        for($i = 1; $i <= count($adname); $i+=1){
            $SQL_reserve_user_student = "SELECT * FROM tbl_student
                                        WHERE '{$adname[$i]}' = tbl_student.stid";
            $rrus = $conn->query($SQL_reserve_user_student);
            $rorus = $rrus->fetch_assoc();
            $SQL_reserve_user_teacher = "SELECT * FROM tbl_teacher
                                        WHERE '{$adname[$i]}' = tbl_teacher.tid
                                        OR '{$adname[$i]}' = tbl_teacher.name";
            $rrut = $conn->query($SQL_reserve_user_teacher);
            $rorut = $rrut->fetch_assoc();
            if($rorus || $rorut){
                $count += 1;
            }
        }
    }
    if($count == 0){
        $count = 1;
    }

    echo '<tr><td rowspan = "' . $count . '">利用代表者: </td>';
    
    $convertedAdname = []; // 存在するデータを格納する配列
    //print_r($adname);
    $reserve_users = array();
    $i = 0;

    for($i = 1; $i <= count($adname); $i+=1){
        $SQL_reserve_user_teacher = "SELECT * FROM tbl_teacher
                                        WHERE '{$adname[$i]}' = tbl_teacher.tid
                                        OR '{$adname[$i]}' = tbl_teacher.name";
        $rrut = $conn->query($SQL_reserve_user_teacher);
        $rorut = $rrut->fetch_assoc();
        if($rorut){
            echo '<td>' . $rorut['name'] . '</td><td></td></tr>';
            echo '<input type="hidden" name="reserve_teacher[]" value="' . $rorut['uid'] . '">';
        }
    }
    
    foreach ($adname as $key => $value) {
        $convertedValue = KsuStudent::validateSid($value);
        if(is_null($convertedValue) == false){
            if($rolui['urole'] == 1){
                $SQL_reserve_user_student = "SELECT * FROM tbl_student
                                WHERE '{$convertedValue}' = tbl_student.stid
                                AND '{$convertedValue}' <> '{$rogs['stid']}'";
                $rrus = $conn->query($SQL_reserve_user_student);
                $rorus = $rrus->fetch_assoc();
                if($rorus){
                    echo $convertedAdname[] = '<td colspan=2>' . $rorus['name']. ' (' . $rorus['stid'] . ')</td></tr>';
                    echo '<input type="hidden" name="reserve_stid[]" value="' . $rorus['uid'] .'">';
                }
            }else{
                $SQL_reserve_user_student = "SELECT * FROM tbl_student
                                WHERE '{$convertedValue}' = tbl_student.stid";
                $rrus = $conn->query($SQL_reserve_user_student);
                $rorus = $rrus->fetch_assoc();
                if($rorus){
                    echo $convertedAdname[] = '<td>' . $rorus['name']. '</td><td>(' . $rorus['stid'] . ')</td></tr>';
                    echo '<input type="hidden" name="reserve_stid[]" value="' . $rorus['uid'] .'">';
                }
            }
        }
        
    }
    echo'<tr><td>その他利用者:</td>';
    if(!empty($other)){
        echo '<td>'.$other.'</td>';
    }
    echo '</tr>';

    echo '<tr><td>希望利用機器: </td><td>';
    $SQL_check_facility = "SELECT * FROM tbl_facility
                            WHERE '{$fid}' = tbl_facility.id";
    $rcf = $conn->query($SQL_check_facility);
    $rocf = $rcf->fetch_assoc();
    echo $rocf['fname'] . '</td></tr>';
    
    // if($stime_h == $etime_h && $stime_m >= $etime_m || $stime_h > $etime_h){
    //     echo '<tr><td>希望利用日時: </td><td>希望する時間帯を入力して下さい</td></tr>';
    //     $ng_flag = true;
    // }else{
    //     echo '<tr><td>希望利用日時: </td><td>' . $stime_h.":" . $stime_m.' 〜 '
    //          . $etime_h.":" . $etime_m.'</td></tr>';                     
    // }
    echo '<tr><td>希望利用日時: </td><td>';
    $_date = new DateTimeImmutable($date);
    $wdays = ['日','月','火','水','木','金','土'];
    $w = $_date->format('w');
    echo '<span class="text-info　">',$_date->format('Y年n月d日'),'(' ,$wdays[$w],')</span>  ';
    echo  $stime.' 〜 '. $etime.'</td></tr>';                    

    echo '<tr><td>試料名: </td><td>' . $saname . '</td></tr>';
    $state =[1 => "固体", 2=> "液体", 3 => "気体"];
    echo '<tr><td>状態: </td><td>' . $state[$sastate] . '</td>';
    $sample_state = $state[$sastate];
    echo '<td>特性: </td><td>';
    if($sachara != 4){
        $chara =[1 => "爆発性", 2=> "毒性", 3 => "腐食性"];
        echo $chara[$sachara] . '</td></tr>';
        $sample_chara = $chara[$sachara];
    }else{
        echo $other_chara . '</td></tr>';
        $sample_chara = $other_chara;
    }
    
    echo '<tr><td>ｘ線取扱者の有無: </td><td>' . ($xraychk == 1 ? '有' : '無') . '</td>';
    if ($xraychk == 1) {
        echo '<td>登録者番号: </td><td>' . $xraynum . '</td></tr>';
    }
    echo '<tr><td>備考: ' . $note . '</td></tr>';
}
//*              
echo '<input type="hidden" name="fid" value=' . $fid . '>';
echo '<input type="hidden" name="uid" value=' . $uid . '>';
//echo '<input type="hidden" name="master_id" value=' . $master_id . '>';
echo '<input type="hidden" name="other" value=' . $other . '>';
echo '<input type="hidden" name="date" value=' . $date . '>';
// echo '<input type="hidden" name="stime_h" value=' . $stime_h . '>';
// echo '<input type="hidden" name="stime_m" value=' . $stime_m . '>';
// echo '<input type="hidden" name="etime_h" value=' . $etime_h . '>';
// echo '<input type="hidden" name="etime_m" value=' . $etime_m . '>';
echo '<input type="hidden" name="stime" value=' . $stime . '>';
echo '<input type="hidden" name="etime" value=' . $etime . '>';

echo '<input type="hidden" name="xraychk" value=' . $xraychk . '>';
echo '<input type="hidden" name="xraynum" value=' . $xraynum . '>';
echo '<input type="hidden" name="note" value=' . $note . '>';

echo '<input type="hidden" name="sample_name" value=' . $saname . '>';
echo '<input type="hidden" name="sample_state" value=' . $sastate . '>';
echo '<input type="hidden" name="sample_chara" value=' . $sample_chara . '>';
echo '<input type="hidden" name="ng_flag" value=' . $ng_flag . '>';
//*/  

echo '</table>';
echo '<input type="submit" value="登録" class="btn btn-primary">&nbsp;';

echo '<input type="reset" value="取消" class = "btn btn-secondary">&nbsp;';
echo '<input value="戻る" onclick="history.back();" type="button" class = "btn btn-info">';

echo '</form>';