<?php

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
	$sql = 'SELECT * FROM administers WHERE login_id = :login_id AND deleted_at IS NULL LIMIT 1';
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(':login_id', $login_id, PDO::PARAM_STR);
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
?>