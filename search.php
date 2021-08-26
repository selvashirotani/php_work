<?php

//送信されたデータ取得
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = $_SESSION['title'];
}

$data =[];

//検索機能
try{
    $dbh = new PDO('mysql:dbname=User;host=153.127.18.207;charset=utf8', 'root', '5lcmJyu8');
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT id,title,created_at FROM thread WHERE title LIKE :title" ;
    $stmt = $dbh-> prepare($sql);
    $stmt-> bindValue(':title','%'.$_SESSION['title'].'%', PDO::PARAM_STR);
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