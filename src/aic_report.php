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
$time = mktime(0, 0, 0, $month, 1, $year);
$days = date('t');
$head1_1 = ['月', '日']; // A1:B1 ... 日付Y/M/D...[C1:N1](10), [O1:Z1](10)...
$head2_1 = ['曜', '日']; // A2:B2 ... 曜日     ...[C2:N2](10), [O2:Z2](10)...
$head3_1 = ['部屋No.', '機器名' ]; // A3, B3   ... 
$head3_2 = [
  '開始時刻',	'終了時刻',	'学部学科',	'利用責任者','学籍番号(学生のみ)',
  '利用代表者氏名',	'教職員人数',	'学生人数',	'備考',	'センター記入欄'
];
/* Data 
 
*/

for($day=1; $day<=$days; $day++){
  $w = date('w', mktime(0, 0, 0, $month, $day, $year));
   
}
echo '<pre>';print_r($head);echo '</pre>';
$date1 = date('Y-m-1 00:00', $time);
$date2 = date('Y-m-t 23:59', $time);

$rows = (new Reserve)->getReport(0, $date1, $date2);
$cols = [
  'stime','etime','dept_code','sid','master_name','staff_n', 'student_n','memo',null
];
// foreach ($rows as $row){
//   print_r(Util::array_slice_by_index($row, $cols));
// }

/*
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

/*
f you have a big piece of data you want to display in a worksheet, or a heading that needs to span multiple sub-heading columns, you can merge two or more cells together, to become one cell. This can be done using the following code:

$spreadsheet->getActiveSheet()->mergeCells('A18:E22');
Removing a merge can be done using the unmergeCells() method:

$spreadsheet->getActiveSheet()->unmergeCells('A18:E22');

https://github.com/PHPOffice/PhpSpreadsheet/blob/master/docs/topics/recipes.md#mergeunmerge-cells

*/