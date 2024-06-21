<?php
session_start();
require '../db-connect.php';

$groupId = $_GET['groupId'] ?? null;
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

try {
    $pdo = new PDO($connect, user, pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // グループ情報を取得
    $group = null;
    if ($groupId) {
        $stmt = $pdo->prepare('SELECT * FROM `Group` WHERE groupID = ?');
        $stmt->execute([$groupId]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // グループメンバーを取得
    $members = [];
    if ($groupId) {
        $stmt = $pdo->prepare('SELECT Users.userID, Users.nickname, Users.profileicon FROM GroupUser JOIN Users ON GroupUser.userID = Users.userID WHERE GroupUser.groupID = ?');
        $stmt->execute([$groupId]);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo 'データベースエラー: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>グループ管理画面</title>
    <link rel="stylesheet" type="text/css" href="css/GroupCon.css">
</head>
<body>
    <?php if ($message): ?>
        <div id="message-popup" class="message-popup"><?= htmlspecialchars($message) ?></div>
        <script>
            setTimeout(function() {
                document.getElementById('message-popup').style.display = 'none';
            }, 3000);
        </script>
    <?php endif; ?>

    <div class="group-container">
        <h1 class="group-title">グループ管理</h1>

        <?php if ($group): ?>
            <form action="UpdateGroup.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="groupId" value="<?= htmlspecialchars($group['groupID']) ?>">

                <div class="group-details">
                    <img src="<?= htmlspecialchars($group['groupIcon']) ?>" class="current-icon" style="width: 100px; height: 100px;">
                    <p class="group-name">グループ名<br>
                        <input class="group-input" type="text" name="groupName" value="<?= htmlspecialchars($group['groupName']) ?>" required>
                    </p>
                </div>

                <p class="group-icon">
                    <label for="groupIcon" class="file-label">グループのアイコンを設定</label>
                    <input id="groupIcon" type="file" name="groupIcon" accept="image/*" onchange="previewImage(event)">
                </p>

                <img id="preview" class="circle-icon">

                <p><button class="group-button" type="submit">更新する</button></p>
            </form>
        <?php else: ?>
            <p class="error-message">グループが見つかりません。</p>
        <?php endif; ?>

        <form action="SearchMember.php" method="get">
            <input type="hidden" name="groupId" value="<?= htmlspecialchars($groupId) ?>">
            <input class="group-input" type="text" name="search" placeholder="メンバーを検索">
            <button class="group-button" type="submit">検索</button>
        </form>

        <form action="AddMember.php" method="post">
            <input type="hidden" name="groupId" value="<?= htmlspecialchars($groupId) ?>">
            <input class="group-input" type="text" name="userId" placeholder="追加するユーザーID">
            <button class="group-button" type="submit">メンバー追加</button>
        </form>

        <?php if (!empty($members)): ?>
            <h2 class="group-title">メンバー一覧</h2>
            <ul>
                <?php foreach ($members as $member): ?>
                    <li>
                        <img src="<?= htmlspecialchars($member['profileicon']) ?>" class="circle-icon member-icon" style="width: 50px; height: 50px;">
                        <span><?= htmlspecialchars($member['nickname']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('preview');
                output.src = reader.result;
                output.style.display = 'block';
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
