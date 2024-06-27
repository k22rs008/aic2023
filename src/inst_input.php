<?php
namespace aic;

use aic\models\Instrument;
use aic\models\Room;
use aic\models\Security;
use aic\models\KsuCode;

use aic\views\Html;


(new Security)->require('admin');

$inst_id = isset($_GET['id']) ? $_GET['id'] : 0;;
$status = $category = $room_id = 1;
$id = $code = "";
$fullname = $shortname = $purpose = $maker = $model = $detail = $memo = "";

$rooms = (new Room)->getListItems();

$row = (new Instrument)->getDetail($inst_id);
if ($row) { 
    foreach (array_keys($row) as $key){
        $$key = $row[$key];
    }
}
$url = "img/instrument/{$inst_id}.webp";
if (!@GetImageSize($url)){
    $url = 'img/dummy-image-square1.webp' ; 
}
?>
$url .= '?' . time();
<img src="<?=$url?>" height="240" width="320" class="m-1 rounded">
<form method="post" action="?do=inst_save" enctype="multipart/form-data">
<?= Html::input('hidden', 'id', $inst_id)?>
<table class="table table-hover">
<tr><th width="20%">機器ID</th><td>
<?= Html::input('number', 'code', $id, 'class="form-control"  disabled')?></td></tr>
<tr><th width="20%">機器名称</th><td><?= Html::input('text','fullname', $fullname)?></td></tr>
<tr><th>略称</th><td><?= Html::input('text','shortname', $shortname)?></td></tr>
<tr><th>主な用途</th><td><?= Html::input('text','purpose', $purpose)?></td></tr>
<tr><th>機器状態</th><td><?= Html::select(KsuCode::INST_STATE,'state',[$state],'radio')?></td></tr>
<tr><th>カテゴリ</th><td><?= Html::select(KsuCode::INST_CATEGORY,'category',[$category],'radio')?></td></tr>
<tr><th>メーカー</th><td><?= Html::input('text','maker', $maker)?></td></tr>
<tr><th>型式</th><td><?= Html::input('text','model', $model)?></td></tr>
<tr><th>導入年月</th><td><?= Html::input('date','bought_year', $bought_year)?></td></tr>
<tr><th>設置場所</th><td><?= Html::select($rooms,'room_id', [$room_id])?></td></tr>
<tr><th>詳細</th><td><?= Html::textarea('detail', $detail, 'class="form-control" rows="4"')?></td></tr>
<tr><th>備考</th><td><?= Html::textarea('memo', $memo, 'class="form-control" rows="4"')?></td></tr>
<tr><th width="20%">写真ファイル</th><td>  
<input type="file" class="form-control-file border" name="imgfile"></td></tr>
</table>
<div class="pb-5 mb-5"><button type="submit" class="btn btn-outline-primary m-1">保存</button>
<?php
if ($inst_id > 0){
    echo '<a href="?do=inst_detail&id='.$inst_id.'" class="btn btn-outline-info m-1">戻る</a>';
}else{
    echo '<a href="?do=inst_list" class="btn btn-outline-info m-1">戻る</a>';
}
?>
</div>
</form>
