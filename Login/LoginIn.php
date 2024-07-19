<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../image/SiteIcon.svg" type="image/svg">
    <title>ログイン | Yadi-X</title>
    <link rel="stylesheet" type="text/css" href="css/Login.css">
</head>

<body>
    <header>
        <!-- Header.phpを読み込む -->
        <include src="../Header/Header.php"></include>
    </header>
    <main>
        <div class="in-container">
        <form action="LoginOut.php" method="post">
        <h1 class="in-title">ログイン</h1>

        <p class="in-mailid">メールアドレス、またはユーザーID<br>
            <input class="in-input" type="text"  placeholder="mail or ID" name="mailid">
        </p>

        <p class="in-pass">パスワード<br>
            <input class="in-input" type="password" placeholder="password" name="pass">
        </p>
    
        <p><a class="in-link" href="../Registration/Registration.php">アカウント新規作成</a></p>

        <p><button class="in-button" type="submit">ログイン</button></p>
        </form>
        </div>
        </main>
    <footer>
          
    </footer>
</body>
</html>