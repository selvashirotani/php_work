<?php
session_start();


if( !empty($_POST['btn_submit']) ) {
    if( !empty($_SESSION['page']) && $_SESSION['page'] === true ) {

    }
}

var_dump($_POST)
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
        <input type="hidden" name="example" value="認識したよ">
        <input type="submit" name="btn_submit" value="登録完了">
    </form>
</div>
</body>
</html>