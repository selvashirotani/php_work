<?php
//ini_set("display_errors", 1);
//error_reporting(E_ALL);
//var_dump($_POST);
//var_dump($_SESSION);

require_once('db_helper.php');

session_start();

if(!empty($_SESSION['member'])){
	header('Location: https://153.127.18.207/top.php');
	exit;
  }
  


if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $email = get_post('email');
  $password = get_post('password');

  $dbh = get_db_connect();
  $errs = array();


  // メールアドレスのバリデーション
  if(!check_words($email,200)){
    $errs['password'] = "IDもしくはパスワードが間違っています。";
  }elseif (!email_exists($dbh,$email)) {
		$errs['password'] = "IDもしくはパスワードが間違っています。";
	}

  // パスワードのバリデーション
  if(!check_words($password,20)){
    $errs['password'] = "IDもしくはパスワードが間違っています。";
  }if(!$member = select_member($dbh,$email,$password)){
    $errs['password'] = "IDもしくはパスワードが間違っています。";
  }

  if(empty($errs)){
    $_SESSION['member'] = $member;
    header('Location: https://153.127.18.207/top.php');
    exit;
  }

}

include_once('login_view.php');
?>