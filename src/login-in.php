<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <?php require 'Header/Header.html' ?>

    <div class="in-container">
    <form action="login-out.php" method="post">
    <div class="in-title"><h1>ログイン</h1></div>

    <div class="in-mailid"><p>メールアドレス、またはユーザーID<br>
        <input type="text"  placeholder="mail or ID" name="mailid">
    </p></div>

    <div class="in-pass"><p>パスワード<br>
        <input type="password" placeholder="password" name="pass">
    </p></div>
    
    <div class="in-link"><p><a href="toroku-input.php">アカウント新規作成</a></p></div>

    <div class="in-button"><button type="submit">ログイン</button></div>
    </form>
    </div>
</body>
</html>