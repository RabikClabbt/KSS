<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規会員登録</title>
    <link rel="stylesheet" href="css/Toroku.css">
</head>
<body>
    <h1>アカウント作成</h1>
    <div class="form-container">
        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') . '</div>';
            unset($_SESSION['error_message']);
        }
        if (isset($_SESSION['success_message'])) {
            echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') . '</div>';
            unset($_SESSION['success_message']);
        }
        ?>
        <form action="toroku-output.php" method="post">
            <div class="form-group">
                <h4><label for="signin-id">ユーザーID</label></h4>
                <input id="signin-id" type="text" required name="userID" value="<?php echo isset($_SESSION['users']) ? htmlspecialchars($_SESSION['users']['userID'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>
            <div class="form-group">
                <h4><label for="signin-mail">メールアドレス</label></h4>
                <input id="signin-mail" type="email" required name="mailaddress" value="<?php echo isset($_SESSION['users']) ? htmlspecialchars($_SESSION['users']['mailaddress'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>
            <div class="form-group">
                <h4><label for="signin-pass">パスワード</label></h4>
                <input id="signin-pass" type="password" required name="password">
                <a href="login.php">アカウント作成済み</a>
            </div>
            <div class="form-group">
                <button type="submit">作成</button>
            </div>
        </form>
    </div>
</body>
</html>
