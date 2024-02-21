<?php
include 'models/facility.php';
include 'views/Html.php';

$f_id = isset($_GET['id']) ? $_GET['id'] : 0;;
$f_status = Facility::status; 
$f_category = Facility::category;
$status = $category = 1;
$id = $code = "";
$fname = $fshortname = $maker = $model = "";
$splace = $place_no = $detail = $memo = "";
$row = (new Facility)->getDetail($f_id);
if ($row) { 
    foreach (array_keys($row) as $key){
        $$key = $row[$key];
    }
}
$url = "img/facility/{$f_id}.webp";
if (!@GetImageSize($url)){
    $url = 'img/dummy-image-square1.webp' ; 
}
echo '<img src="'. $url . '" height="240" width="320" class="img-rounded">' . PHP_EOL;

echo '<form method="post" action="?do=inst_save" enctype="multipart/form-data">' . PHP_EOL;
echo '<input type="hidden" name="id" value="', $f_id, '">';
echo '<table class="table table-hover">' . PHP_EOL;
echo '<tr><th width="20%">機器ID</th><td>',  
 '<input type="number" class="form-control"  disabled name="code" value="', $id, '"></td></tr>' . PHP_EOL;
echo '<tr><th width="20%">機器名称</th><td>', 
 '<input type="text" class="form-control" name="fname" value="', $fname, '"></td></tr>' . PHP_EOL;
echo '<tr><th>略称</th><td>', 
 '<input type="text" class="form-control" name="fshortname" value="', $fshortname, '"></td></tr>' . PHP_EOL;
echo '<tr><th>機器状態</th><td>' , Html::select($f_status,'radio','status', [$status]),'</td></tr>' . PHP_EOL;
echo '<tr><th>カテゴリ</th><td>' , Html::select($f_category,'radio','category', [$category]),'</td></tr>' . PHP_EOL;
echo '<tr><th>メーカー</th><td>', 
'<input type="text" class="form-control" name="maker" value="', $maker. '"></td></tr>' . PHP_EOL;
echo '<tr><th>型式</th><td>' ,
'<input type="text" class="form-control" name="model" value="', $model. '"></td></tr>' . PHP_EOL;
echo '<tr><th>導入年月</th><td>',
'<input type="date" class="form-control" name="iyear" value="', $iyear, '"></td></tr>' . PHP_EOL;
echo '<tr><th>設置場所</th><td>' ,
'<input type="text" class="form-control" name="splace" value="', $splace. '"></td></tr>' . PHP_EOL;
echo '<tr><th>場所番号</th><td>' ,
'<input type="text" class="form-control" name="place_no" value="', $place_no. '"></td></tr>' . PHP_EOL;
echo '<tr><th>詳細</th><td>',
'<textarea class="form-control" name="detail" rows="4">', $detail, '</textarea></td></tr>' . PHP_EOL;
echo '<tr><th>備考</th><td>',
'<textarea class="form-control" name="memo" rows="4">', $memo, '</textarea></td></tr>' . PHP_EOL;
echo '<tr><th width="20%">写真ファイル</th><td>',  
 '<input type="file" class="form-control-file border" name="imgfile">', '</td></tr>' . PHP_EOL;
echo '</table>';
echo '<button type="submit" class="btn btn-primary mr-2">保存</button>' . PHP_EOL;
if ($f_id > 0){
    echo '<a href="?do=inst_detail&id='.$f_id.'" class="btn btn-info">戻る</a>';
}else{
    echo '<a href="?do=inst_list" class="btn btn-info">戻る</a>';
}
echo '</form>';
