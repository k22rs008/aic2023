<?php
// namespace ksu\aic;

include_once('Model.php');

class Member extends Model{
    protected $table = "tb_member";

    public function getStaffItems()
    {
        $rows = parent::getList('category>1', 'dept_code DESC');
        $items = [];
        foreach ($rows as $row){
            $sid = $row['sid'];
            $dept_code = $row['dept_code'];
            $items[$sid] = sprintf('%s (%s)', $row['ja_name'], KsuCode::FACULTY_DEPT[$dept_code]);
        }
        return $items;
    }

}