<?php
session_start();
require '../db-connect.php';

// ログインしているユーザーのIDをセッションから取得
$user_id = $_SESSION['user_id'];

// POSTデータを取得
$mail = $_POST['mail'];
$pass = $_POST['pass'];
$new_pass = $_POST['new_pass'];
$kaku_pass = $_POST['kaku_pass'];

// パスワードの確認
if ($new_pass !== $kaku_pass) {
    die('新しいパスワードが一致しません。');
}

// データベース接続
try {
    $pdo = new PDO($connect, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続失敗: " . $e->getMessage());
}

// 現在のパスワードが正しいか確認
$sql = 'SELECT password FROM Users WHERE userID = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!password_verify($pass, $user['password'])) {
    die('元のパスワードが正しくありません。');
}

// 新しいパスワードをハッシュ化
$hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);

// メールアドレスとパスワードを更新
$sql = 'UPDATE Users SET mailaddress = ?, password = ? WHERE userID = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$mail, $hashed_new_pass, $user_id]);

echo 'ユーザー情報が更新されました。';
?>

