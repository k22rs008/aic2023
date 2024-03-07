<?php
// namespace aic;

use aic\models\Reserve;
use aic\models\Member;
use aic\models\User;
use aic\models\RsvMember;
use aic\models\RsvSample;
use aic\models\Util;

$data = $_POST;
$rsv_id = $data['id'];
$rsv = [
    'id'=>0, 'code'=>'', 'instrument_id'=>0, 'apply_mid'=>0, 'master_mid'=>0,'process_status'=>1,
    'purpose'=>'','other_num'=>0, 'other_user'=>'', 'stime'=>'','etime'=>'','sample_name'=>'','sample_state'=>1,
    'xray_chk'=>0, 'xray_num'=>'', 'memo'=>'',
];
foreach($rsv as $key=>$val){
    if (array_key_exists($key, $data)){
        $rsv[$key] = $data[$key];
    }
}
 
$errors = [];
$existed_rsv = (new Reserve)->getListByInst($rsv['instrument_id'], $rsv['stime'], $rsv['etime']);
if (count($existed_rsv) > 0 ){
    $errors[] = sprintf("ほかの予約時間帯と被っています：%s～%s：", Util::jpdate($rsv['stime'],true), Util::jpdate($rsv['etime'],true));
}

$rsv['master_mid'] = (new User)->getLoginMid();
$member = (new Member)->getDetailBySid($data['master_sid']);
if ($member){
    $rsv['master_mid'] = $member['id'];
}

$rsv_members = [];
foreach ($data['rsv_member'] as $sid){
    if (empty($sid)) continue;
    $member = (new Member)->getDetailBySid($sid);
    if ($member){
        $rsv_members[] = $member;
    }else{
        $errors[] = sprintf("'%s'：無効な利用代表者IDです", $sid);
    }
}
if (count($rsv_members) == 0){
    $errors[] = "有効な利用代表者が指定されていません";
}
if (count($errors) > 0){
    echo '<h3 class="text-danger">以下の理由により登録できません</h3>' . PHP_EOL;
    echo '<ul class="list-group">';
    foreach ($errors as $error){
        echo '<li class="list-group-item list-group-item-info">エラー：' . $error . '！</li>' . PHP_EOL;
    }
    echo '</ul>';
    echo '<button class="btn btn-primary m-2" onclick="history.back();">戻る</button>';
}else{
    if ($rsv_id == 0){
        $rsv['code'] = (new Reserve)->nextCode();
    }
    $id = (new Reserve)->write($rsv);

    if ($rsv_id == 0){
        $rsv_id = $id;
    }

    (new RsvMember)->reset($rsv_id);
    (new RsvSample)->reset($rsv_id);

    foreach ($data['rsv_member'] as $sid){
        if (empty($sid)) continue;
        $member = (new Member)->getDetailBySid($sid);
        if ($member){
            $record = ['id'=>0, 'reserve_id'=>$rsv_id, 'member_id'=>$member['id']];
            $rs = (new RsvMember)->write($record);
        }
    }

    foreach ($data['rsv_sample'] as $val){
        $other = $val==4 ?$other = $data['sample_other'] : '';
        $record = ['id'=>0, 'reserve_id'=>$rsv_id, 'nature'=>$val, 'other'=>$other];
        // echo '<pre>'; print_r($record); echo '</pre>';
        $rs = (new RsvSample)->write($record);
    }

    header('Location:?do=rsv_detail&id='.$rsv_id);
}