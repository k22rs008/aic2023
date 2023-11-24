<?php
require_once('db_config.php');

$ng_flag = $_POST['ng_flag'];
$fid = $_POST['fid'];
$uid = $_POST['uid'];
$master_id = $_POST['master_id'];
$other = $_POST['other'];
$date = $_POST['date'];
// $stime_h = $_POST['stime_h'];
// $stime_m = $_POST['stime_m'];
// $etime_h = $_POST['etime_h'];
// $etime_m = $_POST['etime_m'];
$stime = $_POST['stime'];
$etime = $_POST['etime'];
//$saname = $_POST['saname'];
//$sastate = $_POST['sastate'];
//$sachara = $_POST['sachara'];
$xraychk = $_POST['xraychk'];
$xraynum = $_POST['xraynum'];
$note = $_POST['note'];


$sample_name = $_POST['sample_name'];
$sample_state = $_POST['sample_state'];
$sample_chara = $_POST['sample_chara'];
if($ng_flag == true){
    $alert = "<script type='text/javascript'>alert('正しい情報を入力して下さい．');</script>";
    echo $alert;
    echo '<script>location.href = "?do=aic_reserve_DBtest&facility_id=' . $fid . '&date=' . $date . '" ;</script>';
}else{
    if(is_null($_POST['reserve_stid'])){
        $reserve_stid = [];
    }else{
        $reserve_stid = $_POST['reserve_stid'];
    }
    $reserve_teacher = $_POST['reserve_teacher'];


    //var_dump($master_id);
    //var_dump($reserve_stid);
    //var_dump($reserve_teacher);
    //print_r($_POST);

    $SQL_insert = "INSERT INTO tbl_reserve_test(facility_id, uid, master_user, 
                other, reserved, stime, etime, xraychk, xraynum, note, decided, 
                purpose, comment) VALUES ("
                . $fid . ", '" . $uid . "', '" . $master_id . "', '" . $other . "', now(), '" . 
                $date . " " . $stime. "', '" . $date . " " . $etime. ":', " . $xraychk .
                ", '" . $xraynum . "', '" . $note . "', 0, '', '');";
    $SQL_insert_sample = "INSERT INTO tbl_sample_test VALUES(
                        LAST_INSERT_ID(), '" . $sample_name . "', '" . $sample_state . 
                        "', '" . $sample_chara . "');";

    $ri = $conn->query($SQL_insert);
    if(!$ri) die('エラー:' . $conn->error);
    $ris = $conn->query($SQL_insert_sample);
    if(!$ris) die('エラー:' . $conn->error);
    for($i = 0; $i < count($reserve_teacher); $i++){
        $SQL_insert_teacher = "INSERT INTO tbl_reserve_user VALUES(
                            LAST_INSERT_ID(), '" . $reserve_teacher[$i] . "', 5);";
        $rit = $conn->query($SQL_insert_teacher);
        if(!$rit) die('エラー:' . $conn->error);
    }
    for($i = 0; $i < count($reserve_stid); $i++){
        $SQL_insert_user = "INSERT INTO tbl_reserve_user VALUES(
                            LAST_INSERT_ID(), '" . $reserve_stid[$i] . "', 1);";
        $riu = $conn->query($SQL_insert_user);
        if(!$riu) die('エラー:' . $conn->error);
    }
    header('Location:?do=aic_home');
}
?>