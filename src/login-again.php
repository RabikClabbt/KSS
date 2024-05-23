<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <?php require 'Header/Header.html'; ?>

    <form action="login-out.php" method="post">
    <h1>ログイン</h1>

    <p>メールアドレス、またはユーザーID<br>
        <input type="text"  placeholder="mail or ID" name="mailid">
    </p>

    <p>パスワード<br>
        <input type="password" placeholder="password" name="pass">
    </p>

    <!-- エラーメッセージ -->
    <p>メールアドレス・ユーザーIDまたはパスワードが違います</p>
    
    <p><a href="toroku-input.php">アカウント新規作成</a></p>

    <button type="submit">ログイン</button>
    </form>
</body>
</html>