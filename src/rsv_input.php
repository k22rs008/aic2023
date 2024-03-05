<?php
// namespace aic;

use aic\models\Reserve;
use aic\models\Instrument;
use aic\models\Staff;
use aic\models\Security;
use aic\models\KsuCode;

use aic\views\Html;

(new Security)->require('login');
(new Security)->require('reserve');

$rsv_id = isset($_GET['id']) ? $_GET['id'] :0;
$rsv = (new Reserve)->getDetail($rsv_id);

if (isset($_GET['inst'])){
    $rsv['instrument_id'] = $_GET['inst'];
    $instrument = (new Instrument)->getDetail($rsv['instrument_id']);
    $rsv['instrument_name'] = $instrument['fullname']; 
}

$stime = date('Y-m-d H:i');
if (isset($_GET['d'])){
    $ymd = DateTime::createFromFormat('ymd', $_GET['d']);
    $stime = $ymd->format('Y-m-d H:i');
}

if ($rsv_id == 0){
    $rsv['stime'] = $stime;
    $rsv['etime'] = $stime;
}

foreach($rsv as $key=>$value){
    $$key = $value;
}
$master_sid = isset($rsv['master_member']) ? $rsv['master_member']['sid'] : '';

$staffs = (new Staff)->getOptions('responsible');

?>
<h2>総合機器センター機器設備利用申請</h2>
<form class="needs-validation" method="post" action="?do=rsv_save">
<table class="table table-bordered table-hover">
<input type="hidden" name="id" value="<?=$rsv_id?>">  
<input type="hidden" name="instrument_id" value="<?=$rsv['instrument_id']?>">    
<input type="hidden" name="apply_mid" value="<?=$rsv['apply_member']['id']?>">
<tr><td width="20%" class="text-info">利用申請者</td>
    <td><?=$rsv['apply_member']['ja_name']?></td>
    <td class="text-info">会員番号</td>
    <td colspan="2"><?=$rsv['apply_member']['sid']?></td>
</tr>
<tr><td class="text-info form-group">利用目的※</td>
    <td colspan="4"><?=Html::input('text','purpose', $rsv['purpose'],'required')?></td></tr>
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
<tr><td class="text-info">その他利用者数</td>
    <td><?= Html::input('number', 'other_num', $rsv['other_num'])?></td>
    <td class="text-info">内訳等の説明</td>
    <td colspan="2"><?= Html::input('text', 'other_user', $rsv['other_user'],'placeholder="例：○○株式会社４名、○○学校2名"')?></td>
</tr>
<tr><td class="text-info">希望利用機器</td>
    <td colspan="4"><?=$instrument_name?></td>
</tr>
<tr><td class="text-info form-group">希望利用日時</td>
    <td colspan="2"><?= Html::input('datetime-local', 'stime', $rsv['stime'], 'id="stime"')?></td>
    <td colspan="2"><?= Html::input('datetime-local', 'etime', $rsv['etime'], 'id="etime"')?></td>
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
<script>
const occupied = <?= isset($occupied_periods)?json_encode($occupied_periods):[] ?>; 
$.validator.setDefaults({
  errorClass: "text-danger",
  validClass: "text-success",
  focusCleanup: true,
  highlight : function(element, errorClass, validClass) {
    $(element).closest(".form-group").addClass(errorClass).removeClass(validClass);
  },
  unhighlight : function(element, errorClass, validClass) {
    $(element).closest(".form-group").removeClass(errorClass).addClass(validClass);
  }
});
$( "form" ).validate({
  rules: {
    purpose: "required",
    stime : {
        required: true,
    },
    etime : {
        required : true,
        validateTimePeriod: true,
    },
  },  
  messages: {
    purpose: "利用目的が必須です"
  },
});
var overlaped = function (a1, a2, b1, b2){
    return Math.max(a1, b1)<Math.min(a2,b2);
}
var now = moment(new Date()).format("YYYY/MM/DD HH:mm");
$.validator.addMethod(
    "validateTimePeriod",
    function(value, element) {
        const stime = new Date($('#stime').val());
        const etime = new Date($('#etime').val());
        if (stime > etime) return false;
        var ok = true; 
        occupied.forEach((period)=>{
            var p0 = new Date(period[0]);
            var p1 = new Date(period[1]);
            // console.log(period[0],period[1], 'overlaped?');
            if (overlaped(stime, etime, p0, p1)){
                // console.log(period[0],period[1], 'overlaped');
                ok = false;
                return;
            }
        });
        return ok;
    },
    "有効な期間ではありません。"
  );
</script>
