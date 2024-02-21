<?php
include 'models/facility.php';
$f_id = $_GET['id'];
$row= (new Facility)->getDetail($f_id);
if ($row) {  
    $url = 'img/facility/'. $f_id .'.webp';
    if (!@GetImageSize($url)){
        $url = 'img/dummy-image-square1.webp' ; 
    }   
    echo '<p><img src="'. $url . '" height="240" width="320" class="img-rounded"></p>' . PHP_EOL;
    echo '<h3 class="text-primary">'. $row['fname'].'</h3>' . PHP_EOL;
    echo '<table class="table table-hover">' . PHP_EOL;
    echo '<tr><th width="20%">機器ID</th><td>' . $row['id'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th width="20%">機器名称</th><td>' . $row['fname']. '</td></tr>' . PHP_EOL;
    echo '<tr><th>略称</th><td>' . $row['fshortname']. '</td></tr>' . PHP_EOL;
    $i  = $row['status']; 
    $status= Facility::status; 
    echo '<tr><th>状態</th><td>' . $status[$i]. '</td></tr>' . PHP_EOL;
    $i  = $row['category']; 
    $category = Facility::category;
    echo '<tr><th>カテゴリ</th><td>' . $category[$i] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>メーカー</th><td>' . $row['maker'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>型式</th><td>' . $row['model'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>導入年月</th><td>' . $row['iyear'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>設置場所</th><td>' . $row['splace'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>場所番号</th><td>' . $row['place_no'] . '</td></tr>' . PHP_EOL;
    echo '<tr><th>詳細</th><td>' . nl2br($row['detail']) . '</td></tr>' . PHP_EOL;
    echo '</table>' . PHP_EOL;
    echo '<a class="btn btn-primary mr-2" href="?do=inst_edit&id='.$f_id.'">編集</a>' . PHP_EOL;
    echo '<a href="#myModal" class="btn btn-danger  mr-2" data-id='.$f_id.' data-toggle="modal">削除</a>' . PHP_EOL;
    echo '<a href="?do=inst_list" class="btn btn-info">戻る</a>' . PHP_EOL;  
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