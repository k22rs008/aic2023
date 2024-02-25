<?php
// namespace ksu\aic;

require_once('Model.php');

class RsvSample extends Model{
    protected $table = "rsv_sample";
    
    const tag = [1=>'状態',2=>'特性'];
    const state = [1=>'固体',2=>'液体',3=>'気体'];
    const nature = [1=>'爆発性',2=>'毒性',3=>'揮発性',4=>'その他'];

}