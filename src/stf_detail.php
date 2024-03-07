<?php
// namespace aic;

use aic\models\Member;
use aic\models\Staff;
use aic\models\User;
use aic\models\KsuCode;

$mbr_id = 0;
if (isset($_GET['id'])){
  $mbr_id = $_GET['id'];
}
$staff = null;
$stf_id = 0;
$rows = (new Staff)->getList('member_id='.$mbr_id);
if (count($rows)>0){
  $staff = $rows[0];
  $stf_id = $staff['id'];
}
$row= (new Member)->getDetail($mbr_id);

if ($staff and $row ) {  
    echo '<h3 class="text-primary">「'. $row['ja_name'].'」教職員情報</h3>' . PHP_EOL;
    echo '<table class="table table-hover">' . PHP_EOL;
    echo '<tr><th width="20%">会員ID</th><td>' . $row['sid'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>ログインID</th><td>' . $row['uid'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>日本語名</th><td>' . $row['ja_name']. '</td></tr>' . PHP_EOL;
    echo '<tr><th>所属</th><td>' . $row['dept_name'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>職員種別</th><td>' . $staff['role_title'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>役職</th><td>' . $staff['role_rank'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>メールアドレス</th><td>' . $row['email'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>電話番号</th><td>' . $row['tel_no'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>内線番号</th><td>' . $staff['tel_ext']. '</td></tr>' . PHP_EOL;
    echo '<tr><th>部屋番号</th><td>' . $staff['room_no']. '</td></tr>' . PHP_EOL;
    $i  = $row['authority'];  
    echo '<tr><th>予約権有無</th><td>' . KsuCode::MBR_AUTHORITY[$i] . '</td></tr>' . PHP_EOL;
    $r  = $staff['responsible'];  
    $class = [ 1=>'text-success', 0=>'text-danger'];
    echo '<tr><th>責任者可否</th><td class="'.$class[$r].'">' . KsuCode::STAFF_RESPONSIBLE[$r] . '</td></tr>' . PHP_EOL;
    echo '</table>' . PHP_EOL;
    echo '<div class="pb-5 mb-5">' . PHP_EOL;    
    $is_admin = (new User)->isAdmin();
    if ($is_admin){
      $i  = $row['authority'];    
      $label = ($r==0) ?'責任者指定' : '責任者指定撤回';  
        echo '<a class="btn btn-outline-primary m-1" href="?do=stf_grant&id='.$stf_id.'">'.$label.'</a>'. PHP_EOL;
    }
    //echo '<a class="btn btn-outline-primary m-1" href="?do=stf_input&id='.$stf_id.'">編集</a>' . PHP_EOL;
    echo '<a href="?do=mbr_detail&id='.$mbr_id.'" class="btn btn-outline-info m-1">戻る</a>' . PHP_EOL .  
      '</div>';
}else{
    echo '教職員情報は存在しません！';
}
?>
<!-- Modal HTML -->
<div id="myModal" class="modal fade">
  <div class="modal-dialog modal-confirm">
    <div class="modal-content">
      <div class="modal-header">
        <div class="icon-box">
          <i class="material-icons">&#xE5CD;</i>
        </div>
        <h4 class="text-info">この会員を削除しますか？</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <p>「はい」を押したら、この会員を削除します。</p>
      </div>
      <div class="modal-footer">
        <a href="" data-url="?do=mbr_delete" class="btn btn-danger" id="deleteBtn">はい</a>
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