<?php
//var_dump($row);
//ini_set("display_errors", 1);
//error_reporting(E_ALL);
//var_dump($_SESSION);

//require_once('db_helper.php');

// 変数の初期化

$data = null;
$_SESSION = array();
$errs = array();
$dbh = null;
$stmt = null;
session_start();

// サニタイズ
//if( !empty($_POST) ) {
//	foreach( $_POST as $key => $value ) {
//		$_SESSION[$key] = htmlspecialchars( $value, ENT_QUOTES);
//	}
//}

//idの昇順降順


function generate_sort_links($sort) {
    $sort_links = '';
    
    switch ($sort) {
    case 1:
        $sort_links .= '<td>ID<input type="submit" name="sort" value="2" class="sankaku"　/></td>';
        $sort_links .= '<td>氏名</td>';
        $sort_links .= '<td>性別</td>';
        $sort_links .= '<td>住所</td>';
        $sort_links .= '<td>登録日時<input type="submit" name="sort" value="3" class="sankaku"/></td>';
        $sort_links .= '<td>編集</td>';
        $sort_links .= '<td>詳細</td>';
        break;
        
    case 3:
        $sort_links .= '<td>ID<input type="submit" name="sort" value="1" class="sankaku"/></td>';
        $sort_links .= '<td>氏名</td>';
        $sort_links .= '<td>性別</td>';
        $sort_links .= '<td>住所</td>';
        $sort_links .= '<td>登録日時<input type="submit" name="sort" value="4" class="sankaku"/></td>';
        $sort_links .= '<td>編集</td>';
        $sort_links .= '<td>詳細</td>';
        break;
    default:
        $sort_links .= '<td>ID<input type="submit" name="sort" value="1" class="sankaku"/></td>';
        $sort_links .= '<td>氏名</td>';
        $sort_links .= '<td>性別</td>';
        $sort_links .= '<td>住所</td>';
        $sort_links .= '<td>登録日時<input type="submit" name="sort" value="3" class="sankaku"/></td>';
        $sort_links .= '<td>編集</td>';
        $sort_links .= '<td>詳細</td>';
        break;
    }
    
    return $sort_links;
    }

//昇順降順切り替え
if(empty($_REQUEST['sort'])){
    $sort="";
}else{
    $sort=$_REQUEST['sort'];
}

//var_dump($sort);

switch ($sort) {
    case 1:
    $sort_query = " ORDER BY id ASC";
    break;
    case 2:
    $sort_query = " ORDER BY id DESC";
    break;
    case 3:
    $sort_query = " ORDER BY created_at ASC";
    break;
    case 4:
    $sort_query = " ORDER BY created_at DESC";
    break;
    default:
    $sort_query = " ORDER BY id DESC";
    break;
}

$gender=implode(",",$_POST['gender']);
if($gender==="1,2"){
    $gender=NULL;
}

//フリー ワード検索機能
try{
    $dbh = new PDO('mysql:dbname=User;host=153.127.18.207;charset=utf8', 'root', '5lcmJyu8');
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM users WHERE (CONCAT(name_sei,name_mei,email) LIKE :search) AND (id LIKE :id) AND (gender LIKE :gender) AND (pref_name LIKE :pref_name)";
    $sql.="$sort_query";
    $stmt = $dbh-> prepare($sql);
    $stmt-> bindValue(':search','%'.$_POST['search'].'%', PDO::PARAM_STR);
    $stmt-> bindValue(':id','%'.$_POST['id'].'%', PDO::PARAM_STR);
    $stmt-> bindValue(':gender','%'.$gender.'%', PDO::PARAM_STR);
    $stmt-> bindValue(':pref_name','%'.$_POST['pref_name'].'%', PDO::PARAM_STR);

    $stmt->execute();

    $count = $stmt->rowCount();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $data[] = $row;
    }
   
}catch(PDOException $e){
    echo ($e->getMessage());
    die();
}
    

//ページ切り替え
$max_page = (int)ceil($count / 10);

if(!isset($_GET['page_id'])){ // $_GET['page_id'] はURLに渡された現在のページ数
    $now = 1; // 設定されてない場合は1ページ目にする
}else{
    $now = (int)$_GET['page_id'];
}

$start_no = ($now - 1) * 10; // 配列の何番目から取得すればよいか

// array_sliceは、配列の何番目($start_no)から何番目(MAX)まで切り取る関数
$disp_data = array_slice($data, $start_no, 10, true);

if($now === 1){
    $pageRange = 2;
}elseif($now === $max_page){
    $pageRange = 2;
}else{
    $pageRange = 1;
}
$start = max($now - $pageRange,1);
$end = min($now + $pageRange,$max_page);
$nums =[];


//var_dump($_POST);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<title>会員一覧ページ</title>
<link rel="stylesheet" href="style_administer.css">

</head>

<body>

<div class="header">
    <div class= "right-column">
        <input type="button" class="button" onclick="location.href='./top_administer.php'"value="トップへ戻る">
    </div>
</div>

<div class="back_color">
    <input type="button" class="button" onclick="location.href='./member_regist_administer.php'"value="会員登録">
    <form action="" method="POST">

        <div class="element_wrap">
			<label>ID</label>
            <input type="text" name="id" value="<?php if( !empty($_POST['id']) ){ echo $_POST['id']; } ?>">
		</div>

		<div class="element_wrap">
			<label>性別</label>
			<label for="gender_male"><input id="gender_male" type="checkbox" name="gender[]" value="1" <?php if($gender === "1"){echo 'checked';} ?>>男性</label>
			<label for="gender_female"><input id="gender_female" type="checkbox" name="gender[]" value="2" <?php if($gender === "2"){echo 'checked';} ?>>女性</label>
			
		</div>

        <div class="element_wrap">
			<label>都道府県</label>
			<select name="pref_name">
				<option value=""  <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "" ? 'selected' : ''; ?>>選択してください</option>
				<option value="01" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "01" ? 'selected' : ''; ?>>北海道</option>
				<option value="02" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "02" ? 'selected' : ''; ?>>青森県</option>
				<option value="03" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "03" ? 'selected' : ''; ?>>岩手県</option>
				<option value="04" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "04" ? 'selected' : ''; ?>>宮城県</option>
				<option value="05" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "05" ? 'selected' : ''; ?>>秋田県</option>
				<option value="06" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "06" ? 'selected' : ''; ?>>山形県</option>
				<option value="07" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "07" ? 'selected' : ''; ?>>福島県</option>
				<option value="08" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "08" ? 'selected' : ''; ?>>茨城県</option>
				<option value="09" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "09" ? 'selected' : ''; ?>>栃木県</option>
				<option value="10" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "10" ? 'selected' : ''; ?>>群馬県</option>
				<option value="11" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "11" ? 'selected' : ''; ?>>埼玉県</option>
				<option value="12" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "12" ? 'selected' : ''; ?>>千葉県</option>
				<option value="13" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "13" ? 'selected' : ''; ?>>東京都</option>
				<option value="14" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "14" ? 'selected' : ''; ?>>神奈川県</option>
				<option value="15" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "15" ? 'selected' : ''; ?>>新潟県</option>
				<option value="16" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "16" ? 'selected' : ''; ?>>富山県</option>
				<option value="17" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "17" ? 'selected' : ''; ?>>石川県</option>
				<option value="18" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "18" ? 'selected' : ''; ?>>福井県</option>
				<option value="19" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "19" ? 'selected' : ''; ?>>山梨県</option>
				<option value="20" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "20" ? 'selected' : ''; ?>>長野県</option>
				<option value="21" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "21" ? 'selected' : ''; ?>>岐阜県</option>
				<option value="22" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "22" ? 'selected' : ''; ?>>静岡県</option>
				<option value="23" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "23" ? 'selected' : ''; ?>>愛知県</option>
				<option value="24" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "24" ? 'selected' : ''; ?>>三重県</option>
				<option value="25" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "25" ? 'selected' : ''; ?>>滋賀県</option>
				<option value="26" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "26" ? 'selected' : ''; ?>>京都府</option>
				<option value="27" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "27" ? 'selected' : ''; ?>>大阪府</option>
				<option value="28" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "28" ? 'selected' : ''; ?>>兵庫県</option>
				<option value="29" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "29" ? 'selected' : ''; ?>>奈良県</option>
				<option value="30" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "30" ? 'selected' : ''; ?>>和歌山県</option>
				<option value="31" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "31" ? 'selected' : ''; ?>>鳥取県</option>
				<option value="32" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "32" ? 'selected' : ''; ?>>島根県</option>
				<option value="33" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "33" ? 'selected' : ''; ?>>岡山県</option>
				<option value="34" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "34" ? 'selected' : ''; ?>>広島県</option>
				<option value="35" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "35" ? 'selected' : ''; ?>>山口県</option>
				<option value="36" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "36" ? 'selected' : ''; ?>>徳島県</option>
				<option value="37" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "37" ? 'selected' : ''; ?>>香川県</option>
				<option value="38" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "38" ? 'selected' : ''; ?>>愛媛県</option>
				<option value="39" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "39" ? 'selected' : ''; ?>>高知県</option>
				<option value="40" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "40" ? 'selected' : ''; ?>>福岡県</option>
				<option value="41" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "41" ? 'selected' : ''; ?>>佐賀県</option>
				<option value="42" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "42" ? 'selected' : ''; ?>>長崎県</option>
				<option value="43" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "43" ? 'selected' : ''; ?>>熊本県</option>
				<option value="44" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "44" ? 'selected' : ''; ?>>大分県</option>
				<option value="45" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "45" ? 'selected' : ''; ?>>宮崎県</option>
				<option value="46" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "46" ? 'selected' : ''; ?>>鹿児島県</option>
				<option value="47" <?php echo array_key_exists('pref_name', $_POST) && $_POST['pref_name'] == "47" ? 'selected' : ''; ?>>沖縄県</option>
			</select>
			
		</div>

        <div class="element_wrap">
            <label>フリー ワード</label>
            <input type="text" name="search" value="<?php if( !empty($_POST['search']) ){ echo $_POST['search']; } ?>">
        </div>

        <input type="submit" name="btn_search" value="検索する">    


    <table>
        <tr>
            <input type="hidden" name="sort" value="<?php if(!empty($_REQUEST['sort'])){echo $sort;} ?>"/>
        <?php echo generate_sort_links($sort) ?>
        </tr>
        <?php foreach($disp_data as $row): ?>
        <tr>
            <td><?php echo $row['id'] ?></td>
            <td><a name="detail_d" href="<?php echo "detail_administer.php?pageid=".$row['id'] ?>" ><?php echo $row['name_sei'] ?>　<?php echo $row['name_mei'] ?></a></td>
            <td><?php if( $row['gender'] === "1" ){ echo '男性'; }
            else{ echo '女性'; } ?></td>
            <td>
            <?php 
            if( $row['pref_name'] === "01" ){ echo '北海道'; }
            elseif( $row['pref_name'] === "02" ){ echo '青森県'; }
            elseif( $row['pref_name'] === "03" ){ echo '岩手県'; }
            elseif( $row['pref_name'] === "04" ){ echo '宮城県'; }
            elseif( $row['pref_name'] === "05" ){ echo '秋田県'; }
            elseif( $row['pref_name'] === "06" ){ echo '山形県'; }
            elseif( $row['pref_name'] === "07" ){ echo '福島県'; }
            elseif( $row['pref_name'] === "08" ){ echo '茨城県'; }
            elseif( $row['pref_name'] === "09" ){ echo '栃木県'; }
            elseif( $row['pref_name'] === "10" ){ echo '群馬県'; }
            elseif( $row['pref_name'] === "11" ){ echo '埼玉県'; }
            elseif( $row['pref_name'] === "12" ){ echo '千葉県'; }
            elseif( $row['pref_name'] === "13" ){ echo '東京都'; }
            elseif( $row['pref_name'] === "14" ){ echo '神奈川県'; }
            elseif( $row['pref_name'] === "15" ){ echo '新潟県'; }
            elseif( $row['pref_name'] === "16" ){ echo '富山県'; }
            elseif( $row['pref_name'] === "17" ){ echo '石川県'; }
            elseif( $row['pref_name'] === "18" ){ echo '福井県'; }
            elseif( $row['pref_name'] === "19" ){ echo '山梨県'; }
            elseif( $row['pref_name'] === "20" ){ echo '長野県'; }
            elseif( $row['pref_name'] === "21" ){ echo '岐阜県'; }
            elseif( $row['pref_name'] === "22" ){ echo '静岡県'; }
            elseif( $row['pref_name'] === "23" ){ echo '愛知県'; }
            elseif( $row['pref_name'] === "24" ){ echo '三重県'; }
            elseif( $row['pref_name'] === "25" ){ echo '滋賀県'; }
            elseif( $row['pref_name'] === "26" ){ echo '京都府'; }
            elseif( $row['pref_name'] === "27" ){ echo '大阪府'; }
            elseif( $row['pref_name'] === "28" ){ echo '兵庫県'; }
            elseif( $row['pref_name'] === "29" ){ echo '奈良県'; }
            elseif( $row['pref_name'] === "30" ){ echo '和歌山県'; }
            elseif( $row['pref_name'] === "31" ){ echo '鳥取県'; }
            elseif( $row['pref_name'] === "32" ){ echo '島根県'; }
            elseif( $row['pref_name'] === "33" ){ echo '岡山県'; }
            elseif( $row['pref_name'] === "34" ){ echo '広島県'; }
            elseif( $row['pref_name'] === "35" ){ echo '山口県'; }
            elseif( $row['pref_name'] === "36" ){ echo '徳島県'; }
            elseif( $row['pref_name'] === "37" ){ echo '香川県'; }
            elseif( $row['pref_name'] === "38" ){ echo '愛媛県'; }
            elseif( $row['pref_name'] === "39" ){ echo '高知県'; }
            elseif( $row['pref_name'] === "40" ){ echo '福岡県'; }
            elseif( $row['pref_name'] === "41" ){ echo '佐賀県'; }
            elseif( $row['pref_name'] === "42" ){ echo '長崎県'; }
            elseif( $row['pref_name'] === "43" ){ echo '熊本県'; }
            elseif( $row['pref_name'] === "44" ){ echo '大分県'; }
            elseif( $row['pref_name'] === "45" ){ echo '宮崎県'; }
            elseif( $row['pref_name'] === "46" ){ echo '鹿児島県'; }
            elseif( $row['pref_name'] === "47" ){ echo '沖縄県'; }
            ?><?php echo $row['address']; ?></td>
            <td><?php echo date('Y/m/d',strtotime($row["created_at"])); ?></td>
            <td><a name="detail" href="<?php echo "member_regist_administer.php?id=".$row['id'] ?>" >編集</a></td>
            <td><a name="detail_d" href="<?php echo "detail_administer.php?pageid=".$row['id'] ?>" >詳細</a></td>
        </tr>
        <?php endforeach; ?>

    </table>
    </form>
    
    <ul class="prev_next_ul">
        <?php if($now > 1){ ?>
            <li><a href="<?php echo "member_list.php?page_id=".($now - 1) ?>"><前へ</a></li>
        
        <?php } ?>

        <?php for($i = $start; $i <= $end; $i++){ ?>
            <?php if ($i == $now) { ?>
                <li class="now"><?php echo $now ?></li>
            <?php }else{ ?>
                <li><a href="<?php echo "member_list.php?page_id=".($i) ?>"><?php echo $i ?></a></li>
            <?php } ?>
        <?php } ?>
        

        <?php if($now < $max_page){ ?>
            <li><a href="<?php echo "member_list.php?page_id=".($now + 1) ?>">次へ></a></li>
        
        <?php } ?>

    </ul>
    

</div>


</body>
</html>