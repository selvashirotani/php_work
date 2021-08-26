<?php
//ini_set("display_errors", 1);
//error_reporting(E_ALL);
//var_dump($_POST);
//var_dump($_SESSION);
//var_dump($row);



require_once('db_helper.php');

// 変数の初期化
$page_flag = 0;
$data =[];
$_SESSION = array();
$thread = null;
$dbh = null;
$stmt = null;
$comment = null;
$error = array();
$pressed_count = null;
$likes_count = null;

session_start();
$member = $_SESSION['member'];

get_db_connect();

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


// URLの?以降で渡されるIDをキャッチ
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    //print("$id<br>\n");
}


redirect_main_unless_parameter($id);

$row = find_post_by_id($id);

//$comment = [];


$member_id = find_member_id($row["member_id"]);

//コメントのバリデーション
function validation_comment($data,$member,$dbh) {

	$error = array();

	// コメントのバリデーション
	if( empty($data) ) {
		$error = "※コメントを入力してください";
	}elseif( 500 < mb_strlen($data) ) {
		$error = "※コメントは500字以内で入力してください";
	}

	return $error;
	return $member;
}




//コメントされたときのif文
if( !empty($_POST['btn_submit']) ) {
	$error = validation_comment($_POST['comment'],$member,$dbh);
	if( empty($error) ) {
        insert_comment($dbh,$member['id'],$row['id'],$_POST['comment']);
	}

}


//コメント出力

//$comment = $_SESSION['comment'];
$comments = array();

list($comments,$count) = select_comments($dbh,$row['id']);

//$comments_member_id = find_member_comment_id($dbh);


//error_reporting(E_ALL);

//ページ切り替え

$max_page = ceil($count / 5);

if(!isset($_GET['page_id'])){ // $_GET['page_id'] はURLに渡された現在のページ数
    $now = 1; // 設定されてない場合は1ページ目にする
}else{
    $now = $_GET['page_id'];
}

$start_no = ($now - 1) * 5; // 配列の何番目から取得すればよいか

// array_sliceは、配列の何番目($start_no)から何番目(MAX)まで切り取る関数
$disp_data = array_slice($comments, $start_no, 5, true);

//var_dump($comments);

//var_dump($_GET['likes'])；

$comment_page_id = $row['id'] ;



foreach($disp_data as $comment){
    //いいねについて
    //コメント別のいいねされた件数をDBから取り出し
    $pressed_count = pressed_likes($dbh,$member['id'],$comment['comment_id']);
    //いいねボタン
    //いいねを押された時
    if( isset($_GET['likes']) ) {
    //$_SESSION['likes']に$_GET['likes']をいれる。
    $_SESSION['likes']=$_GET['likes'];
    //押されたボタンとcommentのidが一緒か調べる。これがないと、foreachで全コメント反映された。
    if($_SESSION['likes']===$comment["comment_id"]){
        //過去にいいね済か確認
        pressed_likes($dbh,$member['id'],$comment['comment_id']);
        //いいねのデータ挿入or削除
        if($pressed_count<1){
            if(insert_likes($dbh,$member['id'],$comment['comment_id'])){
                header("Location: https://153.127.18.207/thread_detail.php?id={$comment_page_id}&page_id={$now}");
            }else{
                echo "とうろくできない";
            }              

        }else{
            if(delete_likes($dbh,$member['id'],$comment['comment_id'])){
            header("Location: https://153.127.18.207/thread_detail.php?id={$comment_page_id}&page_id={$now}");
            }else{
                echo "おかしい";
            }
        }
    }   
    }
    //$likes_count = count_likes($dbh,$comment['comment_id']);
}

//var_dump($pressed_count);
//var_dump($likes_count);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<title>スレッド詳細</title>
<link rel="stylesheet" href="style.css">
<link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">

</head>

<body>

<?php if( $page_flag === 1 ): ?>
<div class="header">
    <div class= "right-column">
        
        <input class="button" type="button" onclick="location.href='./thread_view.php'"value="スレッド一覧に戻る">
        
    </div>
</div>

<div class="back_color">
    <div class="thread_detail">
        <h1><?php echo html_escape($row['title']); ?></h1>
        <p><?php echo $count; ?>コメント</p>
        <p class="created_data"><?php echo date('Y/m/d H:i',strtotime($row["created_at"])); ?></p>

        <div class="prev_next">
        <ul class="prev_next_ul">
            <?php if($now > 1){ ?>
                <li><a href="<?php echo "thread_detail.php?id=".$row['id']."&page_id=".($now - 1) ?>">＜前へ</a></li>
            <?php }else{ ?>     
                <li class="gray">＜前へ</li>
            <?php } ?>

            <?php if($now < $max_page){ ?>
                <li><a href="<?php echo "thread_detail.php?id=".$row['id']."&page_id=".($now + 1) ?>">次へ＞</a></li>
            <?php }else{ ?>     
                <li class="gray">次へ＞</li>
            <?php } ?>

        </ul>

        </div>

        <div class="center-column">
        <p>投稿者:<?php echo $member_id["name_sei"].$member_id["name_mei"] ?>    <?php echo date('Y.m.d H:i',strtotime($row["created_at"])); ?></p>
        <p><?php echo nl2br($row["content"]); ?></p>
        </div>

        
        <?php foreach($disp_data as $comment): ?>

            <p><?php echo $comment['comment_id']; ?> 
                <?php echo $comment["name_sei"].$comment["name_mei"] ?> 
            <?php echo date('Y.m.d H:i',strtotime($comment["comments_created_at"])); ?></p>

            <div class="comment_class">
            <p ><?php echo nl2br($comment["comment"]); ?></p>
            </div>

            <div class="likes_class">
            <form method="post" name="btn_likes" action="">
                <input type="hidden" name="likes" value="likes">
                <?php $pressed_count = pressed_likes($dbh,$member['id'],$comment['comment_id']); ?>
                <?php if ($pressed_count < 1) : ?>
                <a class="heart" href="<?php echo "thread_detail.php?id=".$row['id']."&likes=".$comment["comment_id"]."&page_id=".$now  ?>">
                <i class="far fa-heart black_heart"></i>
                </a>
                <?php else : ?>
                <a class="heart " href="<?php echo "thread_detail.php?id=".$row['id']."&likes=".$comment["comment_id"]."&page_id=".$now  ?>">
                <i class="fas fa-heart red_heart"></i>
                </a>
                <?php endif; ?>
                <span>
                    <?php 
                        $likes_count = count_likes($dbh,$comment['comment_id']);
                        echo $likes_count; 
                    ?>
                </span>
                
            </form>
            </div>

            <div class="comments">
            </div>
        <?php endforeach; ?>
        

        <div class="prev_next">
        <ul class="prev_next_ul">
            <?php if($now > 1){ ?>
                <li><a href="<?php echo "thread_detail.php?id=".$row['id']."&page_id=".($now - 1) ?>">＜前へ</a></li>
            <?php }else{ ?>     
                <li class="gray">＜前へ</li>
            <?php } ?>

            <?php if($now < $max_page){ ?>
                <li><a href="<?php echo "thread_detail.php?id=".$row['id']."&page_id=".($now + 1) ?>">次へ＞</a></li>
            <?php }else{ ?>     
                <li class="gray">次へ＞</li>
            <?php } ?>

        </ul>

        </div>

        <form action="" method="post">

            <textarea name="comment"><?php if(!empty($error) && !empty($_POST['comment']) === true ){ echo $_POST['comment']; } ?></textarea>
            <br>
                <?php if( !empty($error) ): ?>
                <p　class="error_sentence"><?php echo $error; ?></p>
                <?php endif; ?>
            
            <input type="submit" name="btn_submit" value="コメントする">

        </form>
    </div>
</div>

<?php else: ?>
<div class="header">
    <div class= "right-column">
        
        <input class="button" type="button" onclick="location.href='./thread_view.php'"value="スレッド一覧に戻る">
        
    </div>
</div>

<div class="back_color">
    <div class="thread_detail">
        <h1><?php echo html_escape($row['title']); ?></h1>
        <p><?php echo $count; ?>コメント</p>
        <p class="created_data"><?php echo date('Y/m/d H:i',strtotime($row["created_at"])); ?></p>

        <div class="prev_next">
        <ul class="prev_next_ul">
            <?php if($now > 1){ ?>
                <li><a href="<?php echo "thread_detail.php?id=".$row['id']."&page_id=".($now - 1) ?>">＜前へ</a></li>
            <?php }else{ ?>     
                <li class="gray">＜前へ</li>
            <?php } ?>

            <?php if($now < $max_page){ ?>
                <li><a href="<?php echo "thread_detail.php?id=".$row['id']."&page_id=".($now + 1) ?>">次へ＞</a></li>
            <?php }else{ ?>     
                <li class="gray">次へ＞</li>
            <?php } ?>

        </ul>

        </div>

        <div class="center-column">
        <p>投稿者:<?php echo $member_id["name_sei"].$member_id["name_mei"] ?>    <?php echo date('Y.m.d H:i',strtotime($row["created_at"])); ?></p>
        <p><?php echo nl2br($row["content"]); ?></p>
        </div>

        
        <?php foreach($disp_data as $comment): ?>

            <p><?php echo $comment['comment_id']; ?> 
                <?php echo $comment["name_sei"].$comment["name_mei"] ?> 
            <?php echo date('Y.m.d H:i',strtotime($comment["comments_created_at"])); ?></p>
            
            <div class="comment_class">
            <p ><?php echo nl2br($comment["comment"]); ?></p>
            </div>

            <div class="likes_class">
            <form method="post" name="btn_likes" action="">
                <input type="hidden" name="likes" value="likes">
                <a class="heart" href="member_regist.php">
                <i class="far fa-heart black_heart"></i>
                </a>
                <span>
                    <?php 
                        $likes_count = count_likes($dbh,$comment['comment_id']);
                        echo $likes_count; 
                    ?>
                </span>
                
            </form>
            </div>

            <div class="comments">
            </div>
        <?php endforeach; ?>
        

        <div class="prev_next">
        <ul class="prev_next_ul">
            <?php if($now > 1){ ?>
                <li><a href="<?php echo "thread_detail.php?id=".$row['id']."&page_id=".($now - 1) ?>">＜前へ</a></li>
            <?php }else{ ?>     
                <li class="gray">＜前へ</li>
            <?php } ?>

            <?php if($now < $max_page){ ?>
                <li><a href="<?php echo "thread_detail.php?id=".$row['id']."&page_id=".($now + 1) ?>">次へ＞</a></li>
            <?php }else{ ?>     
                <li class="gray">次へ＞</li>
            <?php } ?>

        </ul>

        </div>

    </div>
</div>



<?php endif; ?>

</body>

</html>