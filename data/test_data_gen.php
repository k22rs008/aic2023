<?php
/**
 * テストデータ生成：
 * 1. 架空の氏名（男性80名、女性40）を生成。以下のツールを使用
 *    http://www-dx.ip.kyusan-u.ac.jp/ksu/autodata/index.php
 * 2. データを読み込み、 偏らないようにシャフルする
 * 3. 会員(member)データを120行生成（学生100+教育職員20）
 * 4. 教育職員(staff)データを20行生成(教育職員15+事務職員5)
 * 5. 会員データに基づきユーザアカウントを作成
 * 6. 予約(reserve)データを生成(40機器X2ヶ月X30日X1日数件)
 */

// 2. データを読み込み、 偏らないようにシャフルする
$persons = file('test_data_persons.txt');
srand(20240224); // 乱数順を固定にする
shuffle($persons);

$undergrad_num = 90;// 学部生数
$postgrad_num = 10; // 院生数
$student_num  = $undergrad_num + $postgrad_num; // 学生総数
$staff_num = 20; // 教育職員数
$s21= 43;// 学籍番号が「21LT」で始まる学生数（残りは「22LT」）
$g21= 4; // 学籍番号が「21GLT」で始まる前期課程学生数
$g22= 5; // 学籍番号が「22GLT」で始まる前期課程学生数(残りは後期課程「22DLT」)

// 3. 会員（学部生90＋大学院生10＋教育職員20）
$members =[];
$member=[
    'id'=>0,
    'uid'=>'','sid'=>'','email'=>'','tel_no'=>'',
    'ja_name'=>'','sex'=>1, 'dept_code'=>'', 'category'=>1,'authority'=>1,
];

for ($i=0; $i <count($persons); $i++) {
    $id = $i + 1;
    $line = trim($persons[$i]);
    list($ja_name, $sex, $tel_no) = explode(',',$line);
    $sex = (int)$sex;
    if ($i < $student_num){ // 学生
        if ($i < $undergrad_num){ // 学部生
            $dept_code = 'LT';
            if ($i < $s21) {
                list($yy, $num)=[21,$id];
            }else{
                list($yy, $num)=[22, $id-$s21];
            }
            $sid = sprintf('%d%s%03d', $yy, $dept_code, $num) ;
        }else{ // 院生
            $_id = $i + 1 - $undergrad_num;
            if ($_id <= $g21) {
                list($yy, $dept_code, $num) = [21,'GLT',$_id];
            }elseif ($_id <= $g21 + $g22) {
                list($yy, $dept_code, $num) = [22,'GLT',$_id-$g21];
            }else {
                list($yy, $dept_code, $num) = [22,'DLT',$_id-$g21-$g22];
            }
            $sid = sprintf('%d%s%02d', $yy, $dept_code, $num) ;
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
            $category = 2;
        }else{//職員20%
            $dept_code = 'AIC';
            $category = 3;
        }
    } 
    $authority = 1; // 1:予約権なし

    foreach(array_keys($member) as $key){
        $member[$key] = $$key;
    }
    $members[] = $member;   
}
// echo '<pre>';print_r($members);echo '</pre>';


// 4. 教職員
$staff =  [
    'member_id'=>'','title'=>1,'rank'=>1,'room_no'=>'','tel_ext'=>'',
];
$s_title =[1=>'大学教育職員',2=>'事務職員',3=>'職員'];
$s_rank  = [1=>'教授',2=>'准教授',3=>'講師',4=>'助教',5=>'職員'];
$staffs = [];

foreach(array_slice($members, $student_num, $staff_num) as $row){
    $member_id = $row['id'];
    $_t = rand(1, 10) < 8 ? 1 : rand(2,3);//教育職員80%
    $_r = $_t==1 ? rand(1,4) : 5;
    // $title = $s_title[$_t]; // 役職1:大区分
    // $rank = $s_rank[$_r];   // 役職2:中区分（主に教育職員）
    $title = $_t;
    $rank = $_r;
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

function toSQL($table, $data)
{
    if (!$data) return null;
    $row = $data[0];
    $fields = implode(',',array_keys($row));    
    $sql = sprintf('INSERT INTO %s (%s) VALUES' . PHP_EOL, $table, $fields);
    $rows = [];
    foreach ($data as $row){
        foreach ($row as $k => $v){
            if (is_string($v)){
                $row[$k] = "'" . $v. "'";
            }
        }
        $rows[] = sprintf('(%s)', implode(',', array_values($row)));
    }
    return $sql . implode(',' . PHP_EOL, $rows);
}

header('Content-Type: text/plain'); 
// 6. 予約情報
$instruments = [1,2,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,21,22,23,24,25,26,28];
$purposes = ['実験','ゼミ', '見学', '授業'];
$samples = ['キノコ','牛乳','パン','ケーキ','牛肉','サンドイッチ'];
$year = 2024; 
$timeslices=[
    ['9:00','10:40'],
    ['11:00','12:40'],
    ['13:00','15:20'],
    ['15:40','17:20'],
    ['17:40','19:20'],
];

foreach ($instruments as $instrument_id){
    srand(floor(time() / (60*60*24)));
    foreach (range(3,5) as $month){
        $t = date('t', strtotime($year.'-'.$month.'-1'));
        echo $t . PHP_EOL;
        foreach(range(1, $t) as $d){
            $n = rand(-1,4);
            if ($n < 1) continue;
            $r = array_rand($timeslices, $n);
            if (!is_array($r)) $r=[$r];
            foreach ($r as $_r){
                $time = $timeslices[$_r];
                $date = sprintf('%d-%d-%d', $year, $month, $d);
                $stime = $date . ' ' . $time[0];
                $etime = $date . ' ' . $time[1];
                printf('%d: %s - %s' . PHP_EOL,$instrument_id, $stime, $etime);
            }
        } 
    }
}
// echo toSQL('tb_member', $members), ';', PHP_EOL ;
// echo toSQL('tb_staff', $staffs), ';', PHP_EOL;
// echo toSQL('tb_user', $users), ';', PHP_EOL;

// Output:
$debug = false;
if ($debug){

    echo '<table>'. PHP_EOL;
    echo "<tr>";
    foreach(array_keys($member) as $key){
        echo sprintf('<td>%s</td>',$key);
    } 
    echo " </tr>", PHP_EOL;
    foreach ($members as $row){
        echo "<tr>";
        foreach(array_values($row) as $val){
            echo sprintf('<td>%s</td>',$val);
        }
        echo " </tr>", PHP_EOL;
    }
    echo '</table>'. PHP_EOL;

    echo '<table>'. PHP_EOL;
    echo "<tr>";
    foreach(array_keys($staff) as $key){
        echo sprintf('<td>%s</td>',$key);
    } 
    echo " </tr>", PHP_EOL;
    foreach ($staffs as $row){
        echo "<tr>";
        foreach(array_values($row) as $val){
            echo sprintf('<td>%s</td>',$val);
        }
        echo " </tr>", PHP_EOL;
    }
    echo '</table>'. PHP_EOL;
}