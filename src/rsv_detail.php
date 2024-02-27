<?php
require_once('models/Reserve.php');
require_once('models/Instrument.php');
include_once 'views/Html.php';
include_once 'lib/func.php';

$rsv_id = 0;
if (isset($_GET['id'])){
  $rsv_id = $_GET['id'];
}
$rsv= (new Reserve)->getDetail($rsv_id);
// echo '<pre>'; print_r($rsv); echo '</pre>';
// $_natures = array_slice_by_index(KsuCode::SAMPLE_NATURE, $rsv['sample_nature']);
// $sample_nature = implode(', ', $_natures);
// $sample_state = KsuCode::SAMPLE_STATE[$rsv['sample_state']];
// $xray_yesno = KsuCode::YESNO[$rsv['xray_chk']];
// $_dept_code = $rsv['master_user']['dept_code'];
// $dept_name = KsuCode::FACULTY_DEPT[$_dept_code];
// ?>
<h3>機器設備利用申請内容詳細</h3>
<table class="table table-bordered table-hover">
<tr><td width="20%" class="text-info">利用申請者</td>
    <td><?=$rsv['apply_member']['ja_name']?></td>
    <td class="text-info">学籍番号</td>
    <td colspan="2"><?=$rsv['apply_member']['sid']?></td>
</tr>
<tr><td class="text-info">利用責任者氏名</td>
    <td><?=$rsv['master_member']['ja_name']?></td>
    <td class="text-info">学部学科</td>
    <td><?=$rsv['dept_name'] ?></td>
    <td><?=$rsv['master_member']['tel_no']?></td>
</tr>
<tr><td class="text-info">利用代表者氏名</td><td class="pt-0 pb-0" colspan="4">
<table class="table table-light" width="100%">
<?php
foreach($rsv['use_members'] as $row){
    printf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>', $row['sid'], $row['ja_name'], $row['tel_no']);
}
?>
</table>
</td></tr>
<tr><td class="text-info">その他利用者</td><td colspan="4"><?=$rsv['other_user'] ?></td>
</tr>
<tr><td class="text-info">教職員人数</td><td><?= $rsv['staff_n'] ?>人</td>
    <td class="text-info">学生人数</td><td colspan="2"><?= $rsv['student_n'] ?>人</td>
</tr>
<tr><td class="text-info">希望利用機器</td><td colspan="4"><?=$rsv['instrument_name']?></td>
</tr>
<tr><td class="text-info">希望利用日時</td><td colspan=4><?=jpdate($rsv['stime'],true)?>～<?=jpdate($rsv['etime'],true)?></td>
</tr>
<tr><td class="text-info">試料名</td><td colspan=4><?=$rsv['sample_name']?></td>
</tr>
<tr><td class="text-info">状態</td><td colspan=4><?= $rsv['sample_state_value'] ?></td>
</tr>
<tr><td class="text-info">特性</td><td colspan=2><?= $rsv['sample_nature_value'] ?></td><td colspan=2><?=$rsv['sample_other']?></td>
</tr>
<tr><td class="text-info">X線取扱者登録の有無</td><td colspan=2><?=$rsv['xray_chk_value'] ?></td>
    <td class="text-info">登録者番号</td><td colspan=2><?=$rsv['xray_num'] ?></td>
</tr>
<tr style="height:80px;"><td class="text-info">備考</td><td colspan=4><?= $rsv['memo'] ?></td>
</tr>
</table>
<a class="btn btn-outline-primary m-1" href="?do=rsv_input&id=<?=$rsv_id?>">編集</a>
<a href="?do=inst_list" class="btn btn-outline-info m-1">戻る</a> 