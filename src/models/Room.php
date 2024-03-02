<?php
namespace aic\models;

// use aic\models\Model;

class Room extends Model {
    protected $table = "tb_room";

    public function getListItems()
    {
        $rows = parent::getList();
        $items = [];
        foreach($rows as $row){
            $id = $row['id'];
            $name = $row['room_name'] . $row['room_no']; 
            $items[$id] = $name;
        }
        return $items;
    }
}