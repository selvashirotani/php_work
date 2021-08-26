

<?php
//var_dump($_POST);
//var_dump($_SESSION);

// 変数の初期化
$page_flag = 0;
$data = null;
$_SESSION = array();
$error = array();
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$dbh = null;
$stmt = null;
$res = null;
session_start();


// サニタイズ
if( !empty($_POST) ) {
	foreach( $_POST as $key => $value ) {
		$_SESSION[$key] = htmlspecialchars( $value, ENT_QUOTES);
	}
}

//データベース接続
function get_db_connect(){
	try {
		$dbh = new PDO('mysql:dbname=User;host=153.127.18.207;charset=utf8', 'root', '5lcmJyu8');
	
	} catch (PDOException $e) {
		echo ($e->getMessage());
		die();
	}
	$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

//データ挿入
function insert_member_data($dbh,$name_sei,$name_mei,$gender,$pref_name,$address,$password,$email){
	$dbh =get_db_connect();
    $password = password_hash($password,PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name_sei,name_mei,gender,pref_name,address,password,email) VALUES (:name_sei,:name_mei,:gender,:pref_name,:address,:password,:email)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue( ':name_sei',$name_sei,PDO::PARAM_STR);
    $stmt->bindParam( ':name_mei', $name_mei, PDO::PARAM_STR);
	$stmt->bindParam( ':gender', $gender, PDO::PARAM_STR);
	$stmt->bindParam( ':pref_name', $pref_name, PDO::PARAM_STR);
	$stmt->bindParam( ':address', $address, PDO::PARAM_STR);
	$stmt->bindParam( ':password', $password, PDO::PARAM_STR);
	$stmt->bindParam( ':email', $email, PDO::PARAM_STR);
	if($stmt->execute()){
        return TRUE;
    }else{
        return FALSE;
    }

}


if( !empty($_POST['btn_confirm']) ) {
	$error = validation($_SESSION,$member,$dbh);
	if( empty($error) ) {
	$page_flag = 1;
	// セッションの書き込み
	session_start();
	$_SESSION['page'] = true;	
	}

}elseif( !empty($_POST['btn_submit']) ) {
	
	session_start();
	if( !empty($_SESSION['page']) && $_SESSION['page'] === true ) {

		// セッションの削除
		unset($_SESSION['page']);

		if(insert_member_data($dbh,$_SESSION['name_sei'],$_SESSION['name_mei'],$_SESSION['gender'],$_SESSION['pref_name'],$_SESSION['address'],$_SESSION['password'],$_SESSION['email'])){
			$page_flag = 2;
		}else {
			$page_flag = 0;
		}
		
	}

	error_reporting(E_ALL);

}

//データベース内のメールアドレスを取得
function email_exists($dbh,$email){
	$sql = "SELECT COUNT(id) FROM users WHERE email = :email";
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(':email', $email,PDO::PARAM_STR);
	$stmt->execute();
	$count = $stmt-> fetch(PDO::FETCH_ASSOC);
	if($count['COUNT(id)']>0){
		return TRUE;
	}else{
		return FALSE;
	}
}

function validation($data,$member,$dbh) {

	$error = array();

	// 氏名のバリデーション
	if( empty($data['name_sei']) ) {
		$error[] = "氏名（姓）は必須入力です";
	}elseif( 20 < mb_strlen($data['name_sei']) ) {
		$error[] = "氏名（姓）は20文字以内で入力してください。";
	}

	if( empty($data['name_mei']) ) {
		$error[] = "氏名（名）は必須入力です";
	}elseif( 20 < mb_strlen($data['name_mei']) ) {
		$error[] = "氏名（名）は20文字以内で入力してください。";
	}

	// 性別のバリデーション
	if( empty($data['gender']) ) {
		$error[] = "性別は必須入力です";
	}elseif( $data['gender'] !== '1' && $data['gender'] !== '2' ) {
		$error[] = "性別は必須入力です";
	}

	// 住所のバリデーション
	if( empty($data['pref_name']) ) {
		$error[] = "都道府県は必須入力です";
	}elseif( (int)$data['pref_name'] < 01 || 47 < (int)$data['pref_name'] ) {
		$error[] = "都道府県は必須入力です。";
	}

	if(100 < mb_strlen($data['address']) ) {
		$error[] = "住所は100文字以内で入力してください。";
	}
	

	// パスワードのバリデーション
	if( empty($data['password']) ) {
		$error[] = "パスワードは必須入力です";
	}elseif( !preg_match( '/^[0-9a-z]/', $data['password']) ) {
		$error[] = "パスワードは半角英数字で入力してください。";
	}elseif( 20 < mb_strlen($data['password']) ) {
		$error[] = "パスワードは8文字以上20文字以内で入力してください。";
	}elseif( 8 > mb_strlen($data['password']) ) {
		$error[] = "パスワードは8文字以上20文字以内で入力してください。";
	}
    
    if($data['password'] != $data['password_conf']){
        $error[]="確認用パスワードと異なっています。";
    }

	// メールアドレスのバリデーション
	$dbh =get_db_connect();
	if( empty($data['email']) ) {
		$error[] = "メールアドレスは必須入力です。";
	}elseif( !preg_match( '/^[0-9a-z_.\/?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$/', $data['email']) ) {
		$error[] = "メールアドレスは正しい形式で入力してください。";
	}elseif( 200 < mb_strlen($data['email']) ) {
		$error[] = "メールアドレス200文字以内で入力してください。";
	}elseif (email_exists($dbh,$data['email'])) {
		$error[] = "メールアドレスが重複しています。";
	}

	return $error;
	return $member;
}




?>

<!DOCTYPE>
<html lang="ja">
<head>
<title>お問い合わせフォーム</title>
<link rel="stylesheet" href="style.css">

</head>

<body>


<?php if( $page_flag === 1 ): ?>

<div class="back_color">

	<h1>会員情報確認画面</h1>

	<form method="post" action="">
		<div class="element_wrap">
			<label>氏名</label>
			<p><?php echo $_POST['name_sei']; ?>　<?php echo $_POST['name_mei']; ?></p>
		</div>

		<div class="element_wrap">
			<label>性別</label>
			<p><?php if( $_POST['gender'] === "1" ){ echo '男性'; }
			else{ echo '女性'; } ?></p>
		</div>
		<div class="element_wrap">
			<label>住所</label>
			<p>
			<?php 
			if( $_POST['pref_name'] === "01" ){ echo '北海道'; }
			elseif( $_POST['pref_name'] === "02" ){ echo '青森県'; }
			elseif( $_POST['pref_name'] === "03" ){ echo '岩手県'; }
			elseif( $_POST['pref_name'] === "04" ){ echo '宮城県'; }
			elseif( $_POST['pref_name'] === "05" ){ echo '秋田県'; }
			elseif( $_POST['pref_name'] === "06" ){ echo '山形県'; }
			elseif( $_POST['pref_name'] === "07" ){ echo '福島県'; }
			elseif( $_POST['pref_name'] === "08" ){ echo '茨城県'; }
			elseif( $_POST['pref_name'] === "09" ){ echo '栃木県'; }
			elseif( $_POST['pref_name'] === "10" ){ echo '群馬県'; }
			elseif( $_POST['pref_name'] === "11" ){ echo '埼玉県'; }
			elseif( $_POST['pref_name'] === "12" ){ echo '千葉県'; }
			elseif( $_POST['pref_name'] === "13" ){ echo '東京都'; }
			elseif( $_POST['pref_name'] === "14" ){ echo '神奈川県'; }
			elseif( $_POST['pref_name'] === "15" ){ echo '新潟県'; }
			elseif( $_POST['pref_name'] === "16" ){ echo '富山県'; }
			elseif( $_POST['pref_name'] === "17" ){ echo '石川県'; }
			elseif( $_POST['pref_name'] === "18" ){ echo '福井県'; }
			elseif( $_POST['pref_name'] === "19" ){ echo '山梨県'; }
			elseif( $_POST['pref_name'] === "20" ){ echo '長野県'; }
			elseif( $_POST['pref_name'] === "21" ){ echo '岐阜県'; }
			elseif( $_POST['pref_name'] === "22" ){ echo '静岡県'; }
			elseif( $_POST['pref_name'] === "23" ){ echo '愛知県'; }
			elseif( $_POST['pref_name'] === "24" ){ echo '三重県'; }
			elseif( $_POST['pref_name'] === "25" ){ echo '滋賀県'; }
			elseif( $_POST['pref_name'] === "26" ){ echo '京都府'; }
			elseif( $_POST['pref_name'] === "27" ){ echo '大阪府'; }
			elseif( $_POST['pref_name'] === "28" ){ echo '兵庫県'; }
			elseif( $_POST['pref_name'] === "29" ){ echo '奈良県'; }
			elseif( $_POST['pref_name'] === "30" ){ echo '和歌山県'; }
			elseif( $_POST['pref_name'] === "31" ){ echo '鳥取県'; }
			elseif( $_POST['pref_name'] === "32" ){ echo '島根県'; }
			elseif( $_POST['pref_name'] === "33" ){ echo '岡山県'; }
			elseif( $_POST['pref_name'] === "34" ){ echo '広島県'; }
			elseif( $_POST['pref_name'] === "35" ){ echo '山口県'; }
			elseif( $_POST['pref_name'] === "36" ){ echo '徳島県'; }
			elseif( $_POST['pref_name'] === "37" ){ echo '香川県'; }
			elseif( $_POST['pref_name'] === "38" ){ echo '愛媛県'; }
			elseif( $_POST['pref_name'] === "39" ){ echo '高知県'; }
			elseif( $_POST['pref_name'] === "40" ){ echo '福岡県'; }
			elseif( $_POST['pref_name'] === "41" ){ echo '佐賀県'; }
			elseif( $_POST['pref_name'] === "42" ){ echo '長崎県'; }
			elseif( $_POST['pref_name'] === "43" ){ echo '熊本県'; }
			elseif( $_POST['pref_name'] === "44" ){ echo '大分県'; }
			elseif( $_POST['pref_name'] === "45" ){ echo '宮崎県'; }
			elseif( $_POST['pref_name'] === "46" ){ echo '鹿児島県'; }
			elseif( $_POST['pref_name'] === "47" ){ echo '沖縄県'; }
			?><?php echo $_POST['address']; ?>
			</p>
		</div>
		<div class="element_wrap">
			<label>パスワード</label>
			<p>セキュリティのため非表示</p>
		</div>

		<div class="element_wrap">
			<label>メールアドレス</label>
			<p><?php echo $_POST['email']; ?></p>
		</div>
		
		<button type="button" name="btn_back" onclick=history.back()>戻る</button>
		<input type="submit" name="btn_submit" value="登録完了">
	</form>
</div>
<?php elseif( $page_flag === 2 ): ?>

<div class="back_color">
	<p>会員登録完了</p>
	<p>会員登録が完了しました</p>
	<input type="button" name="btn_top" onclick="location.href='./top.php'" value="トップに戻る">
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
	<h1>会員情報登録フォーム</h1>

	<form method="post" action="">
		<div class="element_wrap">
			<label>氏名</label>
			<p>姓</p>
			<input type="text" name="name_sei" value="<?php if( !empty($_POST['name_sei']) ){ echo $_POST['name_sei']; } ?>">
			
			<p>名</p>
			<input type="text" name="name_mei" value="<?php if( !empty($_POST['name_mei']) ){ echo $_POST['name_mei']; } ?>">
			
		</div>

		<div class="element_wrap">
			<label>性別</label>
			<label for="gender_male"><input id="gender_male" type="radio" name="gender" value="1" <?php if( !empty($_POST['gender']) && $_POST['gender'] === "1" ){ echo 'checked'; } ?>>男性</label>
			<label for="gender_female"><input id="gender_female" type="radio" name="gender" value="2" <?php if( !empty($_POST['gender']) && $_POST['gender'] === "2" ){ echo 'checked'; } ?>>女性</label>
			
		</div>

		<div class="element_wrap">
			<label>住所</label>
			<p>都道府県</p>
			<select name="pref_name">
				<option value="">選択してください</option>
				<option value="01">北海道</option>
				<option value="02">青森県</option>
				<option value="03">岩手県</option>
				<option value="04">宮城県</option>
				<option value="05">秋田県</option>
				<option value="06">山形県</option>
				<option value="07">福島県</option>
				<option value="08">茨城県</option>
				<option value="09">栃木県</option>
				<option value="10">群馬県</option>
				<option value="11">埼玉県</option>
				<option value="12">千葉県</option>
				<option value="13">東京都</option>
				<option value="14">神奈川県</option>
				<option value="15">新潟県</option>
				<option value="16">富山県</option>
				<option value="17">石川県</option>
				<option value="18">福井県</option>
				<option value="19">山梨県</option>
				<option value="20">長野県</option>
				<option value="21">岐阜県</option>
				<option value="22">静岡県</option>
				<option value="23">愛知県</option>
				<option value="24">三重県</option>
				<option value="25">滋賀県</option>
				<option value="26">京都府</option>
				<option value="27">大阪府</option>
				<option value="28">兵庫県</option>
				<option value="29">奈良県</option>
				<option value="30">和歌山県</option>
				<option value="31">鳥取県</option>
				<option value="32">島根県</option>
				<option value="33">岡山県</option>
				<option value="34">広島県</option>
				<option value="35">山口県</option>
				<option value="36">徳島県</option>
				<option value="37">香川県</option>
				<option value="38">愛媛県</option>
				<option value="39">高知県</option>
				<option value="40">福岡県</option>
				<option value="41">佐賀県</option>
				<option value="42">長崎県</option>
				<option value="43">熊本県</option>
				<option value="44">大分県</option>
				<option value="45">宮崎県</option>
				<option value="46">鹿児島県</option>
				<option value="47">沖縄県</option>
			</select>
			
		</div>

		<div class="element_wrap">
			<label></label>
			<p>それ以降の住所</p>
			<textarea name="address"><?php if( !empty($_POST['address']) ){ echo $_POST['address']; } ?></textarea>
			
		</div>

		<div class="element_wrap">
			<label>パスワード</label>
			<input type="password" name="password" value="<?php echo str_repeat("*", mb_strlen($password, "UTF8")); ?>">
		</div>

		<div class="element_wrap">
			<label for="password_conf">パスワード確認</label>
			<input type="password" name="password_conf" >
			
		</div>

		<div class="element_wrap">
			<label>メールアドレス</label>
			<input type="text" name="email" value="<?php if( !empty($_POST['email']) ){ echo $_POST['email']; } ?>">
			
		</div>

		<input type="submit" name="btn_confirm" value="確認画面へ">
		<input type="button" name="btn_top" onclick="location.href='./top.php'"value="トップに戻る">
	</form>
</div>

<?php endif; ?>

</body>
</html>