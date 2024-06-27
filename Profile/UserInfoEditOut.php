<?php
session_start();
require '../db-connect.php';
require '../Header/Header.php';

$user = $_SESSION['users'];
$pdo = new PDO($connect, user, pass);

$update_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = htmlspecialchars($_POST['mail']);
    $pass = htmlspecialchars($_POST['pass']);
    $new_pass = htmlspecialchars($_POST['new_pass']);
    $kaku_pass = htmlspecialchars($_POST['kaku_pass']);

    // 入力されたメールアドレスとパスワードがデータベースのものと一致するか確認
    $sql = $pdo->prepare('SELECT * FROM Users WHERE userID = ?');
    $sql->execute([$user['id']]);
    $stored_user = $sql->fetch(PDO::FETCH_ASSOC);

    if ($stored_user && password_verify($pass, $stored_user['password']) && $mail === $stored_user['mailaddress']) {
        // 新しいパスワードの一致を確認
        if ($new_pass === $kaku_pass) {
            // パスワードを更新する
            $update_sql = $pdo->prepare('UPDATE Users SET password = ? WHERE userID = ?');
            $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $update_sql->execute([$hashed_new_pass, $user['id']]);

            // 更新成功のメッセージなどを表示
            $update_success = true;

            echo '<div class="kuro">';
            echo "パスワードを更新しました。";
            echo '</div>';
        } else {
            echo "新しいパスワードが一致しません。";
        }
    } else {
        echo "メールアドレスまたは元のパスワードが一致しません。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー情報変更結果</title>
    <link rel="stylesheet" href="../css/UserInfoEdit.css">
    <script>
        window.onload = function() {
            var updateSuccess = <?php echo json_encode($update_success); ?>;
            if (updateSuccess) {
                window.location.href = '../Top/TopPage.php';
            }
        }
    </script>
</head>
<body>
    <?php if (!$update_success): ?>
        <p class = "kuro">パスワードの更新に失敗しました。再度お試しください。</p>
        <a href="UserInfoEdit.php">戻る</a>
    <?php endif; ?>
</body>
</html>
