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
<div class="row">
<div class="col-sm-3">利用申請者</div>
<div class="col-sm-9 border-bottom border-top">
<div class="row">
    <div class="col-sm-3"><?=$rsv['apply_uname']?></div>
    <div class="col-sm-3">学籍番号</div>
    <div class="col-sm-6">21LL001</div>
</div></div></div>
<div class="row">
<div class="col-sm-3">利用責任者氏名</div>
<div class="col-sm-9 border-bottom">
<div class="row">    
    <div class="col-sm-3"><?=$rsv['master_uname']?></div>
    <div class="col-sm-3">学部学科</div>
    <div class="col-sm-3">生命科学部 生命科学科</div>
    <div class="col-sm-3">TEL. 090-5540-0862</div>
</div></div></div>
<div class="row">
<div class="col-sm-3">利用代表者氏名</div>
<div class="col-sm-9 border-bottom">
<?php
foreach($rsv['rsv_member'] as $row){
    printf('<div class="row">
    <div class="col-sm-3 border-bottom">%s</div>
    <div class="col-sm-3 border-bottom">%s</div>
    <div class="col-sm-6 border-bottom">%s</div>
    </div>', $row['sid'], $row['ja_name'], $row['tel_no']);
}
?>
</div></div>
<div class="row">
    <div class="col-sm-3">その他利用者</div><div class="col-sm-9"></div>
</div>
<div class="row">
    <div class="col-sm-3">教職員人数</div><div class="col-sm-3 border-bottom">1人</div>
    <div class="col-sm-3">学生人数</div><div class="col-sm-3 border-bottom">2人</div>
</div>
<div class="row">
    <div class="col-sm-3">希望利用機器</div><div class="col-sm-4 border-bottom"><?=$rsv['instrument_id']?></div>
</div>
<div class="row">
    <div class="col-sm-3">希望利用日時</div><div class="col-sm-9 border-bottom"><?=jpdate($rsv['stime'],true)?>～<?=jpdate($rsv['etime'],true)?></div>
</div>
<div class="row">
    <div class="col-sm-3">試料名</div><div class="col-sm-9 border-bottom"><?=$rsv['sample_name']?></div>
</div>
<div class="row">
    <div class="col-sm-3">状態</div><div class="col-sm-9 border-bottom">固体</div>
</div>
<div class="row">
    <div class="col-sm-3">特性</div><div class="col-sm-9 border-bottom">爆発性</div>
</div>
<div class="row">
    <div class="col-sm-3">X線取扱者登録の有無</div><div class="col-sm-3 border-bottom">無</div>
    <div class="col-sm-3">登録者番号</div><div class="col-sm-3 border-bottom"></div>
</div>
<div class="row">
    <div class="col-sm-2">備考</div><div class="col-sm-9 border-bottom"></div></div>
</a>