<?php
require_once('db_config.php');

class Model
{
    protected $table;

    public function getDetail($id)
    {
        global $conn;
        $sql = sprintf("SELECT * FROM %s WHERE id=$id", $this->table);
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_assoc(); 
    }
    public function getList($where=1, $orderby="id")
    {
        global $conn;
        $sql = sprintf("SELECT * FROM %s WHERE %s ORDER BY %s", $this->table, $where, $orderby);
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC); 
    }
}