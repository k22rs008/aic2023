<?php
require_once('models/Reserve.php');
require_once('models/Instrument.php');
include 'views/Html.php';
include 'lib/func.php';

$sample_states = KsuCode::SAMPLE_STATE;
$sample_natures = KsuCode::SAMPLE_NATURE;
$yesno = KsuCode::YESNO;

$rsv_id = 0;
if (isset($_GET['id'])){
  $rsv_id = $_GET['id'];
}
$rsv= (new Reserve)->getDetail($rsv_id);
foreach($rsv as $key=>$value){
    $$key = $value;
}
echo '<pre>';
print_r($rsv);
echo '</pre>';
?>
<h2>総合機器センター機器設備利用申請</h2>
<form method="post" action="?do=rsv_save">
<table class="table table-bordered table-hover">
<tr><td>利用申請者</td>
    <td><?=$rsv['apply_uname']?></td>
    <td>学籍番号</td>
    <td colspan="2">21LL001</td>
</tr>
<tr><td>利用責任者氏名</td>
    <td><?=Html::input('text', 'master_mid', $rsv['master_mid'])?></td>
    <td>学部学科</td>
    <td>生命科学部 生命科学科</td>
    <td>Tel. 090-5540-0862</td>
</tr>
<tr><td>利用代表者氏名</td><td class="pt-0 pb-0" colspan="4">
<table class="table table-light" width="100%">
<?php
foreach(range(0,2) as $i){
    list($k1, $k2) = [2*$i,2*$i+1];
    printf('<tr><td>%s</td>',Html::input('text',"rsv_member[$k1]", $k1, 'placeholder="(例)21LL999"' ));
    printf('<td>%s</td></tr>',Html::input('text',"rsv_member[$k2]", $k2,'placeholder="(例)21LL999"' ));
}
?>
</table>
</td></tr>
<tr><td>その他利用者</td><td colspan="4"><?= Html::input('text', 'other_users', '')?></td>
</tr>
<tr><td>教職員人数</td><td>1人</td>
    <td>学生人数</td><td colspan="2">2人</td>
</tr>
<tr><td>希望利用機器</td><td colspan="4"><?=$instrument_name?></td>
</tr>
<tr><td>希望利用日時</td>
<td colspan="2"><?= Html::input('datetime-local', 'stime',$stime)?></td>
<td colspan="2"><?= Html::input('datetime-local', 'etime',$etime)?></td>
</tr>
<tr><td>試料名</td><td colspan="4"><?=$rsv['sample_name']?></td>
</tr>
<tr><td>状態</td><td colspan="4"><?= Html::select($sample_states,'sample_state',[$sample_state], 'radio') ?></td>
</tr>
<tr><td>特性</td><td colspan="3"><?= Html::select($sample_natures,'sample_natue[]',[$sample_state], 'checkbox') ?></td>
<td><?= Html::input('text', 'sample_other', '', 'placeholder="「その他」の内容"')?></td>
</tr>
<tr>
<td>X線取扱者登録の有無</td><td><?= Html::select($yesno,'xray_chk',[$xray_chk], 'radio') ?></td>
<td>登録者番号</td><td colspan="2"><?= Html::input('text', 'xray_num')?></td>
</tr>
<tr><td>備考</td><td colspan="4"><?= Html::textarea('memo', $memo, 'class="form-control" rows="4"')?></td>
</tr>
</table>
<div class="pb-5 mb-5"><button type="submit" class="btn btn-outline-primary m-1">保存</button>
<?php
if ($rsv_id > 0){
    echo '<a href="?do=rsv_detail&id='.$rsv_id.'" class="btn btn-outline-info m-1">戻る</a>';
}else{
    echo '<a href="?do=rsv_list" class="btn btn-outline-info m-1">戻る</a>';
}
?>
</form>