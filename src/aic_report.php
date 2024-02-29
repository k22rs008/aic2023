<?php
namespace aic;

use aic\models\Reserve;
use aic\models\Util;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$year = date('Y');
$month = date('m');
if (isset($_GET['y'])){
  $year = $_GET['y'];
}
if (isset($_GET['m'])){
  $month = $_GET['m'];
}
$time = mktime(0,0,0,$month, 1, $year);
$days = date('t');
$head = ['機器名', '設置場所'];
for($day=1; $day<=$days; $day++){
  $dt = sprintf('%d-%02d-%02d', $year, $month, $day);
  $head[] = Util::jpdate($dt,false, false);//和暦日付(年無し・時間なし)
}
echo '<pre>';print_r($head);echo '</pre>';
$date1 = date('Y-m-1 00:00', $time);
$date2 = date('Y-m-t 23:59', $time);
// echo $date1, $date2;

$rows = (new Reserve)->getListByInst(0, $date1, $date2);

// foreach ($rows as $row){
//   print_r(Util::array_slice_by_index($row, ['fullname','shortname','stime','etime','apply_name','master_name']));
// }
/*
foreach ($report as $year=>$months) {
  foreach ($months as $month=>$days){
    $data[] = [$year.'年'.$month.'月',null,null,null,null,null,];
    $data[] = ['日付(曜日)','行事名','使用団体','団体区分','使用時間','備考',];
    foreach ($days as $day){
      $data[] = [
        $day['date_ja'],
        isset($day['event']) ? $day['event']['name'] : '',
        isset($day['event']) ? $day['event']['group_name'] : '',
        isset($day['event']) ? $day['event']['group_type'] : '',
        isset($day['event']) ? $day['event']['use_time'] : '',
        isset($day['event']) ? $day['event']['memo'] : '',
      ]; 
    }
  }
}

$filename = sprintf("Report%02d_%04d.xlsx", $id, $year);
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
foreach ($data as $rowNum => $rowData) {
  $worksheet->fromArray($rowData, null, 'A' . ($rowNum + 1));
}
$writer = new Xlsx($spreadsheet);    
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
header("Content-Disposition: attachment; filename=\"{$filename}\"");
header('Cache-Control: max-age=0');
ob_end_clean();//IMPORTANT for prevending file crash
$writer->save('php://output');
*/