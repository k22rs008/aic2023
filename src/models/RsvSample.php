<?php
// namespace ksu\aic;

require_once('Model.php');

class RsvSample extends Model{
    protected $table = "rsv_sample";

    public function reset($rsv_id)
    {
        global $conn;
        $sql = sprintf('DELETE FROM %s WHERE reserve_id=%d', $this->table, $rsv_id);
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $conn->affected_rows;
    }
}