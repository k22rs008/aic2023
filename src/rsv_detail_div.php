<?php
namespace aic;

use aic\models\Reserve;

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
<div class="row mb-1 border-bottom border-top">
<div class="col-sm-3 text-info border-right">利用申請者</div>
<div class="col-sm-9">
<div class="row mb-1">
    <div class="col-sm-3"><?=$rsv['apply_uname']?></div>
    <div class="col-sm-3 text-info border-left border-right">学籍番号</div>
    <div class="col-sm-6">21LL001</div>
</div></div></div>
<div class="row mb-1">
<div class="col-sm-3 text-info border-bottom border-right">利用責任者氏名</div>
<div class="col-sm-9 border-bottom">
<div class="row mb-1">    
    <div class="col-sm-3"><?=$rsv['master_uname']?></div>
    <div class="col-sm-3 text-info border-left border-right">学部学科</div>
    <div class="col-sm-3">生命科学部 生命科学科</div>
    <div class="col-sm-3">Tel. 090-5540-0862</div>
</div></div></div>
<div class="row mb-1">
<div class="col-sm-3 text-info border-bottom border-right">利用代表者氏名</div>
<div class="col-sm-9">
<?php
foreach($rsv['rsv_member'] as $row){
    printf('<div class="row border-bottom">
    <div class="col-sm-3">%s</div>
    <div class="col-sm-3">%s</div>
    <div class="col-sm-6">%s</div>
    </div>', $row['sid'], $row['ja_name'], $row['tel_no']);
}
?>
</div></div>
<div class="row mb-1 border-bottom">
    <div class="col-sm-3 text-info border-right">その他利用者</div><div class="col-sm-9"></div>
</div>
<div class="row mb-1 border-bottom">
    <div class="col-sm-3 text-info border-right">教職員人数</div><div class="col-sm-3">1人</div>
    <div class="col-sm-3 text-info border-left border-right">学生人数</div><div class="col-sm-3">2人</div>
</div>
<div class="row mb-1 border-bottom">
    <div class="col-sm-3 text-info border-right">希望利用機器</div><div class="col-sm-4"><?=$rsv['instrument_name']?></div>
</div>
<div class="row mb-1 border-bottom">
    <div class="col-sm-3 text-info border-right">希望利用日時</div><div class="col-sm-9"><?=jpdate($rsv['stime'],true)?>～<?=jpdate($rsv['etime'],true)?></div>
</div>
<div class="row mb-1 border-bottom">
    <div class="col-sm-3 text-info border-right">試料名</div><div class="col-sm-9"><?=$rsv['sample_name']?></div>
</div>
<div class="row mb-1 border-bottom">
    <div class="col-sm-3 text-info border-right">状態</div><div class="col-sm-9">固体</div>
</div>
<div class="row mb-1 border-bottom">
    <div class="col-sm-3 text-info border-right">特性</div><div class="col-sm-9">爆発性</div>
</div>
<div class="row mb-1 border-bottom">
    <div class="col-sm-3 text-info border-right">X線取扱者登録の有無</div><div class="col-sm-3">無</div>
    <div class="col-sm-3 text-info border-left border-right">登録者番号</div><div class="col-sm-3"></div>
</div>
<div class="row mb-1 border-bottom">
    <div class="col-sm-3 text-info border-right">備考</div><div class="col-sm-9"></div></div>
</a>