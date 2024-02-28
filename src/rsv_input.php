<?php
require_once('models/Reserve.php');
require_once('models/Instrument.php');
require_once('models/Staff.php');
include_once 'views/Html.php';
include_once 'lib/func.php';

$rsv_id = 0;
if (isset($_GET['id'])){
    $rsv_id = $_GET['id'];
}
$rsv = (new Reserve)->getDetail($rsv_id);
if ($rsv_id==0 and isset($_GET['inst'])){
    $rsv['instrument_id'] = $_GET['inst'];
    $instrument = (new Instrument)->getDetail($rsv['instrument_id']);
    $rsv['instrument_name'] = $instrument['fullname']; 
}
// echo '<pre>';print_r($rsv);echo '</pre>';
foreach($rsv as $key=>$value){
    $$key = $value;
}
$master_sid = isset($rsv['master_member']) ? $rsv['master_member']['sid'] : '';

$staffs = (new Staff)->getOptions();

?>
<h2>総合機器センター機器設備利用申請</h2>
<form method="post" action="?do=rsv_save">
<table class="table table-bordered table-hover">
<input type="hidden" name="id" value="<?=$rsv_id?>">  
<input type="hidden" name="instrument_id" value="<?=$rsv['instrument_id']?>">    
<input type="hidden" name="apply_mid" value="<?=$rsv['apply_member']['id']?>">
<tr><td width="20%" class="text-info">利用申請者</td>
    <td><?=$rsv['apply_member']['ja_name']?></td>
    <td class="text-info">学籍番号</td>
    <td colspan="2"><?=$rsv['apply_member']['sid']?></td>
</tr>
<tr><td class="text-info">利用目的</td>
    <td colspan="4"><?=Html::input('text','purpose', $rsv['purpose'])?></td></tr>
<tr><td class="text-info">利用責任者</td>
    <td colspan="4"><?=Html::select($staffs, 'master_sid', [$master_sid])?></td>
</tr>
<tr><td class="text-info">利用代表者<div class="text-danger"> (学籍番号・職員番号を各欄に一つずつ入力。例: 21LL999)</</td>
    <td class="pt-0 pb-0" colspan="4"><table class="table table-light" width="100%">   
<?php
$n = count($rsv['rsv_member']);
foreach(range(0,2) as $i){
    list($k1, $k2) = [2*$i, 2*$i+1];
    $sid1 = $k1 < $n ? $rsv['rsv_member'][$k1]['sid'] : '';
    $sid2 = $k2 < $n ? $rsv['rsv_member'][$k2]['sid'] : ''; 
    printf('<tr><td>%s</td>', Html::input('text',"rsv_member[]", $sid1 ));
    printf('<td>%s</td></tr>',Html::input('text',"rsv_member[]", $sid2 ));
}
?>
    </table></td>
</tr>
<tr><td class="text-info">その他利用者</td>
    <td colspan="4"><?= Html::input('text', 'other_user', $rsv['other_user'])?></td>
</tr>
<tr><td class="text-info">希望利用機器</td>
    <td colspan="4"><?=$instrument_name?></td>
</tr>
<tr><td class="text-info">希望利用日時</td>
    <td colspan="2"><?= Html::input('datetime-local', 'stime', $rsv['stime'])?></td>
    <td colspan="2"><?= Html::input('datetime-local', 'etime', $rsv['etime'])?></td>
</tr>
<tr><td class="text-info">試料名</td>
    <td colspan="4"><?= Html::input('text', 'sample_name', $rsv['sample_name']) ?></td>
</tr>
<tr><td class="text-info">状態</td>
    <td colspan="4"><?= Html::select(KsuCode::SAMPLE_STATE,'sample_state',[$rsv['sample_state']], 'radio') ?></td>
</tr>
<tr><td class="text-info">特性</td>
    <td colspan="3"><?= Html::select(KsuCode::SAMPLE_NATURE,'rsv_sample[]',$rsv['sample_nature'], 'checkbox') ?></td>
    <td><?= Html::input('text', 'sample_other', $rsv['sample_other'], 'placeholder="「その他」の内容"')?></td>
</tr>
<tr>
    <td class="text-info">X線取扱者登録の有無</td><td><?= Html::select(KsuCode::YESNO,'xray_chk',[$xray_chk], 'radio') ?></td>
    <td class="text-info">登録者番号</td><td colspan="2"><?= Html::input('text', 'xray_num')?></td>
</tr>
<tr><td class="text-info">備考</td>
    <td colspan="4"><?= Html::textarea('memo', $memo, 'class="form-control" rows="4"')?></td>
</tr>
</table>
<div class="pb-5 mb-5">
<button type="submit" class="btn btn-outline-primary m-1">保存</button>
<?php
if ($rsv_id > 0){
    echo '<a href="?do=rsv_detail&id='.$rsv_id.'" class="btn btn-outline-info m-1">戻る</a>';
}else{
    echo '<a href="?do=rsv_list" class="btn btn-outline-info m-1">戻る</a>';
}
?>
</div>
</form>
