<?php
session_start();
date_default_timezone_set("Asia/Tokyo");

require "vendor/autoload.php";

define ('ENV', 'development'); // 開発テスト時の設定
// define ('ENV', 'deployment'); // 本番運用時の設定
require 'conf/' . ENV . '_env.php';

$requested_action = 'aic_home'; //ホームページ (aic_home)をデフォルト機能とする
if (isset($_GET['do'])) {//index.php?do=に続くパラメータで実行する機能を指定
  $requested_action = $_GET['do'];
}

if (!in_array($requested_action, ['rsv_excel']))
  include('src/views/bs4_header.php');

include('src/' . $requested_action . '.php'); //指定されたファイルを読み込む

if (! in_array($requested_action, ['rsv_excel']))
  include('src/views/bs4_footer.php');  
