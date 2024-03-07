<?php
namespace aic;

use aic\models\Member;
use aic\models\User;
use aic\models\KsuCode;

$mbr_id = (new User)->getLoginMemberId();
if (isset($_GET['id'])){
  $mbr_id = $_GET['id'];
}

$row= (new Member)->getDetail($mbr_id);
if ($row) {  
    echo '<h3 class="text-primary">「'. $row['ja_name'].'」会員情報</h3>' . PHP_EOL;
    echo '<table class="table table-hover">' . PHP_EOL;
    echo '<tr><th width="20%">会員ID</th><td>' . $row['sid'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>ログインID</th><td>' . $row['uid'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>日本語名</th><td>' . $row['ja_name']. '</td></tr>' . PHP_EOL;
    echo '<tr><th>日本語読み</th><td>' . $row['ja_yomi']. '</td></tr>' . PHP_EOL;
    echo '<tr><th>英語名</th><td>' . $row['en_name']. '</td></tr>' . PHP_EOL;
    echo '<tr><th>英語読み</th><td>' . $row['en_yomi']. '</td></tr>' . PHP_EOL;
    $i  = $row['category']; 
    echo '<tr><th>会員種別</th><td>' . KsuCode::MBR_CATEGORY[$i];
    if ($row['category']>1){//教育職員
      echo '<span class="float-right"><a class="btn btn-outline-primary ml-1" href="?do=stf_detail&id='.$mbr_id.'">教職員詳細</a></span>' . PHP_EOL;
    }
    echo '</td></tr>' . PHP_EOL;
    
    echo '<tr><th>メールアドレス</th><td>' . $row['email'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>電話番号</th><td>' . $row['tel_no'] . '</td></tr>' . PHP_EOL;
    $i  = $row['sex'];
    echo '<tr><th>性別</th><td>' . KsuCode::MBR_SEX[$i] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>所属</th><td>' . $row['dept_name'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>所属番号</th><td>' . $row['dept_code'] . '</td></tr>' . PHP_EOL;
    $i  = $row['authority'];  
    $class = [ 1=>'text-success', 0=>'text-danger'];
    echo '<tr><th>予約権有無</th><td class="'.$class[$i].'">' . KsuCode::MBR_AUTHORITY[$i] . '</td></tr>' . PHP_EOL;
    echo '</table>' . PHP_EOL;
    echo '<div class="pb-5 mb-5">' . PHP_EOL;
    $label = ($row['authority']) ?'予約権撤回' : '予約権付与';
    $is_admin = (new User)->isAdmin();
    $is_owner = (new User)->isOwner($mbr_id);
    if ($is_admin){
      echo '<a class="btn btn-outline-success m-1" href="?do=mbr_grant&id='.$mbr_id.'">'.$label.'</a>' . PHP_EOL;
    }
    if ($is_admin or $is_owner){
      echo '<a class="btn btn-outline-primary m-1" href="?do=mbr_input&id='.$mbr_id.'">編集</a>' . PHP_EOL;
    }
    if ($is_admin){  
      '<a href="#myModal" class="btn btn-outline-danger m-1" data-id='.$mbr_id.' data-toggle="modal">削除</a>' . PHP_EOL;
    }
    echo '<a href="?do=mbr_list" class="btn btn-outline-info m-1">戻る</a>' . PHP_EOL .  
      '</div>';
}else{
    echo '会員情報は存在しません！';
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