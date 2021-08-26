

<?php

//var_dump($_POST);
//var_dump($_SESSION);


require_once('db_helper.php');

// 変数の初期化
$data = null;
$_SESSION = array();
$errs = array();
$dbh = null;
$stmt = null;
session_start();


// サニタイズ
if( !empty($_POST) ) {
	foreach( $_POST as $key => $value ) {
		$_SESSION[$key] = htmlspecialchars( $value, ENT_QUOTES);
	}
}

  
$administer = $_SESSION['administer'];
$dbh = get_db_connect();


if( empty($_SESSION['administer']) ) {
	header('Location: https://153.127.18.207/login_administer.php');
	session_start();
	$_SESSION['page'] = true;	
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<title>管理画面トップ</title>
<link rel="stylesheet" href="style_administer.css">

</head>

<body>


<div class="header">
	<div class = "left-column">
		<p><strong>掲示板管理画面メインメニュー</strong></p>
		
	</div>

	<div class= "right-column">
		<p>ようこそ<?php echo html_escape($administer['name']); ?>さん</p>
		<input class="button" type="button" onclick="location.href='./logout_administer.php'"value="ログアウト">
	</div>

</div class="header">

<div class="main">
<input type="button" name="btn_submit" onclick="location.href='./member_list.php'" value="会員一覧">
</div>


</body>
</html>