<?php
namespace aic\models;

class RsvMember extends Model {
    protected $table = "rsv_member";
    protected $member_table = "tb_member";

    public function getList($where=1, $orderby='id', $page=0)
    {
        $conn = $this->db; 
        $sql = "SELECT m.* FROM %s r, %s m WHERE %s AND r.member_id=m.id ORDER BY %s";
        $sql = sprintf($sql, $this->table, $this->member_table, $where, $orderby);
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC); 
    }

    public function reset($rsv_id)
    {
        $conn = $this->db; 
        $sql = sprintf('DELETE FROM %s WHERE reserve_id=%d', $this->table, $rsv_id);
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $conn->affected_rows;
    }
    
}