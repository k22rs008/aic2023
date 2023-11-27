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
            foreach (['uid'=>'uid',
                'mail'=>'email',
                'cn'=>'en_name',
                'jadisplayname'=>'ja_name',
                'jasn' =>'ja_yomi',
                'initials'=>'category',
                'businesscategory'=>'subcategory',
                'carlicense'=>'dept',
                ] as $key=>$item){
                     $record[$item]= $info[$key];   
            }
            return $record;
        }
    }
    ldap_close($conn);
    return false;
}