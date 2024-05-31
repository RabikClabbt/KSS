<?php
require '../db-connect.php';
$pdo = new PDO($connect, user, pass);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/rply.css" />
    <title>グローバルチャット詳細</title>
</head>
<body>
    <div class="headerr">
        <?php require '../Header/Header.php'; ?>
    </div>
</body>