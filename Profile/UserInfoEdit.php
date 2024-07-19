<?php
session_start();
require '../src/db-connect.php';
require '../Header/Header.php';

$user = $_SESSION['users'];
$pdo = new PDO($connect, user, pass);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="../image/SiteIcon.svg" type="image/svg">
    <title><?= $user['name'] ?> (#<?= $user['id'] ?>) さんの情報更新 | Yadi-X</title>
    <link rel="stylesheet" href="./css/UserInfoEdit.css">
</head>
<body>
    <header id="header"></header>
    <h1 class="mainwhite">ユーザー情報変更</h1>
    <form action="UserInfoEditOut.php" method="post" class="a">
        <h2 class="white">メールアドレス</h2>
        <div class="center">
            <input class="text-size" type="email" name="mail" required>
        </div>
        <h2 class="white">パスワード</h2>
        <h3 class="minwhite">元のパスワード</h3>
        <div class="center">
            <input class="text-size" type="password" name="pass" required>
        </div>
        <h3 class="minwhite">新しいパスワード</h3>
        <div class="center">
            <input class="text-size" type="password" name="new_pass" required>
        </div>
        <h3 class="minwhite">確認</h3>
        <div class="center">
            <input class="text-size" type="password" name="kaku_pass" required>
        </div>
        <div class="button">
            <input type="submit" value="保存">
        </div>
    </form>
</body>
</html>
