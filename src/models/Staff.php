<?php
namespace aic\models;

class Staff extends Model {
   protected $table = 'tb_staff';
   protected $member_table = "tb_member";

   public function getOptions($where=1)
   {
      $conn = $this->db; 
      $sql = 'SELECT m.*, s.role_rank FROM %s s, %s m WHERE %s AND s.member_id=m.id ORDER BY dept_code DESC';
      $sql = sprintf($sql, $this->table, $this->member_table, $where);
      $rs = $conn->query($sql);
      if (!$rs) die('エラー: ' . $conn->error);
      $rows = $rs->fetch_all(MYSQLI_ASSOC); 
      $items = [];
      foreach ($rows as $row){
           $sid = $row['sid'];
           $rank_name = $row['role_rank'];
           $dept_name = $row['dept_name'];      
           $items[$sid] = sprintf('%s (%s) %s', $row['ja_name'], $dept_name, $rank_name);
       }
       return $items;
   }

}