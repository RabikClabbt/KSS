<?php
session_start();
require '../src/db-connect.php';
$pdo = new PDO($connect, user, pass);
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/unimple.css" />
    <title>トップ画面</title>
</head>
<body>
    <?php require '../Header/Header.php'; ?>
    <h1>未実装</h1>
    <p>この画面は未実装のため使えません。</p>
    <a href="../Top/TopPage.php">トップ画面へ</a>
</body>