<?php
// namespace ksu\aic;

require_once ('models/Reserve.php');

// echo '<pre>'; print_r($_POST); echo '</pre>';
$data = $_POST;
$rsv_id = $data['id'];
$rsv = [
    'id'=>0,'instrument_id'=>0,'apply_mid'=>0,'master_mid'=>0,
    'purpose'=>'','other_user'=>'','stime'=>'','etime'=>'','sample_name'=>'','sample_state'=>0,
    'xray_chk'=>0, 'xray_num'=>'', 'memo'=>'',
];
foreach($rsv as $key=>$val){
    if (array_key_exists($key, $data)){
        $rsv[$key] = $data[$key];
    }
} 

$member = (new Member)->getDetailBySid($data['master_sid']);
$rsv['master_mid'] = $member['id']; 

echo '<pre>'; print_r($rsv); echo '</pre>';

$rs = (new Reserve)->write($rsv);

if ($rsv_id == 0){
    $rsv_id = $rs;
}

(new RsvMember)->reset($rsv_id);
(new RsvSample)->reset($rsv_id);

foreach ($data['rsv_member'] as $sid){
    if (empty($sid)) continue;
    $member = (new Member)->getDetailBySid($sid);
    $record = ['id'=>0, 'reserve_id'=>$rsv_id, 'member_id'=>$member['id']];
    // echo '<pre>'; print_r($record); echo '</pre>';
    $rs = (new RsvMember)->write($record);
}


foreach ($data['rsv_sample'] as $val){
    $other = $val==4 ?$other = $data['sample_other'] : '';
    $record = ['id'=>0, 'reserve_id'=>$rsv_id, 'nature'=>$val, 'other'=>$other];
    echo '<pre>'; print_r($record); echo '</pre>';
    $rs = (new RsvSample)->write($record);

}
