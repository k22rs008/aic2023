<?php
namespace aic\models;

// use aic\models\Model;

class Member extends Model {
    protected $table = "tb_member";
    
    public function getDetailBySid($sid)
    {
        $member = $this->getList("sid='$sid'");
        if (count($member)>0){
            return $member[0];
        }
        return null;
    }
}