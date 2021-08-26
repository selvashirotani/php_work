<?php

ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once('db_helper.php');
session_start();


$dbh = get_db_connect();

if( empty($_SESSION['member']) ) {
    header('Location: https://153.127.18.207/top.php');

}

if( !empty($_POST['btn_submit']) ) {
    if(withdrawal_member($dbh,$_SESSION['member']['email'],$_SESSION['member']['id'])){
        unset($_SESSION['member']);
        header('Location: https://153.127.18.207/top.php');
        exit;
    }
}

//var_dump($_SESSION['member']['email']);
//var_dump($_SESSION['member']['id']);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<title>スレッド</title>
<link rel="stylesheet" href="style.css">

</head>

<body>
<div class="header">
	<div class= "right-column">
		<input class="button" type="button" onclick="location.href='./top.php'"value="トップに戻る">
	</div>

</div class="header">

<div class="main">
	<p>退会</p>
    <p>退会しますか？</p>
    <form method="post" action="">
        <input type="submit" name="btn_submit" value="退会する">
    </form>
</div>
</body>
</html>