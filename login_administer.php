<?php
//ini_set("display_errors", 1);
//error_reporting(E_ALL);
//var_dump($_POST);
//var_dump($_SESSION);

require_once('db_helper.php');

session_start();



if(!empty($_SESSION['administer'])){
	header('Location: https://153.127.18.207/top_administer.php');
	exit;
  }
  


if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $login_id = get_post('login_id');
  $password = get_post('password');

  $dbh = get_db_connect();
  $errs = array();

  //$name = "管理者";
  //$login_id = "kanrikanri";
  //$password = "kanrikanri";


  //insert_administer_data($dbh,$name,$login_id,$password);

  // メールアドレスのバリデーション
  if(!check_length($login_id,7,10)){
    $errs['password'] = "IDもしくはパスワードが間違っています。";
  }elseif (!login_id_exists($dbh,$login_id)) {
		$errs['password'] = "IDもしくはパスワードが間違っています。";
	}

  // パスワードのバリデーション
  elseif(!check_length($password,8,20)){
    $errs['password'] = "IDもしくはパスワードが間違っています。";
  }elseif(!$administers = select_administer($dbh,$login_id,$password)){
    $errs['password'] = "IDもしくはパスワードが間違っています。";
  }

  if(empty($errs)){
    $_SESSION['administer'] = $administers;
    header('Location: https://153.127.18.207/top_administer.php');
    exit;
  }

}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>管理画面ログイン</title>
    <link rel="stylesheet" href="style.css">
 
</head>

<body>
<div class="back_color">
    <h1>ログインページ</h1>
    <form action="login_administer.php" method="POST">

    <div class="element_wrap">
        <label>ログインID</label>
        <input type="text" name="login_id" value="<?php if( !empty($_POST['login_id']) ){ echo $_POST['login_id']; } ?>">
            
    </div>

    <div class="element_wrap">
        <label>パスワード</label>
        <input type="password" name="password" >
        <p class="error_sentence"><?php echo html_escape($errs['password']); ?></p>
    </div>


    <input name="btn_submit" type="submit" value="ログイン">
    
    </form>
</div>
</body>

</html>