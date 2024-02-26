<?php
// namespace ksu\aic;

require_once('Model.php');

class RsvMember extends Model{
    protected $table = "rsv_member";
    protected $member_table = "tb_member";

    public function getList($where=1, $orderby='id')
    {
        global $conn;
        $sql = "SELECT mm.* FROM %s rm, %s mm WHERE %s AND rm.member_id=mm.id ORDER BY %s";
        $sql = sprintf($sql, $this->table, $this->member_table, $where, $orderby);
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC); 
    }
    
}