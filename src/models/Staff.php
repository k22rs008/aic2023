<?php
// namespace ksu\aic;

require_once('Model.php');

class Staff extends Model{
   protected $table = 'tb_staff';
   protected $member_table = "tb_member";

   public function getOptions($where=1)
   {
      global $conn;
      $sql = 'SELECT m.*, s.rank FROM %s s, %s m WHERE %s AND s.member_id=m.id ORDER BY dept_code DESC';
      $sql = sprintf($sql, $this->table, $this->member_table, $where);
      $rs = $conn->query($sql);
      if (!$rs) die('エラー: ' . $conn->error);
      $rows = $rs->fetch_all(MYSQLI_ASSOC); 
      $items = [];
      foreach ($rows as $row){
           $sid = $row['sid'];
           $rank = $row['rank'];
           $dept_code = $row['dept_code'];
           $rank_name = KsuCode::STAFF_RANK[$rank];
           $dept_name = KsuCode::FACULTY_DEPT[$dept_code];
           $items[$sid] = sprintf('%s (%s) %s', $row['ja_name'], $dept_name, $rank_name);
       }
       return $items;
   }

}