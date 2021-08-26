<?php
require_once('db_helper.php');

//詳細情報取得
if(isset($_GET['pageid'])) {
    $id = $_GET['pageid'];
    try{
        $dbh = new PDO('mysql:dbname=User;host=153.127.18.207;charset=utf8', 'root', '5lcmJyu8');
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $dbh-> prepare($sql);
        $stmt-> bindValue(':id',$id, PDO::PARAM_STR);
        $stmt->execute();
    
        $count = $stmt->rowCount();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }
    }catch(PDOException $e){
        echo ($e->getMessage());
        die();
    }
}

if( !empty($_POST['delete_user']) ) {
    if(withdrawal_member($dbh,$data[0]['email'],$id)){
        unset($_SESSION['member']);
        header('Location: https://153.127.18.207/member_list.php');
        exit;
    }
}

//var_dump($data);

?>

<!DOCTYPE>
<html lang="ja">
<head>
<title>管理画面会員詳細</title>
<link rel="stylesheet" href="style_administer.css">

</head>

<body>

<div class="header">
    <div class = "left-column">
		<p>会員詳細</p>
	</div>

    <div class= "right-column">
        <input type="button" class="button" onclick="location.href='./member_list.php'"value="一覧へ戻る">
    </div>
</div>

<div class="back_color">

	<form method="post" action="">
        <div class="element_wrap">
			<label>ID</label>
			<p><?php echo $id; ?></p>
		</div>

		<div class="element_wrap">
			<label>氏名</label>
			<p><?php echo $data[0]['name_sei']; ?>　<?php echo $data[0]['name_mei']; ?></p>
		</div>

		<div class="element_wrap">
			<label>性別</label>
			<p><?php if( $data[0]['gender'] === "1" ){ echo '男性'; }
			else{ echo '女性'; } ?></p>
		</div>
		<div class="element_wrap">
			<label>住所</label>
			<p>
			<?php 
			if( $data[0]['pref_name'] === "01" ){ echo '北海道'; }
			elseif( $data[0]['pref_name'] === "02" ){ echo '青森県'; }
			elseif( $data[0]['pref_name'] === "03" ){ echo '岩手県'; }
			elseif( $data[0]['pref_name'] === "04" ){ echo '宮城県'; }
			elseif( $data[0]['pref_name'] === "05" ){ echo '秋田県'; }
			elseif( $data[0]['pref_name'] === "06" ){ echo '山形県'; }
			elseif( $data[0]['pref_name'] === "07" ){ echo '福島県'; }
			elseif( $data[0]['pref_name'] === "08" ){ echo '茨城県'; }
			elseif( $data[0]['pref_name'] === "09" ){ echo '栃木県'; }
			elseif( $data[0]['pref_name'] === "10" ){ echo '群馬県'; }
			elseif( $data[0]['pref_name'] === "11" ){ echo '埼玉県'; }
			elseif( $data[0]['pref_name'] === "12" ){ echo '千葉県'; }
			elseif( $data[0]['pref_name'] === "13" ){ echo '東京都'; }
			elseif( $data[0]['pref_name'] === "14" ){ echo '神奈川県'; }
			elseif( $data[0]['pref_name'] === "15" ){ echo '新潟県'; }
			elseif( $data[0]['pref_name'] === "16" ){ echo '富山県'; }
			elseif( $data[0]['pref_name'] === "17" ){ echo '石川県'; }
			elseif( $data[0]['pref_name'] === "18" ){ echo '福井県'; }
			elseif( $data[0]['pref_name'] === "19" ){ echo '山梨県'; }
			elseif( $data[0]['pref_name'] === "20" ){ echo '長野県'; }
			elseif( $data[0]['pref_name'] === "21" ){ echo '岐阜県'; }
			elseif( $data[0]['pref_name'] === "22" ){ echo '静岡県'; }
			elseif( $data[0]['pref_name'] === "23" ){ echo '愛知県'; }
			elseif( $data[0]['pref_name'] === "24" ){ echo '三重県'; }
			elseif( $data[0]['pref_name'] === "25" ){ echo '滋賀県'; }
			elseif( $data[0]['pref_name'] === "26" ){ echo '京都府'; }
			elseif( $data[0]['pref_name'] === "27" ){ echo '大阪府'; }
			elseif( $data[0]['pref_name'] === "28" ){ echo '兵庫県'; }
			elseif( $data[0]['pref_name'] === "29" ){ echo '奈良県'; }
			elseif( $data[0]['pref_name'] === "30" ){ echo '和歌山県'; }
			elseif( $data[0]['pref_name'] === "31" ){ echo '鳥取県'; }
			elseif( $data[0]['pref_name'] === "32" ){ echo '島根県'; }
			elseif( $data[0]['pref_name'] === "33" ){ echo '岡山県'; }
			elseif( $data[0]['pref_name'] === "34" ){ echo '広島県'; }
			elseif( $data[0]['pref_name'] === "35" ){ echo '山口県'; }
			elseif( $data[0]['pref_name'] === "36" ){ echo '徳島県'; }
			elseif( $data[0]['pref_name'] === "37" ){ echo '香川県'; }
			elseif( $data[0]['pref_name'] === "38" ){ echo '愛媛県'; }
			elseif( $data[0]['pref_name'] === "39" ){ echo '高知県'; }
			elseif( $data[0]['pref_name'] === "40" ){ echo '福岡県'; }
			elseif( $data[0]['pref_name'] === "41" ){ echo '佐賀県'; }
			elseif( $data[0]['pref_name'] === "42" ){ echo '長崎県'; }
			elseif( $data[0]['pref_name'] === "43" ){ echo '熊本県'; }
			elseif( $data[0]['pref_name'] === "44" ){ echo '大分県'; }
			elseif( $data[0]['pref_name'] === "45" ){ echo '宮崎県'; }
			elseif( $data[0]['pref_name'] === "46" ){ echo '鹿児島県'; }
			elseif( $data[0]['pref_name'] === "47" ){ echo '沖縄県'; }
			?><?php echo $data[0]['address']; ?>
			</p>
		</div>
		<div class="element_wrap">
			<label>パスワード</label>
			<p>セキュリティのため非表示</p>
		</div>

		<div class="element_wrap">
			<label>メールアドレス</label>
			<p><?php echo $data[0]['email']; ?></p>
		</div>
		
		<input type="button" class="button" onclick="location.href='<?php echo "member_regist_administer.php?id=".$id ?>'"value="編集">
		<input type="submit" class="button" name="delete_user" value="削除">
	</form>
</div>

</body>
</html>