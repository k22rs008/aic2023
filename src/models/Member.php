<?php
namespace aic\models;

// use aic\models\Model;

class Member extends Model{
    protected $table = "tb_member";

    public function getDetailBySid($sid)
    {
        $where = "sid='{$sid}'";
        $rows = (new Member)->getList($where);
        if ($rows)
            return  $rows[0];
        return null; 
    }
}