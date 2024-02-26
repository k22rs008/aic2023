<?php
require_once('models/Reserve.php');
require_once('models/Instrument.php');
include 'views/Html.php';
include 'lib/func.php';

$rsv_id = 0;
if (isset($_GET['id'])){
  $rsv_id = $_GET['id'];
}
$rsv= (new Reserve)->getDetail($rsv_id);
// echo '<pre>';
// print_r($rsv);
// echo '</pre>';

?>
<h3>機器設備利用申請内容詳細</h3>
<table class="table table-bordered table-hover">
<tr><td>利用申請者</td>
    <td><?=$rsv['apply_uname']?></td>
    <td>学籍番号</td>
    <td colspan="2">21LL001</td>
</tr>
<tr><td>利用責任者氏名</td>
    <td><?=$rsv['master_uname']?></td>
    <td>学部学科</td>
    <td>生命科学部 生命科学科</td>
    <td>Tel. 090-5540-0862</td>
</tr>
<tr><td>利用代表者氏名</td><td class="pt-0 pb-0" colspan="4">
<table class="table table-light" width="100%">
<?php
foreach($rsv['rsv_member'] as $row){
    printf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>', $row['sid'], $row['ja_name'], $row['tel_no']);
}
?>
</table>
</td></tr>
<tr><td>その他利用者</td><td colspan="4"></td>
</tr>
<tr><td>教職員人数</td><td>1人</td>
    <td>学生人数</td><td colspan="2">2人</td>
</tr>
<tr><td>希望利用機器</td><td colspan="4"><?=$rsv['instrument_name']?></td>
</tr>
<tr><td>希望利用日時</td><td colspan=4><?=jpdate($rsv['stime'],true)?>～<?=jpdate($rsv['etime'],true)?></td>
</tr>
<tr><td>試料名</td><td colspan=4><?=$rsv['sample_name']?></td>
</tr>
<tr><td>状態</td><td colspan=4>固体</td>
</tr>
<tr><td>特性</td><td colspan=4>爆発性</td>
</tr>
<tr><td>X線取扱者登録の有無</td><td>無</td>
    <td>登録者番号</td><td colspan=2></td>
</tr>
<tr><td>備考</td><td colspan=4></td>
</tr>
</table>