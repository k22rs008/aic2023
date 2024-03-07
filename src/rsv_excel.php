<?php
namespace aic;

use aic\models\KsuCode;
use aic\models\Reserve;
use aic\models\RsvMember;
use aic\models\Security;
use aic\models\Util;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Align;

(new Security)->require('admin');

foreach (['inst','status','year','month','day'] as $item){
  $key = 'selected_' . $item; // 'selected_??'
  $$item = isset($_SESSION[$key]) ? $_SESSION[$key] : 0;
}
$year = ($year > 0 ) ? $year: date('Y');
$month = ($month > 0 ) ? $month: date('m');
$date1 = $date2 = null;
$time = mktime(0, 0, 0, $month, 1, $year);
$day1 = $day > 0 ? $day : 1;  // one day or one month from day 1
$day2 = $day > 0 ? $day : date('t', $time); // one day or one month until last day
$date1 = sprintf('%d-%d-%d 00:00', $year, $month, $day1); 
$date2 = sprintf('%d-%d-%d 23:59', $year, $month, $day2);
$page = 0; // no pagination

$data[] = [
  '予約番号', '部屋No.', '利用機器名', '開始時刻', '終了時刻', '利用責任者','利用代表者',
  '学生人数','教員人数', 'その他利用者数','その利用者','備考',
];

$rows= (new Reserve)->getListByInst($inst, $date1, $date2, $status, $page);
$reserve_n = count($rows);
foreach ($rows as $row){ //予約テーブルにある予約の数だけ繰り返す
  $date1 = Util::jpdate($row['stime']);
  $date2 = Util::jpdate($row['etime']);
  $time1 = substr($row['stime'], 10,6); // 開始時刻
  $time2 = ($date1==$date2) ? substr($row['etime'], 10,6) : ''; //終了時刻。日をまかがった予約は表示なし
  $rsv_id = $row['id'];
  $rsv_members = (new RsvMember)->getList('reserve_id='.$rsv_id);
  $rsv_names = [];
  foreach ($rsv_members as $member){
    $rsv_names[] = $member['sid'] . '　' . $member['ja_name'];
  } 
  $rsv_names = implode("\n", $rsv_names);
  $students = array_filter($rsv_members, function($a){ return $a['category']==1; });
  $student_n = count($students);
  $staff_n = count($rsv_members) - count($students); 

  $data[] = [ 
    $row['code'], $row['room_no'], 
    $row['shortname'], //利用機器名(省略)を表示
    $time1, $time2,
    $row['master_name'] , //利用代表者氏名を表示
    $rsv_names,
    $student_n, $staff_n,$row['other_num'],$row['other_user'],
    $row['memo'] ,
  ];
}
// echo '<pre>'; print_r($data); echo '</pre>';

$filename = sprintf("Report%s.xlsx", $date1);
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
foreach(range('A','L') as $col){ 
  $worksheet->getColumnDimension($col)->setWidth(12);
}
$worksheet->getColumnDimension('G')->setWidth(24);
$worksheet->getColumnDimension('L')->setWidth(24);
$worksheet->getStyle('G2:G'.($reserve_n+1))->getAlignment()->setWrapText(true);
$worksheet->getStyle('A2:L'.($reserve_n+1))->getAlignment()->setVertical(Align::VERTICAL_CENTER); 
foreach ($data as $rowNum => $rowData) {
  $worksheet->fromArray($rowData, null, 'A' . ($rowNum + 1));
}
$writer = new Xlsx($spreadsheet);    
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
header("Content-Disposition: attachment; filename=\"{$filename}\"");
header('Cache-Control: max-age=0');
ob_end_clean();//IMPORTANT for prevending file crash
$writer->save('php://output');