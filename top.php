

<?php

//var_dump($_POST);
//var_dump($_SESSION);


require_once('db_helper.php');

// 変数の初期化
$page_flag = 0;
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

  
$member = $_SESSION['member'];
$dbh = get_db_connect();

//ログイン済みの場合

if( !empty($_SESSION['member']) ) {
	$page_flag = 1;
	session_start();
	$_SESSION['page'] = true;	
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
<title>スレッド</title>
<link rel="stylesheet" href="style.css">

</head>

<body>


<?php if( $page_flag === 1 ): ?>

<div class="header">
	<div class = "left-column">
		<p>ようこそ<?php echo html_escape($member['name_sei']); ?>様</p>
	</div>

	<div class= "right-column">
		<input class="button" type="button" onclick="location.href='./thread_view.php'"value="スレッド一覧">
		<input class="button" type="button" onclick="location.href='./thread_regist.php'"value="新規スレッド作成">
		<input class="button" type="button" onclick="location.href='./logout.php'"value="ログアウト">
	</div>

</div class="header">

<div class="main">
	<h1>◯◯掲示板</h1>
</div>

<div class="footer">
	<div class= "right-column">
		<form action="withdrawal.php">
		<input type="hidden" name="example" value="<?php echo $member['email'] ?>">
		<input class="button" type="button" onclick="location.href='./withdrawal.php'" value="退会">
		</form>
	</div>

</div>



<?php else: ?>

<div class="header">
	<div class= "right-column">
		<input class="button" type="button" onclick="location.href='./thread_view.php'"value="スレッド一覧">
		<input class="button" type="button" onclick="location.href='./member_regist.php'"value="新規会員登録">
		<input class="button" type="button" onclick="location.href='./login.php'"value="ログイン">
	</div>

</div class="header">

<div class="main">
	<h1>◯◯掲示板</h1>
</div>


<?php endif; ?>

</body>
</html>