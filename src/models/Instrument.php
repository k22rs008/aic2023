<?php
// namespace ksu\aic;

include_once('Model.php');
include_once('Room.php');

class Instrument extends Model{
    protected $table = "tb_instrument";
    const state = [1=>'使用可',2=>'貸出中',3=>'使用不可',9=>'その他'];
    const category=[1=>'観察', 2=>'分析',3=>'計測',4=>'調製',9=>'その他'];

    public function getDetail($id)
    {
        $detail = parent::getDetail($id);
        if ($detail){
            $room_id = $detail['room_id'];
            $room = (new Room)->getDetail($room_id);
            $detail['room_name'] = $room['room_name'];
            $detail['room_no'] = $room['room_no'];
        }
        return $detail;
    }

}