<?php
namespace aic;

use aic\models\Instrument;
use aic\models\Security;

(new Security)->require('admin');

$id = (new Instrument)->write($_POST);

$inst_id = $_POST['id'] > 0 ? $_POST['id'] : $id;
$webp_name = $inst_id . '.webp';
$upload_dir = 'img/instrument/';
if ($_FILES['imgfile']['size'] > 0){
    $img_file = $_FILES['imgfile']['tmp_name'];
    $webp_file = $upload_dir . $webp_name;
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($img_file);
    switch($mime_type){
        case 'image/jpeg':
            $img = imagecreatefromjpeg($img_file); break;
        case 'image/png':
            $img = imagecreatefrompng($img_file); break;
        case 'image/gif':
            $img = imagecreatefromgif($img_file); break;
    }
    if (isset($img)){
        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);
        imagewebp($img, $webp_file, 100);
        imagedestroy($img);
    }else{
        echo '<h3 class="text-danger">画像タイプはpng, jpg, gifしかサポートしません。</h3>';
    }
}
header('Location:?do=inst_detail&id=' . $inst_id);