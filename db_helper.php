<?php

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

//出力前に特殊文字変換する
function html_escape($word){
	return htmlspecialchars($word, ENT_QUOTES,'UTF-8');
}

//POSTデータを取得する
function get_post($key){
	if(isset($_POST[$key])){
		$var = trim($_POST[$key]);
		return $var;
	}
}

//メールアドレスとパスワードが一致するか確認
function select_member($dbh,$email,$password){
	$sql = 'SELECT * FROM users WHERE email = :email AND deleted_at IS NULL LIMIT 1';
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(':email', $email, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount()>0){
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		if(password_verify($password, $data['password'])){
			return $data;
		}else{
			return FALSE;
		}
		return FALSE;
	}
}

function check_words($word,$length){
	if(mb_strlen($word) === 0){
		return FALSE;
	}elseif(mb_strlen($word) > $length){
		return FALSE;
	}else{
		return TRUE;
	}
}

function redirect_main_unless_parameter($param) {
    if (empty($param)) {
        header('Location: https://153.127.18.207/thread_view.php');
        exit;
    }
}

//スレッド情報取得
function find_post_by_id($id) {
    // PDOのインスタンスを生成
    $dbh = get_db_connect();
    try {
        // SQL文の準備
        $sql = "SELECT * FROM thread WHERE id = :id";
        // プリペアドステートメントの作成
        $stmt = $dbh->prepare($sql);
        // idのバインド
        $stmt->bindParam(':id', $id);
        // 実行
        $stmt->execute();
    } catch (PDOException $e) {
		echo ($e->getMessage());
		die();
    }
    // 結果が1行取得できたら
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row;
    } else {
        redirect_main_unless_parameter($row);
    }
}

//投稿者情報取得
function find_member_id($id) {
    // PDOのインスタンスを生成
    $dbh = get_db_connect();
    try {
        // SQL文の準備
        $sql = "SELECT * FROM users WHERE id = :id";
        // プリペアドステートメントの作成
        $stmt = $dbh->prepare($sql);
        // idのバインド
        $stmt->bindParam(':id', $id);
        // 実行
        $stmt->execute();
    } catch (PDOException $e) {
		echo ($e->getMessage());
		die();
    }
    // 結果が1行取得できたら
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row;
    } else {
        redirect_main_unless_parameter($row);
    }
}

//コメント投稿
function insert_comment($dbh,$member_id,$thread_id,$comment){
	$dbh =get_db_connect();
    $sql = "INSERT INTO comments (member_id,thread_id,comment) VALUES (:member_id,:thread_id,:comment)";
    $stmt = $dbh->prepare($sql);
	$stmt->bindParam( ':member_id', $member_id, PDO::PARAM_STR);
    $stmt->bindParam( ':thread_id', $thread_id, PDO::PARAM_STR);
	$stmt->bindParam( ':comment', $comment, PDO::PARAM_STR);
	if($stmt->execute()){
        return TRUE;
    }else{
        return FALSE;
    }

}

//コメント取り出し
function select_comments($dbh,$thread_id){
	$data = [];
	$dbh =get_db_connect();
	$sql = "SELECT * FROM comments INNER JOIN users ON comments.member_id = users.id WHERE thread_id = :thread_id";
    //LIMITを使ったSELECT文
    //$sql = "limit" . $page_num*5 . ", 5" ;
	$stmt = $dbh-> prepare($sql);
	$stmt->bindParam( ':thread_id', $thread_id, PDO::PARAM_STR);
	$stmt->execute();
	$count = $stmt->rowCount();
	while($row= $stmt->fetch(PDO::FETCH_ASSOC)){
		$data[] = $row;
	}
	return array($data,$count);
}

//投稿者情報取得
function find_member_comment_id($dbh) {
	// PDOのインスタンスを生成
    $dbh = get_db_connect();
    try {
        // SQL文の準備
        $sql = "SELECT * FROM users INNER JOIN comments ON users.id = comments.member_id";
        // プリペアドステートメントの作成
        $stmt = $dbh->prepare($sql);
        // 実行
        $stmt->execute();
    } catch (PDOException $e) {
		echo ($e->getMessage());
		die();
    }
    // 結果が1行取得できたら
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row;
    } 
}

function insert_likes($dbh,$member_id,$comment_id){
    $dbh = get_db_connect();
    $sql = "INSERT INTO likes (member_id,comment_id) VALUES (:member_id,:comment_id)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam( ':member_id', $member_id, PDO::PARAM_INT);
    $stmt->bindParam( ':comment_id', $comment_id, PDO::PARAM_INT);
    if($stmt->execute()){
        return TRUE;
    }else{
        return FALSE;
    }
}

function delete_likes($dbh,$member_id,$comment_id){
    $dbh = get_db_connect();
    $sql = "DELETE FROM likes where member_id=:member_id AND comment_id=:comment_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam( ':member_id', $member_id, PDO::PARAM_INT);
    $stmt->bindParam( ':comment_id', $comment_id, PDO::PARAM_INT);
    if($stmt->execute()){
        return TRUE;
    }else{
        return FALSE;
    }
}

//いいねの数取り出し likes_count
function count_likes($dbh,$comment_id){
	$dbh =get_db_connect();
	$sql = "SELECT * FROM likes WHERE comment_id = :comment_id";
	$stmt = $dbh-> prepare($sql);
	$stmt->bindParam( ':comment_id', $comment_id, PDO::PARAM_INT);
	$stmt->execute();
	$count = $stmt->rowCount();
	return $count;
}

//いいね済か確認 pressed_count
function pressed_likes($dbh,$member_id,$comment_id){
    $dbh = get_db_connect();
    $sql = "SELECT * FROM likes WHERE member_id=:member_id AND comment_id=:comment_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam( ':member_id', $member_id, PDO::PARAM_INT);
    $stmt->bindParam( ':comment_id', $comment_id, PDO::PARAM_INT);
    $stmt->execute();
    $count = $stmt->rowCount();
	return $count;
}

//退会
function withdrawal_member($dbh,$email,$id){
    $dbh = get_db_connect();
    $sql = "UPDATE users SET deleted_at = CURRENT_TIMESTAMP where email=:email AND id=:id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam( ':email', $email, PDO::PARAM_INT);
    $stmt->bindParam( ':id', $id, PDO::PARAM_INT);
    
    if($stmt->execute()){
        return TRUE;
    }else{
        return FALSE;
    }
}


//管理画面

//データベース内のメールアドレスを取得
function login_id_exists($dbh,$login_id){
	$sql = "SELECT COUNT(id) FROM administers WHERE login_id = :login_id";
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(':login_id', $login_id,PDO::PARAM_STR);
	$stmt->execute();
	$count = $stmt-> fetch(PDO::FETCH_ASSOC);
	if($count['COUNT(id)']>0){
		return TRUE;
	}else{
		return FALSE;
	}
}

//メールアドレスとパスワードが一致するか確認
function select_administer($dbh,$login_id,$password){
	$sql = "SELECT * FROM administers WHERE login_id = :login_id ";
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(':login_id', $login_id,PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount()>0){
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		if(password_verify($password, $data['password'])){
			return $data;
		}else{
			return FALSE;
		}
		return FALSE;
	}
		
}

function check_length($word,$short,$long){
	if(mb_strlen($word) === 0){
		return FALSE;
	}elseif(mb_strlen($word) > $long){
		return FALSE;
	}elseif(mb_strlen($word) < $short){
		return FALSE;
	}else{
		return TRUE;
	}
}

//データ挿入
function insert_administer_data($dbh,$name,$login_id,$password){
	$dbh =get_db_connect();
    $password = password_hash($password,PASSWORD_DEFAULT);
    $sql = "INSERT INTO administers (name,login_id,password) VALUES (:name,:login_id,:password)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue( ':name',$name,PDO::PARAM_STR);
	$stmt->bindParam( ':password', $password, PDO::PARAM_STR);
	$stmt->bindParam( ':login_id', $login_id, PDO::PARAM_STR);
	if($stmt->execute()){
        return TRUE;
    }else{
        return FALSE;
    }

}

//メンバー取り出し
function select_users($dbh){
	$data = [];
	$dbh =get_db_connect();
	$sql = "SELECT * FROM users ";
    //LIMITを使ったSELECT文
    //$sql = "limit" . $page_num*5 . ", 5" ;
	$stmt = $dbh-> prepare($sql);
	//$stmt->bindParam( ':thread_id', $thread_id, PDO::PARAM_STR);
	$stmt->execute();
	$count = $stmt->rowCount();
	while($row= $stmt->fetch(PDO::FETCH_ASSOC)){
		$data[] = $row;
	}
	return array($data,$count);
}

?>