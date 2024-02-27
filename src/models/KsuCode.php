<?php
//namespace ksu\aic;

class KsuCode{

    const USER_ROLE = [1=>'学生', 5=>'教員', 9=>'管理者'];
    const INST_STATE = [1=>'使用可',2=>'貸出中',3=>'使用不可',9=>'その他'];
    const INST_CATEGORY =[1=>'観察', 2=>'分析',3=>'計測',4=>'調製',9=>'その他'];
    const RSV_STATUS = [1=>'申請中', 2=>'審査中', 3=>'承認済', 9=>'拒否'];
    const RSV_STYLE = [1=>'red', 2=>'green', 3=>'blue', 9=>'black'];   
    const STAFF_RANK  = [1=>'教授',2=>'准教授',3=>'講師',4=>'助教'];
    const STAFF_TITLE =[1=>'大学教育職員',2=>'事務職員',9=>'その他'];
    const YESNO = [1=>'有',2=>'無'];
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
        'GBE'=>'経済・ビジネス研究科 経済学専攻',
        'GBM'=>'経済・ビジネス研究科 現代ビジネス専攻',
        'GTI'=>'工学研究科 産業技術デザイン専攻',
        'GJK'=>'情報科学研究科 情報科学専攻',
        'GAC'=>'芸術研究科 造形表現専攻',
        'GKK'=>'国際文化研究科 国際文化専攻',

        'DBE'=>'経済・ビジネス研究科 経済学専攻',
        'DBM'=>'経済・ビジネス研究科 現代ビジネス専攻',
        'DTI'=>'工学研究科 産業技術デザイン専攻',
        'DJK'=>'情報科学研究科 情報科学専攻',
        'DAC'=>'芸術研究科 造形表現専攻',
        'DKK'=>'国際文化研究科 国際文化専攻',
    ];
    public static function parseSid($sid){
        $sid = preg_replace("/( |　)/", "", trim($sid) );//空白文字を削除
        $sid = mb_convert_kana($sid, "a");//全角英数を半角英数へ変換
        $sid = strtoupper($sid);//小文字を大文字に変換
        if (strlen($sid) != 7) return null;//正しい学生番号ではない
        if (preg_match('/^(\d{2})('.implode('|', array_keys(self::FACULTY_DEPT)) .')(\d+)$/', $str, $matches)){
            $stud_yr = 20+$matches[1];
            $dept_id = $matches[2];
            $stud_no = $matches[3];
            $dept_name = self::FACULTY_DEPT[$dept_id];
            return [$sid, $stud_yr, $dept_id, $stud_no, $dept_name];
        }
        return null;
    }
}