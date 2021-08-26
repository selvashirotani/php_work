

<?php
//var_dump($_POST);
//ini_set("display_errors", 1);
//error_reporting(E_ALL);
//var_dump($_SESSION);

require_once('db_helper.php');

// 変数の初期化
$page_flag = 0;
$data = null;
$_SESSION = array();
$errs = array();
$dbh = null;
$stmt = null;
session_start();


// サニタイズ
if( !empty($_POST) ) {
	foreach( $_POST as $key => $value ) {
		$_SESSION[$key] = htmlspecialchars( $value, ENT_QUOTES);
	}
}

//ログイン済みの場合

if( !empty($_SESSION['member']) ) {
	$page_flag = 1;
	$_SESSION['page'] = true;	
}


$data =[];

//検索機能
try{
    $dbh = new PDO('mysql:dbname=User;host=153.127.18.207;charset=utf8', 'root', '5lcmJyu8');
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM thread WHERE CONCAT(title, content) LIKE :search ORDER BY created_at DESC";
    $stmt = $dbh-> prepare($sql);
    $stmt-> bindValue(':search','%'.$_POST['search'].'%', PDO::PARAM_STR);

    $stmt->execute();

    $count = $stmt->rowCount();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $data[] = $row;
    }
   
}catch(PDOException $e){
    echo ($e->getMessage());
    die();
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
<title>スレッド</title>
<link rel="stylesheet" href="style.css">

</head>

<body>


<?php if( $page_flag === 1 ): ?>

<div class="header">
    <div class= "right-column">
        
        <input class="button" type="button" onclick="location.href='./thread_regist.php'"value="新規スレッド作成">
        
    </div>
</div>
<div class="back_color">
    <form action="" method="POST">

        <input type="text" name="search" value="<?php if( !empty($_POST['search']) ){ echo $_POST['search']; } ?>">
        <input type="submit" name="btn_search" value="スレッド検索">

    </form>

    <table>
        <?php foreach($data as $row): ?>
        <tr>
            <td>ID:<?php echo $row['id'] ?></td>
            <td><a name="detail" href="<?php echo "thread_detail.php?id=".$row['id'] ?>" ><?php echo $row['title'] ?></a></td>
            <td><?php echo $row['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>

    </table>

    <input type="button" name="btn_top" onclick="location.href='./top.php'"value="トップに戻る">
</div>

<?php else: ?>

<div class="header">

</div>

<div class="back_color">
    <form action="" method="POST">

        <input type="text" name="search" value="<?php if( !empty($_POST['search']) ){ echo $_POST['search']; } ?>">
        <input type="submit" name="btn_search" value="スレッド検索">    

    </form>


    <table>
        <?php foreach($data as $row): ?>
        <tr>
            <td>ID:<?php echo $row['id'] ?></td>
            <td><a name="detail" href="<?php echo "thread_detail.php?id=".$row['id'] ?>" ><?php echo $row['title'] ?></a></td>
            <td><?php echo $row['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>

    </table>



    <input type="button" name="btn_top" onclick="location.href='./top.php'"value="トップに戻る">
</div>
<?php endif; ?>

</body>
</html>