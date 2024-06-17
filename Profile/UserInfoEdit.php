<?php
session_start();
require '../db-connect.php';
require '../Header/Header.php';

// ログインしているユーザーのIDをセッションから取得
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/UserInfoEdit.css">
    <title>ユーザー情報変更画面</title>
</head>
<body>
    <header id="header"></header>
    <h1 class="mainwhite">ユーザー情報変更</h1>
    <form action="UserInfoEditOut.php" method="post" class="a">
        <h2 class="white">メールアドレス</h2>
        <div class="center">
            <input class="text-size" type="email" name="mail" id="" required>
        </div>
        <h2 class="white">パスワード</h2>
        <h3 class="minwhite">元のパスワード</h3>
        <div class="center">
            <input class="text-size" type="password" name="pass" id="" required>
        </div>
        <h3 class="minwhite">新しいパスワード</h3>
        <div class="center">
            <input class="text-size" type="password" name="new_pass" id="" required>
        </div>
        <h3 class="minwhite">確認</h3>
        <div class="center">
            <input class="text-size" type="password" name="kaku_pass" id="" required>
        </div>
        <div class="button">
            <input type="submit" value="保存">
        </div>
    </form>
</body>
</html>
