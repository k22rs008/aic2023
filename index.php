<?php
session_start();
date_default_timezone_set('Asia/Tokyo');
include('src/views/bs4_header.php');
$action = 'aic_home'; //ホームページ (aic_home)をデフォルト機能とする
if (isset($_GET['do'])) {//index.php?do=に続くパラメータで実行する機能を指定
  $action = $_GET['do'];
}
include('src/' . $action . '.php'); //指定されたファイルを読み込む
include('src/views/bs4_footer.php');;  
?>