<?php
session_start();
require '../src/db-connect.php';

try {
    $pdo = new PDO($connect, user, pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_POST['userID'];
    $mailaddress = $_POST['mailaddress'];
    $password = $_POST['password'];

    try {
        // userIDが既に存在するかチェックする
        $sqlr = "SELECT 1 FROM Users WHERE userID = ?";
        $stmtr = $pdo->prepare($sqlr);
        $stmtr->execute([$userID]);
        $judge = $stmtr->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($judge)) {
            $_SESSION['error_message'] = 'ログイン名がすでに使用されています';
            header("Location: ./Registration.php");
            exit;
        } else {
            // パスワードをハッシュ化する
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Usersテーブルに新しいユーザーを挿入する
            $sql = "INSERT INTO Users (userID, mailaddress, password) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userID, $mailaddress, $hashedPassword]);

            header("Location: ../Login/LoginIn.php");
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'エラーが発生しました: ' . $e->getMessage();
        header("Location: ./Registration.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/SiteIcon.svg" type="image/svg">
    <title>アカウント作成 | Yadi-X</title>
    <link rel="stylesheet" href="css/Registration.css">
</head>
<body>
    <h1>アカウント作成</h1>
    <div class="form-container">
        <form method="post">
            <div class="form-group">
                <h4><label for="signin-id">ユーザーID</label></h4>
                <input id="signin-id" type="text" name="userID" value="" required>
            </div>
            <?php
            if (isset($_SESSION['error_message'])) {
                echo '<div class="error-message">' . htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>
            <div class="form-group">
                <h4><label for="signin-mail">メールアドレス</label></h4>
                <input id="signin-mail" type="email" name="mailaddress" value="" required>
            </div>
            <div class="form-group">
                <h4><label for="signin-pass">パスワード</label></h4>
                <input id="signin-pass" type="password" name="password" required >
                <a href="../Login/LoginIn.php">アカウント作成済み</a>
            </div>
            <div class="form-group">
                <button type="submit">作成</button>
            </div>
        </form>
    </div>
</body>
</html>
