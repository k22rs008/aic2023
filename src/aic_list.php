<?php
require_once('db_config.php');
  
if (isset($_GET['date'])) {
    $selectedDate = $_GET['date'];
    } else {
        // デフォルトの日付を設定（必要に応じて変更）
        $selectedDate = date("Y-m-d");
    }

  $end_selectedDate = date("Y-m-d", strtotime("+1 days", strtotime($selectedDate)));
  
  //予約テーブル読み込み
  $sql = "SELECT * FROM tbl_reserve_test";
  $rs = $conn->query($sql);
  if (!$rs) die('エラー: ' . $conn->error);

  $row= $rs->fetch_all();
  $json_row = json_encode($row);
  //機器テーブル読み込み  
  $sql_f = "SELECT * FROM tbl_facility";
  $rs_f = $conn->query($sql_f);
  if (!$rs_f) die('エラー: ' . $conn->error);
  $row_f= $rs_f->fetch_all();
  $json_row_f = json_encode($row_f);
  

  $flg = 0;
  if(isset($_SESSION['urole'])){
    if($_SESSION['urole'] == 9){
      $flg = true;
    }
  }
  //print_r($json_row);
  //print_r($json_row_f);
  
  
  //print_r($selectedDate);
  //print_r($end_selectedDate);
?>

<div id="visualization"></div>

<script type = "text/javascript">
    const container = document.getElementById('visualization');

    let js_row = <?php echo $json_row;?>;
    let js_row_f = <?php echo $json_row_f;?>;
    let s_time = <?php echo "'".$selectedDate."'";?>;
    let e_time = <?php echo "'".$end_selectedDate."'";?>;
    let flg = <?php echo $flg;?>;
    console.log(js_row_f);
    console.log(js_row_f);
    console.log(js_row[0][6]);
    console.log(js_row.length);
    const items = [];
    for (let i = 0; i < js_row.length; i++) {
      const data_tmp = {};

      if (js_row[i][11] == 0){
        data_tmp.className = 'red';
        data_tmp.decided = '申請';
      }else if (js_row[i][11] == 1){
        data_tmp.className = 'green';
        data_tmp.decided = '保留';
      }else {
        data_tmp.className = 'blue';
        data_tmp.decided = '承認';
      }
      data_tmp.id = js_row[i][0];
      data_tmp.group = js_row[i][1];
      data_tmp.name = js_row[i][2];
      data_tmp.start = js_row[i][6];
      data_tmp.end = js_row[i][7];
      console.log(data_tmp);
      items.push(data_tmp);
    }
    moment.locale('ja');
    for (let i = 0; i < items.length; i++) {
      const o = items[i];
      o.title = `${moment(o.start).format('MM月DD日HH:mm')} ~ ${moment(o.end).format('MM月DD日HH:mm')} <br>申請者:${o.name} 状態:${o.decided}`;
      }

    let btn1 = document.createElement("button");
    btn1.innerHTML = "走査型電子顕微鏡";

    const groups = [];

    //タイムテーブル内の機器名からリンク先を出力
    for (let i = 0; i < js_row_f.length; i++) {
      const data_tmp = {};
      data_tmp.id = js_row_f[i][0];
      data_tmp.content =  '<a href=\"<?=$_SERVER["PHP_SELF"]?>?do=aic_avail&facility_id=' + js_row_f[i][0] + '\">' + js_row_f[i][1] + '</a> ';
      //data_tmp.content = js_row_f[i][1];
      
      console.log(data_tmp);
      groups.push(data_tmp);
    }

    btn1.onclick = buttonClick1;

    function buttonClick1(){
      location.href = "?do=aic_avail&facility_id=1";
    }
    
    const options = {
      start: s_time,  // timeline軸が表す期間の範囲の開始日
      end: e_time,    // （同）範囲の終了日
      width: '105%', //timelineの表示
      horizontalScroll: true,
      zoomable: false,  
      moveable: false,    // timeline chartのzoomを有効にする
      orientation: 'top',   // timeline軸(見出し行）を上側に表示する
      showCurrentTime: false,
      stack: true,

      timeAxis: {scale: 'hour', step: 1},
      format: {
        minorLabels: {
          //millisecond:'SSS',
          //second:     'sss',
          minute:     'HH:mm',
          hour:       'HH:mm',
          weekday:    'ddd D',
          day:        'MMM DD',
          month:      'MMM/YYYY',
          year:       'YYYY'
          /*
          day: 'YYYY MM DD',  // 日付の表示フォーマットを設定
          weekday:'ddd D'
          */
        },
      },
      //tack: false,          // イベントが重なった場合にスタックしない
    };
  document.getElementById('visualization').onclick = function (event) {
  var props = timeline.getEventProperties(event)
  
  if(props['item'] != null & flg ){
    //console.log(props);
    window.location.href = `?do=eps_decide_DBtest_alt&id=${props['item']}`;
    //console.log(props);
  }
  }
    // Create a Timeline
    const timeline = new vis.Timeline(container, items, groups, options);
    //document.write(js_row['stime']);
    
</script>