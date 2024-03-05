<?php
namespace aic;

use aic\models\Member;
use aic\models\KsuCode;

use aic\views\Html;

$category = 0;
if (isset($_POST['category'])){
  $category = $_POST['category'];
  $_SESSION['selected_category'] = $category;
}else if(isset($_SESSION['selected_category'])){
  $category = $_SESSION['selected_category'];
}
echo '<h3>会員一覧</h3>' . PHP_EOL;
echo '<div class="text-left">'. PHP_EOL;
echo '<form method="post" action="?do=mbr_list" class="form-inline">'. PHP_EOL;
echo '<div class="form-group mb-2">'. PHP_EOL;
$options = KsuCode::MBR_CATEGORY;
$options[0] ='～会員種別選択～';
ksort($options);
echo Html::select($options, 'category', [$category]);
echo '</div>'. PHP_EOL;
echo '<div class="form-group mx-sm-3 mb-2">'. PHP_EOL;
echo '<button type="submit" name="s" class="btn btn-outline-primary mt-1 mb-1 mr-1">絞り込み</button>' . PHP_EOL; 
echo '</form>'. PHP_EOL;
echo '</div>' . PHP_EOL;

$page = isset($_GET['page']) ? $_GET['page'] : 1; 
// pagination
$where = ($category==0) ? 1 : 'category='. $category;
$num_rows = (new Member)->getNumRows($where, 'id');
echo Html::pagination($num_rows, KsuCode::PAGE_ROWS, $page);
// end of pagination

$rows= (new Member)->getList($where, 'authority,id', $page);

echo '<table class="table table-hover">';
echo '<tr><th>会員ID</th><th>会員名</th><th>所属</th><th>種別</th><th>電話番号</th><th>予約権</th><th>詳細</th></tr>';
foreach ($rows as $row){ 
    echo '<tr><td>' . $row['sid']. '</td>' . PHP_EOL;
    echo '<td>' . $row['ja_name'] . '</td>' . PHP_EOL;
    echo '<td>' . $row['dept_name'] . '</td>' . PHP_EOL;
    $i = $row['category'];
    echo '<td>' . KsuCode::MBR_CATEGORY[$i] . '</td>' . PHP_EOL;
    echo '<td>' . $row['tel_no'] . '</td>'; 
    $i = $row['authority'];
    echo '<td>' . KsuCode::MBR_AUTHORITY[$i] . '</td>' . PHP_EOL;
    echo '<td>' .
    '<a class="btn btn-sm btn-outline-success" href="?do=mbr_detail&id='.$row['id'].'">詳細</a>' .
    '</td>';
    echo '</tr>' . PHP_EOL;
}
echo '</table>';

echo Html::pagination($num_rows, KsuCode::PAGE_ROWS, $page);
