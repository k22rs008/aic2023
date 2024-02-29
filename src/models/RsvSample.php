<?php
namespace aic\models;

class RsvSample extends Model{
    protected $table = "rsv_sample";

    public function reset($rsv_id)
    {
        $conn = $this->db; 
        $sql = sprintf('DELETE FROM %s WHERE reserve_id=%d', $this->table, $rsv_id);
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $conn->affected_rows;
    }
}