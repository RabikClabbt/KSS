<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="css/Login.css">
</head>
<body>
    <?php require '../Header/Header.html'; ?>

    <div class="in-container">
    <form action="LoginOut.php" method="post">
    <h1 class="in-title">ログイン</h1>

    <p class="in-mailid">メールアドレス、またはユーザーID<br>
        <input class="in-input" type="text"  placeholder="mail or ID" name="mailid">
    </p>

    <p class="in-pass">パスワード<br>
        <input class="in-input" type="password" placeholder="password" name="pass">
    </p>

    <!-- エラーメッセージ -->
    <p class="in-error">メールアドレス・ユーザーIDまたはパスワードが違います</p>
    
    <p><a class="in-link" href="TorokuInput.php">アカウント新規作成</a></p>

    <button class="in-button" type="submit">ログイン</button>
    </form>
    </div>
</body>
</html>