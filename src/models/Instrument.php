<?php
namespace aic\models;

use aic\models\Room;

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