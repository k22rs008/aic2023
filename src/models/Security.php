<?php
namespace aic\models;

use aic\models\Model;
use aic\models\User;

class Security extends Model
{
    const ERROR_MSG = [
        'require_login' => 'この機能は、ログインしないと利用できません。',
        'require_admin' => 'この機能は、管理者でないと利用できません。',
        'require_staff' => 'この機能は、教職員でないと利用できません。',
        'require_owner' => 'この機能は、本人でないと利用できません。',
        'require_reserve' => 'この機能は、予約権限のある会員でないと利用できません。',
    ];

    public function require($privilege='login', $mbr_id=0)
    {
        $extra_msg = '';
        $is_ok = true;
        switch ($privilege){
            case 'admin': 
                $is_ok = (new User)->isAdmin();
                break; 
            case 'staff': 
                $is_ok = (new User)->isStaff();
                break;      
            case 'owner': 
                $is_ok = (new User)->isOwner($mbr_id);
                break;
            case 'reserve': 
                $is_ok = (new User)->canReserve();
                break;    
            case 'login':
                $is_ok = (new User)->getLoginRole();
                break;

        }
        if (!$is_ok){
            $error_id = 'require_' . $privilege;
            $default_msg = 'エラーが発生しました。';
            $msg = isset(self::ERROR_MSG[$error_id]) ? self::ERROR_MSG[$error_id] : $default_msg;
            echo '<p class="text-danger">' . $msg . $extra_msg .'</p>';
            die ('<a class="btn btn-outline-primary m-1" href="' . $_SERVER['HTTP_REFERER'] . '" >戻る</a>');
        }
    }



}