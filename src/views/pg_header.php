<!DOCTYPE html>
<html lang="ja"><head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>総合機器センター予約システム(テスト運用中)</title>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" href="css/vis-timeline-graph2d.min.css">

<!-- <link rel="stylesheet" href="css/timetable.css"> -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js'></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script src="js/vis-timeline-graph2d.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"></script>

</head>
<body>
<div class="navbar navbar-inverse bg-primary">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">ナビゲーションの切替</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php">総合機器センター機器設備予約システム (テスト運用中)</a>  
    </div>  <!-- /.navbar-header -->
    <div class="navbar-collapse collapse">
    <ul class="nav navbar-nav navbar-right">
<?php
if (isset($_SESSION['urole'])){
  $menu = array();
  if (($_SESSION['urole']==1 )||($_SESSION['urole']==5)){  //利用者
    $menu = array(   //申請者メニュー
      //'機器設備一覧'  => 'eps_grade',
      '空き状況一覧'  => 'aic_list',
      '利用申請一覧'  => 'rsv_list',
    );
  }

  if($_SESSION['urole']==9) { //管理者
    $menu = array(   //管理者メニュー
      //'利用者一覧'  => '',
      //'機器設備一覧'  => '',
      //'年間スケジュール'  => '',
      //'お知らせ一覧' => '',
      '利用申請一覧' => 'rsv_list',
      //'利用状況集計' => ''
    );
  }

  foreach($menu as $label=>$action){ 
    echo  '<li><a href="?do=' . $action . '">' . $label . '</a></li>' ;
  }

  echo  '<li><a href="?do=sys_logout">ログアウト</a></li>' ;

}else{
  echo  '<li><a href="?do=sys_login">ログイン</a></li>' ;
}
?>
    </ul>
    </div>
  </div>
</div>
<div class="container">