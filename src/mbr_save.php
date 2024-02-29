<?php
namespace aic;

use aic\models\Member;

foreach ($_POST as $name=>$value){
    echo '<b>' . $name, ':</b> ', $value, '<br>' . PHP_EOL;
}
$mbr_id = $_POST['id'];

(new Member)->write($_POST);
header('Location:?do=mbr_detail&id=' . $mbr_id);
