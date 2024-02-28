<?php
// namespace ksu\aic;

require_once('db_config.php');
include_once('KsuCode.php');

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

    public function delete($id)
    {
        global $conn;
        $sql = sprintf("DELETE FROM %s WHERE id=%d", $this->table, $id);
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $conn->affected_rows;
    }

    public function write($data)
    {
        global $conn;   
        $act = (isset($data['id']) and $data['id']>0) ?'update' : 'insert';
        $keys = $values=[];
        foreach($data as $key=>$val){
            if ($key == 'id') continue; // skip 'id'
            $keys[] = $key;
            $typed_val = gettype($val)=='string' ? "'". $val."'" : $val;
            $values[] = ($act=='update') ? $key . '=' . $typed_val : $typed_val;
        }
        $sqlkeys = implode(',', $keys);
        $sqlvalues = implode(',', $values);        
        if ($act=='insert'){
            $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $this->table, $sqlkeys, $sqlvalues);
        }else{
            $id = $data['id'];
            $sql = sprintf("UPDATE %s SET %s WHERE id=%d", $this->table, $sqlvalues, $id);
        }
        // echo $sql;
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return ($act=='insert') ? $conn->insert_id : $conn->affected_rows; 
    }

    /** get information about fields of the table*/
    public function getFileds()
    {
        global $conn;
        $sql = sprintf("SHOW COLUMNS FROM %s",$this->table);
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_all(MYSQLI_ASSOC);// Field, Type, Null...
    }

    /** transform a row list to a KVP list: Key-Value Pair */
    public static function toKVP($data, $key_field, $value_field)
    {
        $options = [];
        foreach($data as $row){
            $key = $row[$key_field];
            $value = $row[$value_field];
            $options[$key] = $value;
        }
        return $options;
    }
}