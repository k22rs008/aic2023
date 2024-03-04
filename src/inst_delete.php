<?php
namespace aic;

use aic\models\Instrument;
use aic\models\Security;

(new Security)->require('admin');

if (isset($_GET['id'])){
    $inst_id = $_GET['id'];
    (new Instrument)->delete($inst_id);
    header('Location:?do=inst_list');
}else{
    echo '<h3 class="text-danger">IDが指定されていないため、削除できません！</h3>';
}