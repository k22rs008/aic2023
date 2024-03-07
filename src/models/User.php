<?php
namespace aic\models;

use aic\models\Member;
use aic\models\Staff;
use aic\models\KsuCode;

class User extends Model {
    protected $table = "tb_user";    
    const ldap_host = "ldap1.ip.kyusan-u.ac.jp";
    const ldap_base = "ou=userall,dc=kyusan-u,dc=ac,dc=jp";

    const LDAP_ENTRIES = [
        #LDAP ENTRY => New NAME
        'uid'=>'uid',//※ユーザID
        'sambasid'=>'sid',//※学籍番号・職員番号
        'mail'=>'email',//※メールアドレス
        'jadisplayname'=>'ja_name',//※日本語氏名
        'jasn' =>'ja_yomi',//※日本語読み
        'cn'=>'en_name',//※英語氏名
        'sn'=>'en_yomi',//※英語読み
        'jagivenname'=>'faculty', //所属学部。例、理工、芸術
        'jao'=>'dept',//所属学科。学生の場合。例、情報科学科
        'jaou'=>'course',
        'description'=>'category', //カテゴリ。学生の場合。 例：一般学生、
        'labeleduri'=>'role_rank', // 役職1。教職員の場合。例：教授、准教授
        'initials'=>'role_title', //役職2。教職員の場合。例：学部長、学科主任、大学教育職、その他職員
        'businesscategory'=>'category',//教職員の場合。例：教育職員、事務職員、業務特別契約職員
        'carlicense'=>'dept',//所属。教職員の場合。例：理工学部情報科学科、産学連携支援室
    ];
    const LDAP_NAMES=[
        'uid'=>'ログインID',
        'sid'=>'会員番号',
        'email'=>'メールアドレス',
        'ja_name'=>'日本語氏名',
        'ja_yomi'=>'日本語読み',
        'en_name'=>'英語氏名',
        'en_yomi'=>'英語読み',
        'dept'=>'所属',
        'role_title'=>'種別',
        'role_rank'=>'役職',
        'category'=>'身分',
        'course'=>'専攻・コース',
        'faculty'=>'部署',
    ];
    public function getDetail($id)
    {
        $conn = $this->db; 
        $sql = sprintf("SELECT * FROM %s WHERE uid='{$id}'", self::$table);
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        return $rs->fetch_assoc(); 
    }

    public function getList($where=1, $orderby='uid',$page=0)
    {
        return parent::getList($where, $orderby);
    }

    public function getLoginUid()
    {
        if (isset($_SESSION['uid'])){
            return $_SESSION['uid'];
        }
        return null;
    }
    public function getLoginMid()
    {
        if (isset($_SESSION['member_id'])){
            return $_SESSION['member_id'];
        }
        return null;
    }
    public function getMemberAuthority()
    {
        if (isset($_SESSION['member_authority'])){
            return $_SESSION['member_authority'];
        }
        return null;
    }

    public function getLoginRole()
    {
        if (isset($_SESSION['urole'])){
            return $_SESSION['urole'];
        }
        return null;
    }

    public function getLoginMemberId()
    {
        if (isset($_SESSION['member_id'])){
            return $_SESSION['member_id'];
        }
        return null;
    }

    public function canReserve()
    {
        return ($this->getLoginRole() and $this->getMemberAuthority());
    }

    public function isAdmin()
    {
        $urole = $this->getLoginRole();        
        return ($urole==9);
    }
    public function isStaff()
    {
        $urole = $this->getLoginRole();        
        return ($urole > 1 and $urole<9);
    }
    
    public function isOwner($mbr_id)
    {
        if ($this->getLoginMemberId()){
            return ($this->getLoginMemberId() === $mbr_id);
        }
        return false;
    }

    public function check($userid, $passwd)
    {
        $conn = $this->db; 
        $userid = htmlspecialchars($userid);
        $passwd = htmlspecialchars($passwd);
        $sql = "SELECT * FROM %s WHERE md5(uid)='%s' AND upass='%s'";
        $sql = sprintf($sql, $this->table, md5($userid), md5($passwd));
        $rs = $conn->query($sql);
        if (!$rs) die('エラー: ' . $conn->error);
        $row = $rs->fetch_assoc();
        return $row;
    }

    public function ldap_check($userid, $passwd)
    {
        $dn = "uid=" . $userid . "," . self::ldap_base;
        $ldap = ldap_connect(self::ldap_host);
        if(!$ldap) return false;
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);        
        $ldap_bind = @ldap_bind($ldap, $dn, $passwd);
        if(!$ldap_bind)  return false;
        // $target = 'k23gjk03'; // 他ユーザ情報の取得 
        $target = $userid;// 本人認証 
        $filter = "uid={$target}";
        $result = ldap_search($ldap, self::ldap_base, $filter);
        $record = [];
        if (ldap_count_entries($ldap, $result) > 0){
            $info = ldap_get_entries($ldap, $result);
            $info = $info[0];
            foreach (self::LDAP_ENTRIES as $key=>$item){
                if (isset($info[$key])){
                    $record[$item]= $info[$key][0];
                } 
            } 
        }
        return $record;                
    }

    public function updateLoginTime($uid, $time)
    {
        $sql = sprintf("UPDATE %s SET last_login='%s' WHERE uid='%s'", $this->table, $time, $uid);
        $rs = $this->db->query($sql);
        if (!$rs) die('エラー: ' . $this->db->error);
        return $this->db->affected_rows; 
    }

    public function addLdapUser($info)
    {
        $category = 4; // その他職員
        $urole = 0;
        if ($info['category']=='一般学生') {
            $category = $urole = 1;
        }
        if ($info['category']=='教育職員') {
            $category = $urole = 2;
        }
        if ($info['category']=='事務職員') {
            $category = $urole = 3;
        }
        $uid = $info['uid'];
        $sid = $info['sid'];
    
        $student = KsuCode::parseSid($sid);
        if ($student){
            $dept_code = $student['dept_code'];
            $dept_name = $student['dept_name'];
        }else{
            $dept_code = 'N/A';
            $dept_name = $info['dept'];
        }
      
        $user = [
            'uid'=>$uid, 'uname'=>$info['ja_name'], 'urole'=>$urole,
            'last_login'=>date('Y-m-d H:i:s'),
        ];
        $this->write($user);

        $member = [
            'id'=>0,
            'uid'=>$uid, 'sid'=>$sid,'email'=>$info['email'],
            'dept_code'=>$dept_code,'dept_name'=>$dept_name,
            'ja_name'=>$info['ja_name'],'ja_yomi'=>$info['ja_yomi'],
            'en_name'=>$info['en_name'],'en_yomi'=>$info['en_yomi'], 
            'category'=>$category, 'authority'=>1,
        ];
        $member_id = (new Member)->write($member);
        if ($category > 1){ //教職員
            $staff = [
                'id'=>0,
                'member_id'=>$member_id, 'role_title'=>$info['role_title'],'role_rank'=>$info['role_rank'],
            ];
            (new Staff)->write($staff);
        }
        return (new Member)->getDetail($member_id);
    }
}
