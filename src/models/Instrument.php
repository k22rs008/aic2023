<?php
// namespace ksu\aic;

include_once('Model.php');
include_once('Room.php');

class Instrument extends Model{
    protected $table = "tb_instrument";
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