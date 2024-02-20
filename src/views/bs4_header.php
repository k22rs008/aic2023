<?php
$app_name = '総合機器センター機器設備予約システム (テスト運用中)';
?>
<!DOCTYPE html>
<html lang="jp">
<head>
 <title><?=$app_name?></title>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- Meta, title, CSS, favicons, etc. -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!-- Bootstrap -->
<link href="/ksu/ogr2023/public/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" href="css/vis-timeline-graph2d.min.css">

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="js/vis-timeline-graph2d.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"></script>

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
 <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
 <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<div class="container">
<!-- page header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded">
 <a class="navbar-brand" href="index.php"><?=$app_name?></a>
 <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample09" aria-controls="navbarsExample09" aria-expanded="false" aria-label="Toggle navigation">
 <span class="navbar-toggler-icon"></span>
 </button>

 <div class="collapse navbar-collapse">
 <ul class="navbar-nav mr-auto">
 <li class="nav-item active">
 <a class="nav-link" href="index.php"><i class="material-icons">home</i><span class="sr-only">(current)</span></a>
 </li></ul>
 <ul class="navbar-nav">
 <li class="navbar-brand text-muted"></li>
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
    echo  '<li class="nav-item"><a class="nav-link" href="?do=' . $action . '">' . $label . '</a></li>' ;
  }

  echo  '<li class="nav-item"><a class="nav-link" href="?do=sys_logout">ログアウト</a></li>' ;

}else{
  echo  '<li class="nav-item"><a class="nav-link" href="?do=sys_login">ログイン</a></li>' ;
}
?>
</ul>
</div>
</nav>
<div class="container">