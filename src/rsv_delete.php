<?php
namespace aic;

use aic\models\Reserve;
use aic\models\RsvMember;
use aic\models\RsvSample;
use aic\models\Security;

(new Security)->require('admin');

if (isset($_GET['id'])){
    $rsv_id = $_GET['id'];
    (new Reserve)->delete($rsv_id);
    (new RsvMember)->reset($rsv_id);
    (new RsvSample)->reset($rsv_id);
    header('Location:?do=rsv_list');
}else{
    echo '<h3 class="text-danger">IDが指定されていないため、削除できません！</h3>';
}