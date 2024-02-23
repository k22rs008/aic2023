<?php
// namespace ksu\aic;

include_once('Model.php');

class Instrument extends Model{
    protected $table = "tb_facility";
    const status = [1=>'使用可',2=>'貸出中',3=>'使用不可',9=>'その他'];
    const category=[1=>'観察', 2=>'分析',3=>'計測',4=>'調製',9=>'その他'];

}