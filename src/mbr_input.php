<?php
namespace aic;

use aic\models\Member;
use aic\models\KsuCode;
use aic\views\Html;

$mbr_id = 0;
if (isset($_GET['id'])){
  $mbr_id = $_GET['id'];
}
$row= (new Member)->getDetail($mbr_id);

if ($row) {  
    echo '<form method="post" action="?do=mbr_save">';
    echo Html::input('hidden', 'id', $mbr_id);
    echo '<h3 class="text-primary">会員情報編集</h3>' . PHP_EOL;
    echo '<table class="table table-hover">' . PHP_EOL;
    echo '<tr><th width="20%">会員ID</th><td>' . $row['sid'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>ログインID</th><td>' . $row['uid'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>日本語名</th><td>' . Html::input('text','ja_name',$row['ja_name']). '</td></tr>' . PHP_EOL;
    echo '<tr><th>日本語読み</th><td>' . Html::input('text','ja_yomi',$row['ja_yomi']). '</td></tr>' . PHP_EOL;
    echo '<tr><th>英語名</th><td>' . Html::input('text','en_name',$row['en_name']). '</td></tr>' . PHP_EOL;
    echo '<tr><th>英語読み</th><td>' . Html::input('text','en_yomi',$row['en_yomi']). '</td></tr>' . PHP_EOL;
    $i  = $row['category']; 
    echo '<tr><th>会員種別</th><td>' . KsuCode::MBR_CATEGORY[$i]. '</td></tr>' . PHP_EOL;
    
    echo '<tr><th>メールアドレス</th><td>' . Html::input('text','email',$row['email']) . '</td></tr>' . PHP_EOL;
    echo '<tr><th>電話番号</th><td>' . Html::input('text','tel_no',$row['tel_no']) . '</td></tr>' . PHP_EOL;
    $i  = $row['sex'];
    echo '<tr><th>性別</th><td>' . Html::select(KsuCode::MBR_SEX,'sex',[$i],'radio') . '</td></tr>' . PHP_EOL;
    echo '<tr><th>所属</th><td>' . Html::input('text','dept_name',$row['dept_name']) . '</td></tr>' . PHP_EOL;
    echo '<tr><th>所属番号</th><td>' . Html::input('text','dept_code',$row['dept_code']) . '</td></tr>' . PHP_EOL;
    $i  = $row['authority'];  
    echo '<tr><th>予約権有無</th><td>' . KsuCode::MBR_AUTHORITY[$i] . '</td></tr>' . PHP_EOL;
    echo '</table>' . PHP_EOL;    
    echo '<div class="pb-5 mb-5">' . PHP_EOL;
    echo '<div class="pb-5 mb-5"><button type="submit" class="btn btn-outline-primary m-1">保存</button>'. PHP_EOL; 
    echo  '<a href="?do=mbr_detail&id='.$mbr_id.'" class="btn btn-outline-info m-1">戻る</a>' . PHP_EOL;
    echo  '</div>';
    echo '</form>';
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