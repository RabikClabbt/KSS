<?php
session_start();
require '../db-connect.php';

if (!isset($_SESSION['users'])) {
    header('Location: Login.php');
    exit;
}

$userID = $_SESSION['users']['id'];
$partnerID = isset($_POST['partnerID']) ? $_POST['partnerID'] : null;
$commentText = isset($_POST['commentText']) ? $_POST['commentText'] : null;

if ($partnerID && $commentText) {
    // PDO接続の設定
    try {
        $pdo = new PDO($dsn, $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit;
    }

    // ダイレクトメッセージをデータベースに挿入
    $sql = $pdo->prepare('INSERT INTO DirectMessage (userID, partnerID, commentText) VALUES (?, ?, ?)');
    $sql->execute([$userID, $partnerID, $commentText]);

    echo 'success';
} else {
    echo 'Invalid request';
}
?>
