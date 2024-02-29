<?php
namespace aic\models;

class KsuCode{

    const PAGE_ROWS = 10;
    const USER_ROLE = [1=>'学生', 5=>'教員', 9=>'管理者'];
    const INST_STATE = [1=>'使用可',2=>'貸出中',3=>'使用不可',9=>'その他'];
    const INST_CATEGORY = [1=>'観察', 2=>'分析',3=>'計測',4=>'調製',9=>'その他'];
    const RSV_STATUS = [1=>'申請中', 2=>'承認済', 3=>'却下済'];
    const RSV_STYLE = [1=>'red', 2=>'green', 3=>'blue', 9=>'black']; 
    const MBR_SEX = [0=>'未登録',1=>'男性',2=>'女性'];
    const MBR_CATEGORY = [1=>'一般学生',2=>'教育職員',3=>'事務職員',9=>'その他職員'];  
    const MBR_AUTHORITY = [0=>'予約権無',1=>'予約権有'];   
    const STAFF_RESPONSIBLE  = [0=>'責任者不可',1=>'責任者可'];
    const STAFF_RANK  = [1=>'教授',2=>'准教授',3=>'講師',4=>'助教',5=>'職員'];
    const STAFF_TITLE = [1=>'大学教育職員',2=>'事務職員',9=>'その他職員'];

    const YESNO = [0=>'無', 1=>'有'];
    const SAMPLE_STATE = [1=>'固体',2=>'液体',3=>'気体'];
    const SAMPLE_NATURE = [1=>'爆発性',2=>'毒性',3=>'揮発性',4=>'その他'];


    const FACULTY_DEPT =[  
        //学科のIDと名称
        'RS'=>'理工学部 情報科学科',
        'RM'=>'理工学部 機械工学科',
        'RE'=>'理工学部 電気工学科',        
        'LL'=>'生命科学部 生命科学科',        
        'UA'=>'建築都市工学部 建築学科',
        'UH'=>'建築都市工学部 住居・インテリア学科',
        'UC'=>'建築都市工学部 都市デザイン工学科',
        'CB'=>'商学部 経営・流通学科',
        'EE'=>'経済学部 経済学科',        
        'DT'=>'地域共創学部 観光学科',
        'DR'=>'地域共創学部 地域づくり学科',
        'AA'=>'芸術学部 芸術表現学科',
        'AP'=>'芸術学部 写真・映像メディア学科',
        'AD'=>'芸術学部 ビジュアルデザイン学科',
        'AE'=>'芸術学部 生活環境デザイン学科',
        'AS'=>'芸術学部 ソーシャルデザイン学科',        
        'KK'=>'国際文化学部 国際文化学科',
        'KN'=>'国際文化学部 日本文化学科',        
        'HP'=>'人間科学部 臨床心理学科',
        'HC'=>'人間科学部 子ども教育学科',
        'HS'=>'人間科学部 スポーツ学科',
    
        //大学院のIDと名称  
        'GBE'=>'経済・ビジネス研究科（M） 経済学（前）',
        'GBM'=>'経済・ビジネス研究科（M） 現代ビジネス（前）',
        'GTI'=>'工学研究科（M） 産業技術デザイン（前）',
        'GJK'=>'情報科学研究科（M） 情報科学（前）',
        'GAC'=>'芸術研究科（M） 造形表現（前）',
        'GKK'=>'国際文化研究科（M） 国際文化（前）',

        'DBE'=>'経済・ビジネス研究科（D） 経済学（後）',
        'DBM'=>'経済・ビジネス研究科（D） 現代ビジネス（後）',
        'DTI'=>'工学研究科（D） 産業技術デザイン（後）',
        'DJK'=>'情報科学研究科（D） 情報科学（後）',
        'DAC'=>'芸術研究科（D） 造形表現（後）',
        'DKK'=>'国際文化研究科（D） 国際文化（後）',

        // その部署
        'AIC'=>'総合機器センター',
        'CNC'=>'総合情報基盤センター',
        'KKC'=>'基礎教育センター',
        'GKC'=>'語学教育研究センター',
        'SGK'=>'産学連携支援室',

        //-- 架空の学部学科
        'LT'=>'生体医工学部 生体工学科',
        'GLT'=>'生体医工学研究科 生体工学専攻・博士前期課程',
        'DLT'=>'生体医工学研究科 生体工学科・博士後期課程',
    ];

    public static function getDeptName($dept_code)
    {
        if (array_key_exists($dept_code, self::FACULTY_DEPT)){
            return self::FACULTY_DEPT[$dept_code];
        }
        return null;
    }
    
    /**
     * Parse $str to student ids
     * e.g., parseSid("21rs017,2")
     */
    public static function parseSid($str)
    {
        $str = trim($str); //空白文字を削除
        $str = mb_convert_kana($str, "a");//全角英数を半角英数へ変換
        $sid = strtoupper($str);//小文字を大文字に変換
        if (strlen($sid) != 7) return null;
        if (preg_match('/^(\d{2})('.implode('|', array_keys(self::FACULTY_DEPT)) .')(\d+)$/', $sid, $matches)){
            $stud_yr = $matches[1];
            $dept_id = $matches[2];
            $stud_no = $matches[3];
            $dept_name = self::FACULTY_DEPT[$dept_id];
            return [
                'sid'=>$sid, 'syear'=>$stud_yr, 'sno'=>$stud_no, 
                'dept_code'=>$dept_id, 'dept_name'=>$dept_name
            ];
        }
        return null;
    }
}