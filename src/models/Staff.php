<?php
// namespace ksu\aic;

require_once('db_config.php');
require_once('Model.php');

class Staff extends Model{
   const rank  = [1=>'教授',2=>'准教授',3=>'講師',4=>'助教'];
   const title =[1=>'大学教育職員',2=>'事務職員',9=>'その他'];

}