<?php
namespace aic\models;

use aic\models\KsuCode;

class Model
{
    protected $table;
    private static $conf;
    protected $db;

    public function __construct()
    {  
        if (!self::$conf){
            self::$conf = ['host'=>"localhost", 'usename'=>"root", 'password'=>"", 'dbname'=>"aic_rsv"];
        }  
        $this->initDb();
    }
  
    public function initDb()
    {
        
        $this->db = new \mysqli(
            self::$conf['host'],
            self::$conf['usename'],
            self::$conf['password'],
            self::$conf['dbname'],
        );//＜開発時の環境設定＞
        if ($this->db->connect_errno) {
            die($this->db->connect_error);
        }
        $this->db->set_charset('utf8'); //文字コードをutf8に設定（文字化け対策）
        
    }
  
    public static function setConnInfo($conf)
    {
      self::$conf = $conf;
    }

    public function getDetail($id)
    {
        $sql = sprintf("SELECT * FROM %s WHERE id=$id", $this->table);
        $rs = $this->db->query($sql);
        if (!$rs) die('エラー: ' . $this->db->error);
        return $rs->fetch_assoc(); 
    }
    
    public function getList($where=1, $orderby="id", $page=0)
    {
        $sql = sprintf("SELECT * FROM %s WHERE %s ORDER BY %s", $this->table, $where, $orderby);
        if ($page > 0){
            $n = KsuCode::PAGE_ROWS;
            $sql .= sprintf(' LIMIT %d OFFSET %d', $n, ($page-1) * $n);
        }
        $rs = $this->db->query($sql);
        if (!$rs) die('エラー: ' . $this->db->error);
        return $rs->fetch_all(MYSQLI_ASSOC); 
    }
    
    public function getNumRows($where=1, $orderby="id")
    {
        $sql = sprintf("SELECT * FROM %s WHERE %s ORDER BY %s", $this->table, $where, $orderby);
        $rs = $this->db->query($sql);
        if (!$rs) die('エラー: ' . $this->db->error);
        return $rs->num_rows;
    }

    public function delete($id)
    {
        $sql = sprintf("DELETE FROM %s WHERE id=%d", $this->table, $id);
        $rs = $this->db->query($sql);
        if (!$rs) die('エラー: ' . $this->db->error);
        return $this->db->affected_rows;
    }

    public function write($data)
    {
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
        $rs = $this->db->query($sql);
        if (!$rs) die('エラー: ' . $this->db->error);
        return ($act=='insert') ? $this->db->insert_id : $this->db->affected_rows; 
    }

    /** get information about fields of the table*/
    public function getFileds()
    {
        $sql = sprintf("SHOW COLUMNS FROM %s",$this->table);
        $rs = $this->db->query($sql);
        if (!$rs) die('エラー: ' . $this->db->error);
        return $rs->fetch_all(MYSQLI_ASSOC);// Field, Type, Null...
    }

}