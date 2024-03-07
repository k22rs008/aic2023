<?php
namespace aic;

use aic\models\Instrument;
use aic\models\User;
use aic\models\KsuCode;

$inst_id = 0;
if (isset($_GET['id'])){
  $inst_id = $_GET['id'];
}
$row= (new Instrument)->getDetail($inst_id);
if ($row) {  
    $url = 'img/instrument/'. $inst_id .'.webp';
    if (!@GetImageSize($url)){
        $url = 'img/dummy-image-square1.webp' ; 
    }   
    echo '<p><img src="'. $url . '" height="240" width="320" class="rounded"></p>' . PHP_EOL;
    echo '<h3 class="text-primary">'. $row['fullname'].'</h3>' . PHP_EOL;
    echo '<table class="table table-hover">' . PHP_EOL;
    echo '<tr><th width="20%">機器ID</th><td>' . $row['id'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th width="20%">機器名称</th><td>' . $row['fullname']. '</td></tr>' . PHP_EOL;
    echo '<tr><th>略称</th><td>' . $row['shortname']. '</td></tr>' . PHP_EOL;
    echo '<tr><th>主な用途</th><td>' . $row['purpose'] . '</td></tr>' . PHP_EOL;
    $i  = $row['state'];  
    echo '<tr><th>状態</th><td>' . KsuCode::INST_STATE[$i]. '</td></tr>' . PHP_EOL;
    $i  = $row['category'];
    echo '<tr><th>カテゴリ</th><td>' . KsuCode::INST_CATEGORY[$i] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>メーカー</th><td>' . $row['maker'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>型式</th><td>' . $row['model'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>導入年月</th><td>' . $row['bought_year'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>設置場所</th><td>' . $row['room_name'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>場所番号</th><td>' . $row['room_no'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>詳細</th><td>' . nl2br($row['detail']) . '</td></tr>' . PHP_EOL;
    echo '</table>' . PHP_EOL;
    echo '<div class="pb-5 mb-5">' . PHP_EOL; 
    $is_admin = (new User)->isAdmin();
    if ($is_admin){
      echo '<a class="btn btn-outline-primary m-1" href="?do=inst_input&id='.$inst_id.'">編集</a>'.
        '<a href="#myModal" class="btn btn-outline-danger m-1" data-id='.$inst_id.' data-toggle="modal">削除</a>';
    }
    $can_reserve = (new User)->canReserve();
    if ($can_reserve){
      echo '<a class="btn btn-outline-success m-1" href="?do=rsv_input&inst='.$row['id'].'">予約</a>';
    }
    echo '<a class="btn btn-outline-success m-1" href="?do=aic_detail&id='.$row['id'].'">空き状態</a>';
    echo '<a href="?do=inst_list" class="btn btn-outline-info m-1">戻る</a>' . PHP_EOL .  
      '</div>';
}else{
    echo 'この機器は存在しません！';
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
        <h4 class="text-info">この機器設備を削除しますか？</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <p>「はい」を押したら、この機器設備を削除します。</p>
      </div>
      <div class="modal-footer">
        <a href="" data-url="?do=inst_delete" class="btn btn-danger" id="deleteBtn">はい</a>
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