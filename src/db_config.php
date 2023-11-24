<?php
  $conn = new mysqli("localhost", "root", "", "aic_test");//＜開発時の環境設定＞
  if ($conn->connect_errno) die($conn->connect_error);
  $conn->set_charset('utf8'); //文字コードをutf8に設定（文字化け対策）
?>