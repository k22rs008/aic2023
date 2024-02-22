<?php
include 'models/Facility.php';
include 'views/Html.php';

$fid = isset($_GET['id']) ? $_GET['id'] : 0;;
$f_status = Facility::status; 
$f_category = Facility::category;
$status = $category = 1;
$id = $code = "";
$fname = $fshortname = $maker = $model = "";
$splace = $place_no = $detail = $memo = "";
$row = (new Facility)->getDetail($fid);
if ($row) { 
    foreach (array_keys($row) as $key){
        $$key = $row[$key];
    }
}
$url = "img/facility/{$fid}.webp";
if (!@GetImageSize($url)){
    $url = 'img/dummy-image-square1.webp' ; 
}
echo '<img src="'. $url . '" height="240" width="320" class="img-rounded">' . PHP_EOL;
echo '<form method="post" action="?do=inst_save" enctype="multipart/form-data">' . PHP_EOL;
echo Html::input('hidden', 'id', $fid);
echo '<table class="table table-hover">' . PHP_EOL;
echo '<tr><th width="20%">機器ID</th><td>',
  Html::input('number', 'code', $id, 'class="form-control"  disabled'), '<td></tr>';
echo '<tr><th width="20%">機器名称</th><td>', Html::input('text','fname', $fname), '<td></tr>';
echo '<tr><th>略称</th><td>', Html::input('text','fshortname', $fshortname), '<td></tr>';
echo '<tr><th>機器状態</th><td>', Html::select($f_status,'status',[$status],'radio'),'</td></tr>', PHP_EOL;
echo '<tr><th>カテゴリ</th><td>', Html::select($f_category,'category',[$category],'radio'),'</td></tr>', PHP_EOL;
echo '<tr><th>メーカー</th><td>', Html::input('text','maker', $maker), '<td></tr>', PHP_EOL;
echo '<tr><th>型式</th><td>', Html::input('text','model', $model), '<td></tr>', PHP_EOL;
echo '<tr><th>導入年月</th><td>', Html::input('date','iyear', $iyear), '<td></tr>', PHP_EOL;
echo '<tr><th>設置場所</th><td>', Html::input('text','splace', $splace), '<td></tr>', PHP_EOL;
echo '<tr><th>場所番号</th><td>', Html::input('text','place_no', $place_no), '<td></tr>', PHP_EOL;
echo '<tr><th>詳細</th><td>', Html::textarea('detail', $detail, 'class="form-control" rows="4"'), '</td></tr>', PHP_EOL;
echo '<tr><th>備考</th><td>', Html::textarea('memo', $memo, 'class="form-control" rows="4"'), '</td></tr>', PHP_EOL;
echo '<tr><th width="20%">写真ファイル</th><td>',  
 '<input type="file" class="form-control-file border" name="imgfile">', '</td></tr>', PHP_EOL;
echo '</table>';
echo '<div class="pb-5 mb-5">' . PHP_EOL . 
  '<button type="submit" class="btn btn-outline-primary m-1">保存</button>' . PHP_EOL;
if ($fid > 0){
    echo '<a href="?do=inst_detail&id='.$fid.'" class="btn btn-outline-info m-1">戻る</a>';
}else{
    echo '<a href="?do=inst_list" class="btn btn-outline-info m-1">戻る</a>';
}
echo '</div>';
echo '</form>';
