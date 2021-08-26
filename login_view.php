
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログインフォーム</title>
    <link rel="stylesheet" href="style.css">
 
</head>

<body>
<div class="back_color">
    <h1>ログインページ</h1>
    <form action="login.php" method="POST">

    <div class="element_wrap">
        <label>メールアドレス</label>
        <input type="text" name="email">
            
    </div>

    <div class="element_wrap">
        <label>パスワード</label>
        <input type="password" name="password" >
        <p class="error_sentence"><?php echo html_escape($errs['password']); ?></p>
    </div>


    <input name="btn_submit" type="submit" value="ログイン">
    <input type="button" name="btn_top" onclick="location.href='./top.php'" value="トップに戻る">
    </form>
</div>
</body>

</html>