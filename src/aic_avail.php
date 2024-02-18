<?php
require_once('db_config.php');
include ('lib/func.php');
include ('models/reservation.php');

if (isset($_GET['date'])) {
    $selectedDate = $_GET['date'];
} else {
  // デフォルトの日付を設定（必要に応じて変更）
  // $selectedDate = date("Y-m-d");
  $selectedDate = '2023-11-27';
}
$jpdate = jpdate($selectedDate);//和暦日付

$end_selectedDate = date("Y-m-d", strtotime("+1 days", strtotime($selectedDate)));
$str_selectedDate = strval($selectedDate);
$str_end_selectedDate = strval($end_selectedDate);
// 以降のコードで $selectedDate を使用して予約情報を表示する

$facility_id = $_GET['facility_id'];
//echo $facility_id;
$sql = "SELECT * FROM tbl_facility WHERE id = '{$facility_id}'";
$rs = $conn->query($sql);
if (!$rs) die('エラー: ' . $conn->error);
$row= $rs->fetch_assoc();
//print_r($row);
$fname = $row['fname'];
echo '<p><img src="img/facility/'. $row['id'] .'.webp" height="240" class="img-rounded"></p>';
echo '<h3 class="bg-info">'. $fname .'</h3>';
echo '<p>' .$row['note'].'</p>';

$sql = "SELECT r.*, u.uname AS master_name FROM tbl_reserve_test r, tbl_user u  
  WHERE stime>='{$str_selectedDate} 0:00' AND etime<='{$str_selectedDate} 23:59' 
  AND facility_id={$facility_id} AND u.uid=r.master_user ORDER BY stime";
$rs = $conn->query($sql);
if (!$rs) die('エラー: ' . $conn->error);
$row_cnt = $rs->num_rows; 
if ($row_cnt) {
  echo '<h4>' . $fname . ' の予約一覧 (' . $selectedDate . ')</h4>';
  echo '<table class="table table-boxed">';
  echo  '<tr><th>開始年月日</th><th>終了年月日</th>';
  echo  '<th>開始時刻</th><th>終了時刻</th>';
  echo  '<th>責任者</th><th>目的</th><th>承認</th>';
  //echo  '<th>予約</th>';
  echo  '</tr>';

  while ($row=$rs->fetch_assoc()) {
      echo  '<tr>';
      echo  '<td>' . date("Y-m-d", strtotime($row['stime'])) . '</td>';
      echo  '<td>' . date("Y-m-d", strtotime($row['etime'])) . '</td>';
      echo  '<td>' . date("H:i", strtotime($row['stime'])) . '</td>';
      echo  '<td>' . date("H:i", strtotime($row['etime'])) . '</td>';
      echo  '<td>' . $row['master_name'] . '</td>';
      echo  '<td>' . $row['purpose'] . '</td>';
      echo  '<td>' . ($row['decided'] == 1 ? '承認済み' : ($row['decided'] == 2 ? '拒否' : '未承認')) . '</td>';
      //echo  '<td><a href="?do=rsv_input" class ="btn btn-secondary">予約</a></td>';
      echo  '</tr>';
  }
  echo  '</table>';
}else{
  // 予約がない場合、予約ボタンを表示
  echo '<p class="bg-info">' . $fname. ' ' . $jpdate . ' の予約はありません</p>';
  //echo '<a href="?do=rsv_input&facility_id='. $facility_id .'&date=' . $selectedDate . '"><button onclick="reserveEquipment(' . $facility_id . ', \'' . $selectedDate . '\')" class = btn btn-secondary>予約する</button></a>';
}

// 予約情報をデータベースに追加する関数
function reserveEquipment($facilityID, $selectedDate) {
    // 予約の可否を確認する処理を実装
    // 例: 時間が被らないかどうかをチェックし、データベースに新しい予約を追加
    // この部分にデータベース操作やエラーハンドリングが必要
}

// 予約の可否を判断する関数
function checkReservationAvailability($facilityID, $selectedDate) {
    // 予約の可否を確認する処理を実装
    // 例: 時間が被っていないかどうかをチェック
    // この部分にデータベース操作が必要
}

// 既存の予約と時間が被らないかどうかを確認する関数
function isTimeSlotAvailable($facilityID, $selectedDate, $startTime, $endTime) {
    // 既存の予約情報を取得し、時間が被らないかどうかを確認する処理を実装
    // この部分にデータベース操作が必要
}

function groupReservationsByDate($reservations, $selectedDate, $facilityID)
{
    $groupedReservations = [];

    foreach ($reservations as $reservation) {
        $reserveDate = date("Y-m-d", strtotime($reservation['stime']));

        if ($reserveDate == $selectedDate & $reservation['facility_id'] == $facilityID) {
            $groupedReservations[] = $reservation;
        }
    }

    return $groupedReservations;
}
   
  // $sql = "SELECT * FROM tbl_reserve_test WHERE facility_id = '{$facility_id}'";
  $sql = "SELECT r.*, u.uname AS master_name FROM tbl_reserve_test r, tbl_user u  
  WHERE stime>='{$str_selectedDate} 0:00' AND etime<='{$str_selectedDate} 23:59' 
  AND facility_id={$facility_id} AND u.uid=r.master_user ORDER BY stime";
  $rs = $conn->query($sql);
  if (!$rs) die('エラー: ' . $conn->error);

  $data = $rs->fetch_all();
  //echo '<pre>';print_r($data); echo '</pre>';
/*  
  for ($i=0; $i<count($data); $i++){
    $s = new DateTimeImmutable($data[$i][5]);
    $e = new DateTimeImmutable($data[$i][6]);
    $data[$i][5] = $s->format('H:i');
    $data[$i][6] = $e->format('H:i');
  }
*/
  $json_row = json_encode($data);
    
  $sql_f = "SELECT * FROM tbl_facility WHERE id='{$facility_id}'";
  $rs_f = $conn->query($sql_f);
  if (!$rs_f) die('エラー: ' . $conn->error);
  $data_f= $rs_f->fetch_all();
  $json_row_f = json_encode($data_f);
  
  #print_r($json_row);
  #print_r($json_row_f);
  
  #print_r($selectedDate);
  #print_r($end_selectedDate);
  $selected_time = strtotime($selectedDate);
  $p7 = date("Y-m-d", strtotime("-7 days", $selected_time));
  $p1 = date("Y-m-d", strtotime("-1 days", $selected_time));
  $n1 = date("Y-m-d", strtotime("+1 days", $selected_time));
  $n7 = date("Y-m-d", strtotime("+7 days", $selected_time));
?>
<div class="text-left">
  <a href="?do=aic_avail&facility_id=<?=$facility_id?>&date=<?=$p7?>" class="btn btn-primary">1週間前</a>
  <a href="?do=aic_avail&facility_id=<?=$facility_id?>&date=<?=$p1?>" class="btn btn-primary">前の日</a>
  <a href="?do=aic_avail&facility_id=<?=$facility_id?>&date=<?=$n1?>" class="btn btn-primary">次の日</a>
  <a href="?do=aic_avail&facility_id=<?=$facility_id?>&date=<?=$n7?>" class="btn btn-primary">1週間後</a>
</div>
<div id="visualization"></div>
<div>
  <a href="?do=rsv_input&facility_id=<?=$facility_id?>&date=<?=$selectedDate?>"
   class="btn btn-success">予約する</a>
  <a href="?do=aic_list" class="btn btn-primary">　戻る　</a>
</div>
<br/><br/><br/><br/>

<script type = "text/javascript">
    const container = document.getElementById('visualization');

    let js_row = <?php echo $json_row;?>;
    let js_row_f = <?php echo $json_row_f;?>;
    let s_time = "<?=$selectedDate.' 00:00'?>";
    let e_time = "<?=$selectedDate.' 23:59'?>";
    //console.log(js_row);
    //console.log(js_row_f);
    //console.log(js_row.length);
    
    
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
        
      data_tmp.group = js_row[i][1];
      data_tmp.start = js_row[i][6];
      data_tmp.end = js_row[i][7];
      
      //console.log(data_tmp);
      items.push(data_tmp);
    }
    
    for (let i = 0; i < items.length; i++) {
      const o = items[i];
      o.id = i + 1;     // itemにidを付ける(推奨)
      o.title = (o.start);  // itemのtooltipで表示されるタイトル
      console.log;
      if (o.end) {
        o.title = `${o.start} - ${o.end}`;
      }
    }

    let btn1 = document.createElement("button");
    btn1.innerHTML = "走査型電子顕微鏡";
    
    const groups = [];

    for (let i = 0; i < js_row_f.length; i++) {
      const data_tmp = {};
      data_tmp.id = js_row_f[i][0];
      data_tmp.content = js_row_f[i][1];
      console.log(data_tmp);
      groups.push(data_tmp);
    }

    btn1.onclick = buttonClick1;

    function buttonClick1(){
      location.href = "?do=aic_avail&facility_id=1";
    }
    
    const options = {
      start: s_time,  // timeline軸が表す期間の範囲の開始日
      end:   e_time,    // （同）範囲の終了日
      width: '100%',  //timelineの表示
      horizontalScroll: false,
      zoomable: false,  
      moveable: false,    // timeline chartのzoomを有効にする
      orientation: 'top',   // timeline軸(見出し行）を上側に表示する
      showCurrentTime: false,
      tooltip: {
        delay: 50           // tooltipが表示されるまでのdelay(ms)
      },
      format: {
        minorLabels: {
          //millisecond:'SSS',
          //second:     'sss',
          minute:     'HH:mm',
          hour:       'HH:mm',
          weekday:    'ddd D',
          day:        'MMM/DD',
          month:      'YYYY年MMM月',
          year:       'YYYY'
          /*
          day: 'YYYY MM DD',  // 日付の表示フォーマットを設定
          weekday:'ddd D'
          */
        },
      },
      stack: false,          // イベントが重なった場合にスタックしない
    };

    // Create a Timeline
    const timeline = new vis.Timeline(container, items, groups, options);
    //document.write(js_row['stime']);
    
</script>