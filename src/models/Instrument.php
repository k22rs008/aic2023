<?php
namespace aic\models;

use aic\models\Room;

class Instrument extends Model{
    protected $table = "tb_instrument";
    protected $inst_view = "vw_instrument";
    protected $mrfu_view = "vw_mrfu";

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

    // policy: 'mru', 'mfu', 'mrfu', 'mfru' (RFU: recently/frequently used)
    public function getListRFU($member_mid, $policy='mrfu', $where=1) 
    {
        $orderby = 'recency,freq DESC,room_id';
        if ($policy == 'mru'){
            $orderby = 'recency, room_id';
        }else if ($policy=='mfu'){
            $orderby = 'freq DESC, room_id';
        }else if ($policy=='mfru'){
            $orderby = 'freq DESC, recency, room_id';
        }
        $sql ='SELECT i.*, IF(ISNULL(u.recency),365,u.recency) AS recency, freq  
            FROM %s i LEFT JOIN %s u ON (i.id=u.instrument_id AND u.apply_mid=%d)
            WHERE %s ORDER BY %s';
        $sql = sprintf($sql, $this->inst_view, $this->mrfu_view, $member_mid, $where, $orderby);   
        $rs = $this->db->query($sql);
        if (!$rs) die('エラー: ' . $this->db->error);
        return $rs->fetch_all(MYSQLI_ASSOC); 
    }

}