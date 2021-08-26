

<?php
//var_dump($_POST);
ini_set("display_errors", 1);
error_reporting(E_ALL);
require_once('db_helper.php');


// 変数の初期化
$page_flag = 0;
$data = null;
$_SESSION = array();
$error = array();
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


//データ挿入
function insert_thread_data($dbh,$member_id,$title,$content){
	$dbh =get_db_connect();
    $sql = "INSERT INTO thread (member_id,title,content) VALUES (:member_id,:title,:content)";
    $stmt = $dbh->prepare($sql);
	$stmt->bindParam( ':member_id', $member_id, PDO::PARAM_STR);
    $stmt->bindParam( ':title', $title, PDO::PARAM_STR);
	$stmt->bindParam( ':content', $content, PDO::PARAM_STR);
	if($stmt->execute()){
        return TRUE;
    }else{
        return FALSE;
    }

}



//ログイン済みのか判断
if(empty($_SESSION['member'])){
	header('Location: https://153.127.18.207/login.php');
	exit;
  }
  
  $dbh = get_db_connect();


if( !empty($_POST['btn_confirm']) ) {
	$error = validation_thread($_SESSION,$member,$dbh);
	if( empty($error) ) {
	$page_flag = 1;
	// セッションの書き込み
	//session_start();
	$_SESSION['page'] = true;	
	}

}elseif( !empty($_POST['btn_submit']) ) {
	
	session_start();
	if( !empty($_SESSION['page']) && $_SESSION['page'] === true ) {

		// セッションの削除
		unset($_SESSION['page']);

		if(insert_thread_data($dbh,$member['id'],$_SESSION['title'],$_SESSION['content'])){
			header('Location: https://153.127.18.207/thread_view.php');
			exit;
		}
		
	}

	error_reporting(E_ALL);

}


function validation_thread($data,$member,$dbh) {

	$error = array();

	// スレッドタイトルのバリデーション
	if( empty($data['title']) ) {
		$error[] = "タイトルは必須入力です";
	}elseif( 100 < mb_strlen($data['title']) ) {
		$error[] = "タイトルは100文字以内で入力してください。";
	}

	// コメントのバリデーション
	if( empty($data['content']) ) {
		$error[] = "コメントは必須入力です";
	}elseif( 500 < mb_strlen($data['content']) ) {
		$error[] = "コメントは500文字以内で入力してください。";
	}

	return $error;
	return $member;
}




?>

<!DOCTYPE>
<html lang="ja">
<head>
<title>スレッド</title>
<link rel="stylesheet" href="style.css">

</head>

<body>


<?php if( $page_flag === 1 ): ?>
<div class="back_color">
	<h1>スレッド作成確認画面</h1>
	<div class="text_center">
	<form method="post" action="">
		<div class="element_wrap">
			<label>スレッドタイトル</label>
			<p><?php echo $_POST['title']; ?></p>
		</div>

		<div class="element_wrap">
			<label>コメント</label>
			<p><?php echo nl2br($_POST['content']); ?></p>
		</div>
		
		<button type="button" name="btn_back" onclick=history.back()>戻る</button>
		<input type="submit" name="btn_submit" value="スレッドを作成する">
	</form>
	</div>
</div>

<?php else: ?>

<?php if( !empty($error) ): ?>
	<ul class="error_list">
	<?php foreach( $error as $value ): ?>
		<li><?php echo $value; ?></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
<div class="back_color">
	<div class="text_center">
	<h1>スレッド作成フォーム</h1>

	<form method="post" action="">
		<div class="element_wrap">
			<label>スレッドタイトル</label>
			<input type="text" name="title" value="<?php if( !empty($_POST['title']) ){ echo $_POST['title']; } ?>">
			
		</div>

		<div class="element_wrap">
			<label>コメント</label>
			<textarea name="content"><?php if( !empty($_POST['content']) ){ echo $_POST['content']; } ?></textarea>
		</div>

		<input type="submit" name="btn_confirm" value="確認画面へ">
		<br>
		<input type="button" name="btn_top" onclick="location.href='./thread_view.php'"value="一覧に戻る">
	</form>
	</div>
</div>
<?php endif; ?>

</body>
</html>