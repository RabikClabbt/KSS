<?php
session_start();
require '../db-connect.php';

$groupId = $_POST['groupId'] ?? null;
$groupName = $_POST['groupName'] ?? null;
$groupIcon = $_FILES['groupIcon'] ?? null;

if ($groupId && $groupName) {
    try {
        $pdo = new PDO($connect, user, pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // グループ名の更新
        $stmt = $pdo->prepare('UPDATE `Group` SET groupName = ? WHERE groupID = ?');
        $stmt->execute([$groupName, $groupId]);

        // グループアイコンの更新
        if ($groupIcon && $groupIcon['tmp_name']) {
            $targetDir = 'uploads/';
            $targetFile = $targetDir . basename($groupIcon['name']);
            if (move_uploaded_file($groupIcon['tmp_name'], $targetFile)) {
                $stmt = $pdo->prepare('UPDATE `Group` SET groupIcon = ? WHERE groupID = ?');
                $stmt->execute([$targetFile, $groupId]);
            } else {
                $_SESSION['message'] = '申し訳ありませんが、ファイルのアップロード中にエラーが発生しました。';
            }
        }

        $_SESSION['message'] = 'グループ情報が更新されました。';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'データベースエラー: ' . $e->getMessage();
    }
} else {
    $_SESSION['message'] = 'グループ名が必要です。';
}

header('Location: GroupControl.php?groupId=' . urlencode($groupId));
exit;
?>
