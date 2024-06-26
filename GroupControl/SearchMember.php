<?php
session_start();
require '../db-connect.php';

$groupId = $_GET['groupId'] ?? null;
$search = $_GET['search'] ?? '';

try {
    $pdo = new PDO($connect, user, pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 検索結果を取得
    $stmt = $pdo->prepare('SELECT Users.userID, Users.nickname, Users.profileicon FROM GroupUser JOIN Users ON GroupUser.userID = Users.userID WHERE GroupUser.groupID = ? AND Users.nickname LIKE ?');
    $stmt->execute([$groupId, "%$search%"]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'データベースエラー: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>メンバー検索結果</title>
    <link rel="stylesheet" type="text/css" href="css/GroupCon.css">
</head>
<body>
    <div class="group-container">
        <h1 class="group-title">メンバー検索結果</h1>

        <?php if (!empty($members)): ?>
            <ul>
                <?php foreach ($members as $member): ?>
                    <li>
                        <img src="<?= htmlspecialchars($member['profileicon']) ?>" class="circle-icon member-icon">
                        <span><?= htmlspecialchars($member['nickname']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="error-message">該当するメンバーが見つかりません。</p>
        <?php endif; ?>

        <p><a class="group-button" href="GroupControl.php?groupId=<?= htmlspecialchars($groupId) ?>">戻る</a></p>
    </div>
</body>
</html>
