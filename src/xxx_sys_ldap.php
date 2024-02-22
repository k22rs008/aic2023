<?php
function kind_ldap($userlogin, $password, $host=null){
    if (!$host){
        $host = "ldap1.ip.kyusan-u.ac.jp";  //LDAPサーバのホスト
    }
    $conn = ldap_connect($host);
    if (!$conn) return false;

    ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);

    $base = "ou=userall,dc=kyusan-u,dc=ac,dc=jp";
    $dn = "uid=" . $userlogin . "," . $base;
    $ldap_bind = @ldap_bind($conn, $dn, $password);
    if($ldap_bind){  //認証成功処理
        $user = $userlogin;
        $filter = "uid={$user}"; // 自分以外のユーザ情報も検索可能！
        $result = ldap_search($conn, $base, $filter);
        if (ldap_count_entries($conn, $result) > 0){
            $entries = ldap_get_entries($conn, $result);
            $info = $entries[0];
            /**
             * 'uid'(ログインID) 'mail'(メールアドレス), 
             * 'cn'(英語名), 'jadisplayname'（漢字氏名）、'jasn'（日本語読み）
             * 'initials'（大種別：大学教職員、学生）、'businesscategory'(教育職員)、
             * 'carlicense'（運転免許？ 内容は学部・学科名) 
             */
            $record = [];
            foreach ( 
                ['uid'=>'uid',//※ユーザID
                'sambasid'=>'sid',//※学籍番号・職員番号
                'mail'=>'email',//※メールアドレス
                'cn'=>'en_name',//※英語氏名
                'sn'=>'en_yomi',//※英語読み
                'jadisplayname'=>'ja_name',//※日本語氏名
                'jasn' =>'ja_yomi',//※日本語読み
                /////////////////////////////////
                'jao'=>'dept',//所属。学生の場合。例、情報科学科
                'description'=>'category', //カテゴリ。学生の場合。 例：一般学生、
                /////////////////////////////////
                'labeleduri'=>'rank', // 役職1。教職員の場合。例：教授、准教授
                'initials'=>'title', //役職2。教職員の場合。例：学部長、学科主任、大学教育職、その他職員
                'businesscategory'=>'category',//教職員の場合。例：教育職員、事務職員、業務特別契約職員
                'carlicense'=>'dept',//所属。教職員の場合。例：理工学部情報科学科、産学連携支援室
                ] as $key=>$item)
                {
                    $record[$item]= $info[$key];   
            }
            return $record;
        }
    }
    ldap_close($conn);
    return false;
}