<?php
include 'models/user.php';
if (isset($_POST['userlogin'], $_POST['password'])){
    $userlogin = $_POST['userlogin'];
    $password = $_POST['password'];   // パスワード
    $info = (new User)->ldap_check($userlogin, $password);
    if ($info){
        echo '<pre>';print_r($info); echo '</pre>';
    }else{
        echo '<h3>LDAP認証失敗しました。';
    }
}
?>

<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body>
<form name="my_form" method="post">
<table>
  <tr><td>id:</td><td><input type="text" name="userlogin" placeholder="k99rs999"></td></tr>
  <tr><td>pw:</td><td><input type="password" name="password"></td></tr>
</table>
 <input type="submit" value="送信" />
</form>
</body></html>