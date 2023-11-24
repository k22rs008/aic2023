<?php
//デバッグ用
// エラーレポーティングを有効にしてエラーメッセージを表示
error_reporting(E_ALL);
ini_set('display_errors', 1);

// カスタムエラーハンドラを設定
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

include 'KsuCode.php';
use kst\KsuCode;

echo "=======kst\KsuCode.php============";
echo '<br>';

// 学科の配列を確認
echo "学科のIDと名称一覧:<br>";
foreach (KsuCode::FACULTY_DEPT as $id => $name) {
    echo "$id => $name<br>";
}

echo '<br>';

// 大学院の配列を確認
echo "\n大学院のIDと名称一覧:<br>";
foreach (KsuCode::GRADUATE_SCHL as $id => $name) {
    echo "$id => $name<br>";
}

echo '<br>';

var_dump(\kst\KsuCode::FACULTY_DEPT);

var_dump(\kst\KsuCode::GRADUATE_SCHL);

include_once 'KsuStudent.php';
use kst\KsuStudent;

echo "=======kst\KsuStudent.php============";
echo '<br>';

$sid = "25RS123"; // 学籍番号を適宜変更してテスト

$result = KsuStudent::parseSid($sid);

try{
    if ($result !== null) {
        list($sid, $stud_yr, $dept_id, $stud_no, $faculty_name, $dept_name) = $result;

        // 学年を取得し、整数に変換
        $stud_yr = intval($stud_yr);

        // 21なら3年生、20なら4年生として計算
        $year_of_study = (date('y') - $stud_yr) + 1;

        echo "学籍番号: " . $sid . "<br>";

        if($year_of_study > 4){

            echo"卒業されている学籍番号です。<br>";

        }else if($year_of_study <= 0){

            echo"まだ登録されていない学籍番号です。<br>";


        }else{

            echo "学年: " . $year_of_study . "<br>";
            echo "入学年度: " . $stud_yr . "<br>";
            echo "学科ID: " . $dept_id . "<br>";
            echo "学生番号: " . $stud_no . "<br>";
            echo "学部名: " . $faculty_name . "<br>";
            echo "学科名: " . $dept_name . "<br>";

        }
        
    } else {

        echo "無効な学籍番号です。<br>";

    }
}catch(Exception $e){
    echo "エラー発生:". $e->getMessage()."<br>";
}

var_dump($result);

echo '<br>';

// 学籍番号の結果を整数型に変換
$result[1] = intval($result[1]);
$result[3] = intval($result[3]);

var_dump($result);

echo "<br>";

?>