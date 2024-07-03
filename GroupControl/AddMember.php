<?php
session_start();
require '../db-connect.php';

$groupId = $_POST['groupId'] ?? null;
$userId = $_POST['userId'] ?? null;

if ($groupId && $userId) {
    try {
        $pdo = new PDO($connect, user, pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 新しいメンバーをグループに追加
        $stmt = $pdo->prepare('INSERT INTO GroupUser (groupID, userID) VALUES (?, ?)');
        $stmt->execute([$groupId, $userId]);

        $_SESSION['message'] = 'メンバーが追加されました。';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'データベースエラー: ' . $e->getMessage();
    }
}

header('Location: GroupControl.php?groupId=' . htmlspecialchars($groupId));
exit;
