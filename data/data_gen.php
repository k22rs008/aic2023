<?php
/**
 * テストデータ生成：
 * 1. 架空の氏名（男性80名、女性40）を生成。以下のツールを使用
 *    http://www-dx.ip.kyusan-u.ac.jp/ksu/autodata/index.php
 * 2. データを読み込み、 偏らないようにシャフルする
 * 3. 会員(member)データを120行生成（学生100+教育職員20）
 * 4. 教育職員(staff)データを20行生成(教育職員15+事務職員5)
 * 5. 会員データに基づきユーザアカウントを作成
 * 6. 予約(reserve)データを生成(40機器X2ヶ月X30日X0~4件/1日)。さらに以下を含む
 * 　　a. 試料状態性質(rsv_sample) 
 *     b. 利用代表者(rsv_member)
 */
include 'data_gen_inc.php';
date_default_timezone_set('Asia/Tokyo');
// header('Content-Type: text/plain'); 

// 2. データを読み込み、 偏らないようにシャフルする
$persons = file('test_data_persons.txt');
srand(20240224); // 乱数順を固定にする
shuffle($persons);

$undergrad_num = 90;// 学部生数 所属学部学科コード: 'LT'
$postgrad_num = 10; // 院生数 所属学部学科コード: 'GLT'(前期課程), 'DLT'(後期課程)
$student_num  = $undergrad_num + $postgrad_num; // 学生総数
$staff_num = 20; // 教育職員数　所属部署コード：'LT'(教員), 'AIC'(職員)
$s21= 43;// 学籍番号が「21LT」で始まる学生数（残りは「22LT」）
$g21= 4; // 学籍番号が「21GLT」で始まる前期課程学生数
$g22= 5; // 学籍番号が「22GLT」で始まる前期課程学生数(残りは後期課程「22DLT」)

// 3. 会員（学部生90＋大学院生10＋教育職員20）
$members =[];
$member=[
    'id'=>0,'uid'=>'','sid'=>'','email'=>'','tel_no'=>'',
    'ja_name'=>'','sex'=>1, 'dept_code'=>'', 'dept_name'=>'','category'=>1,'authority'=>1,
];
for ($i=0; $i <count($persons); $i++) {
    $id = $i + 1;
    $line = trim($persons[$i]);
    list($ja_name, $sex, $tel_no) = explode(',',$line);
    $sex = (int)$sex;
    if ($i < $student_num){ // 学生
        if ($i < $undergrad_num){ // 学部生
            $dept_code = 'LT';
            $dept_name = '生体医工学部 生体工学科';
            list($yy, $num)= $i < $s21 ? [21,$id] : [22, $id-$s21];
            $sid = sprintf('%d%s%03d', $yy, $dept_code, $num) ;
        }else{ // 院生
            $j = $i + 1 - $undergrad_num;
            list($yy, $dept_code, $num) = $j <= $g21 ? [21,'GLT',$j] :
            ($j <= $g21 + $g22 ? [22,'GLT',$j-$g21] : [22,'DLT',$j-$g21-$g22]); 
            $sid = sprintf('%d%s%02d', $yy, $dept_code, $num) ;
            $dept_name = '生体医工学研究科 生体工専攻';
        }
        $uid = 'k' . strtolower($sid);
        $email = $uid . '@st.kyusan-u.ac.jp';
        $category = 1;  
    }else{ // 教育職員
        $sid = rand(105407,119899);  
        $uid = sprintf('t%04d', $id);
        $email = $uid . '@ip.kyusan-u.ac.jp';
        if (rand(1,10) <= 8){ //教員80%
            $dept_code = 'LT';
            $dept_name = '生体医工学部 生体工学科';
            $category = 2;
        }else{//職員20%
            $dept_code = 'AIC';
            $dept_name = '総合機器センター';
            $category = 3;
        }
    } 
    $authority = 1; // 1:予約権なし

    foreach(array_keys($member) as $key){
        $member[$key] = $$key; // $$key: 変数$keyの持つ値（例:'hoge'）を変数名($hoge)にする
    }
    $members[] = $member;   
}
// echo '<pre>';print_r($members);echo '</pre>';

// 4. 教職員
$staff =  [
    'member_id'=>'','role_title'=>1,'role_rank'=>1,'room_no'=>'','tel_ext'=>'',
];
$s_title =[1=>'大学教育職員',2=>'事務職員',3=>'職員'];
$s_rank  = [1=>'教授',2=>'准教授',3=>'講師',4=>'助教',5=>'職員'];
$staffs = [];

foreach(array_slice($members, $student_num, $staff_num) as $row){
    $member_id = $row['id'];
    $r_title = rand_prob([1=>80, 2=>15, 3=>5]);//教育職員80%
    $r_rank = $r_title==1 ? rand(1,4) : 5;
    $role_title = $s_title[$r_title]; // 役職1:大区分
    $role_rank = $s_rank[$r_rank];   // 役職2:中区分（主に教育職員）
    list($b, $f, $r) = [rand(7,12), rand(4,8), rand(10,30)];
    $room_no = sprintf('%d号館%d階%d%d号室', $b, $f, $f, $r);
    $tel_ext = rand(5401, 5899); // 内線番号
    
    foreach(array_keys($staff) as $key){
        $staff[$key] = $$key;
    }
    $staffs[] = $staff; 
}

// 5. ユーザアカウント
$users =[];
foreach ($members as $row){
    $users[] = [
        'uid'=>$row['uid'], 'urole'=>$row['category'], 'uname'=>$row['ja_name'],
        'upass'=>'1234'
    ];
}

// 6. 予約情報
$instruments = [1,2,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,21,22,23,24,25,26,28,29,30,31,32,33,34,35,36,37,38,39,40,41,46];
$r_purposes = ['実験','ゼミ', '見学', '授業'];
$r_samples = ['キノコ','牛乳','パン','ケーキ','牛肉','サンドイッチ'];
$r_status= [1=>'申請中', 2=>'承認済', 3=>'却下'];
$year = 2024; 
$timeslices=[//予約時間の選択肢
    ['9:00','12:40'],
    ['13:40','15:20'],
    ['15:40','19:20'],
    ['19:40','21:20'],
];
$reserves = [];
$rsv_samples = [];
$rsv_members = [];
$student_mids = range(1,100);
$staff_mids = range(101,120);
$member_ids = range(1,120);
$xray_chk = 0; 
$xray_num = '';
$reserve_id = 1; 
$reserved = date('Y-m-d H:i');
$reserve=[
    'code'=>'', 'instrument_id'=>0, 'apply_mid'=>'', 'master_mid'=>'', 'purpose'=>'',
    'stime'=>'', 'etime'=>'','sample_name'=>'', 'sample_state'=>0,
    'xray_chk'=>0, 'xray_num'=>'','process_status'=>1, 'reserved'=>'',
];
$rsv_member=[
    'reserve_id'=>0, 'member_id'=>0, 
];
$rsv_sample=[
    'reserve_id'=>0, 'nature'=>0, 'other'=>'',
];
$no = 0;
foreach ($instruments as $instrument_id){
    // srand(time()); // uncomment this line if you wish the results change every time
    foreach (range(3,5) as $month){
        $t = date('t', strtotime($year.'-'.$month.'-1'));//今月の日数
        foreach(range(1, $t) as $d){
            $n = rand(-1, 4); 
            if ($n < 1) continue; // skip days (30%) w/o any reserve
            $sampled = sample($timeslices, $n);
            foreach ($sampled as $time){
                $apply_mid = rand(1, 100);
                $master_mid = rand(101, 120);
                $purpose = sample($r_purposes)[0];
                $date = sprintf('%d-%d-%d', $year, $month, $d);
                $stime = $date . ' ' . $time[0];
                $etime = $date . ' ' . $time[1];
                $sample_name = sample($r_samples)[0];  
                $sample_state = rand(1,3);
                $process_status = rand(1,3);                
                $code = sprintf('%04d%04d', date('Y'), ++$no);
                foreach(array_keys($reserve) as $key){
                    $reserve[$key] = $$key;
                }
                
                $reserves[] = $reserve; 
                
                // rsv_members
                $n = rand(1,6);
                $r_members= sample($member_ids, $n);
                foreach($r_members as $member_id){
                    foreach(array_keys($rsv_member) as $key){
                        $rsv_member[$key] = $$key;
                    }
                    $rsv_members[] = $rsv_member; 
                }

                // rsv_samples
                $rsv_sample['reserve_id'] = $reserve_id;
                $n = rand(1,2);
                $vals = sample(range(1, 4), $n);
                foreach ($vals as $val){
                    $rsv_sample['nature'] = $val;
                    $rsv_sample['other'] = $val==4 ? '取り扱い注意': '';
                    $rsv_samples[] = $rsv_sample;            
                } 

                $reserve_id++;
            }
 
        } 
    }
}

// Output:
header('Content-Type: text/plain'); 

$tosql = true;
$debug = false;
if ($tosql){
    // echo toSQL('tb_member', $members), ';', PHP_EOL ;
    // echo toSQL('tb_staff', $staffs), ';', PHP_EOL;
    // echo toSQL('tb_user', $users), ';', PHP_EOL;
    echo toSQL('tb_reserve', $reserves), ';', PHP_EOL;
    // echo toSQL('rsv_member', $rsv_members), ';', PHP_EOL;
    // echo toSQL('rsv_sample', $rsv_samples), ';', PHP_EOL;
}

if ($debug){
    // disp_table($members);
    // disp_table($staffs);
    // disp_table($reserves);
}