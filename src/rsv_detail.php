<?php
namespace aic;

use aic\models\Reserve;
use aic\models\User;
use aic\models\Util;

$rsv_id = 0;
if (isset($_GET['id'])){
  $rsv_id = $_GET['id'];
}
$rsv= (new Reserve)->getDetail($rsv_id);
// echo '<pre>'; print_r($rsv); echo '</pre>';
$mbr_id = $rsv['apply_mid'];
$status = $rsv['process_status'];
$status_label = ($status==1 or $status==3) ? '承認' : '却下';
$status_class = [1=>'text-info', 2=>'text-success', 3=>'text-danger'];
?>
<h3>機器設備利用申請内容詳細</h3>
<table class="table table-bordered table-hover">
<tr><td width="20%" class="text-info">利用申請者氏名</td>
  <td><?=$rsv['apply_member']['ja_name']?></td>
  <td class="text-info">会員番号</td>
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
foreach($rsv['rsv_member'] as $row){
  printf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>', $row['sid'], $row['ja_name'], $row['tel_no']);
}
?>
</table>
</td></tr>
<tr><td class="text-info">教職員人数</td><td><?= $rsv['staff_n'] ?>人</td>
  <td class="text-info">学生人数</td><td colspan="2"><?= $rsv['student_n'] ?>人</td>
</tr>
<tr><td class="text-info">その他利用者数</td>
  <td><?=$rsv['other_num'] ?></td>
  <td class="text-info">利用者説明</td>
  <td colspan="2"><?= $rsv['other_user'] ?></td>
</tr>
<tr><td class="text-info">希望利用機器</td>
<td colspan="4"><?=$rsv['instrument_fullname']?>（<?=$rsv['instrument_shortname']?>）</td>
</tr>
<tr><td class="text-info">設置場所</td>
<td colspan="4"><?=$rsv['room_name']?>（<?=$rsv['room_no']?>）</td>
</tr>
<tr><td class="text-info">希望利用日時</td>
<td colspan=4><?=Util::jpdate($rsv['stime'],true)?>～<?=Util::jpdate($rsv['etime'],true)?></td>
</tr>
<tr><td class="text-info">試料名</td><td colspan=4><?=$rsv['sample_name']?></td>
</tr>
<tr><td class="text-info">試料状態</td><td colspan=4><?= $rsv['sample_state_str'] ?></td>
</tr>
<tr><td class="text-info">試料特性</td><td colspan=2><?= $rsv['sample_nature_str'] ?></td>
<td colspan=2><?=$rsv['sample_other']?></td>
</tr>
<tr><td class="text-info">X線取扱者登録の有無</td><td colspan=2><?=$rsv['xray_chk_str'] ?></td>
  <td class="text-info">登録者番号</td><td colspan=2><?=$rsv['xray_num'] ?></td>
</tr>
<tr><td class="text-info">備考</td><td colspan=4><?= $rsv['memo'] ?></td>
</tr>
<tr><td class="text-info">承認状態</td>
  <td  colspan=2 class="<?=$status_class[$status]?>"><?= $rsv['status_name'] ?></td>
  <td class="text-info">申請番号</td><td><?= $rsv['code'] ?></td>
</tr>
</table>
<?php
  $is_admin = (new User)->isAdmin();
  $is_owner = (new User)->isOwner($mbr_id);
  if ($is_admin){
    echo '<a class="btn btn-outline-success m-2" href="?do=rsv_grant&id='.$rsv_id.'">'.$status_label. '</a>';
  }
  if ($is_admin or $is_owner){
    echo '<a class="btn btn-outline-primary m-2" href="?do=rsv_input&id='. $rsv_id.'">編集</a>' . PHP_EOL 
      . '<a href="#myModal" class="btn btn-outline-danger m-2" data-id='.$rsv_id .' data-toggle="modal">削除</a>' .PHP_EOL;
  }
?>
<a href="?do=rsv_list" class="btn btn-outline-info m-2" onclick="history.back();">戻る</a> 

<!-- Modal HTML -->
<div id="myModal" class="modal fade">
  <div class="modal-dialog modal-confirm">
    <div class="modal-content">
      <div class="modal-header">
        <div class="icon-box">
          <i class="material-icons">&#xE5CD;</i>
        </div>
        <h4 class="text-info">この予約を削除しますか？</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <p>「はい」を押したら、この予約を削除します。</p>
      </div>
      <div class="modal-footer">
        <a href="" data-url="?do=rsv_delete" class="btn btn-danger" id="deleteBtn">はい</a>
        <button type="button" class="btn btn-info" data-dismiss="modal">いいえ</button>
      </div>
    </div>
  </div>
</div>
<script>
  $('#myModal').on('shown.bs.modal', function(event) {
    var id = $(event.relatedTarget).data('id');
    var href = $(this).find('#deleteBtn').data('url') +'&id=' + id;
    $(this).find('#deleteBtn').attr('href', href);
  });
</script>