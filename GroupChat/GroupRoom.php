<?php
session_start();
require '../src/db-connect.php';
$pdo = new PDO($connect, user, pass);

if (!isset($_SESSION['users'])) {
    header('Location: ../Login/LoginIn.php');
    exit();
}

$userID = $_SESSION['users']['id'];
$groupID = $_GET['groupID'] ?? null;

if (!$groupID) {
    header('Location: ./GroupList.php');
    exit();
}

$groupStmt = $pdo->prepare('SELECT g.groupID, g.groupName, g.description, g.ownerID, u.nickname AS ownerName FROM GroupRooms g LEFT JOIN Users u ON g.ownerID = u.userID WHERE g.groupID = ?');
$groupStmt->execute([$groupID]);
$group = $groupStmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    header('Location: ./GroupList.php');
    exit();
}

$membershipStmt = $pdo->prepare('SELECT role FROM GroupMembers WHERE groupID = ? AND userID = ?');
$membershipStmt->execute([$groupID, $userID]);
$membership = $membershipStmt->fetch(PDO::FETCH_ASSOC);

if (!$membership) {
    header('Location: ./GroupList.php');
    exit();
}

$membersStmt = $pdo->prepare('SELECT gm.userID, gm.role, u.nickname, u.profileIcon FROM GroupMembers gm JOIN Users u ON gm.userID = u.userID WHERE gm.groupID = ? ORDER BY gm.role DESC, u.nickname');
$membersStmt->execute([$groupID]);
$members = $membersStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chat = trim($_POST['chat'] ?? '');
    $uploadedFile = $_FILES['file'] ?? null;
    $filePath = null;

    if ($uploadedFile && $uploadedFile['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileType = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
        $fileName = substr(sha1(basename($uploadedFile['tmp_name']) . rand(0, 9)), 0, 15) . '.' . $fileType;
        $uploadFilePath = $uploadDir . $fileName;
        if (move_uploaded_file($uploadedFile['tmp_name'], $uploadFilePath)) {
            $filePath = './uploads/' . $fileName;
        }
    }

    if ($chat !== '' || $filePath !== null) {
        $insert = $pdo->prepare('INSERT INTO GroupMessages (groupID, userID, commentText, appendFile) VALUES (?, ?, ?, ?)');
        $insert->execute([$groupID, $userID, $chat, $filePath]);
        header('Location: ./GroupRoom.php?groupID=' . urlencode($groupID));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/group-room.css">
    <link rel="icon" href="../image/SiteIcon.svg" type="image/svg">
    <title><?= htmlspecialchars($group['groupName']) ?> | グループチャット</title>
    <script>
        const groupID = <?= json_encode($groupID) ?>;
        const currentUserId = <?= json_encode($userID) ?>;
    </script>
</head>
<body>
    <?php require '../Header/Header.php'; ?>
    <div class="room">
        <aside class="members">
            <div class="members-header">
                <div>
                    <p class="eyebrow">Members</p>
                    <h3>参加メンバー</h3>
                </div>
                <span class="count-chip"><?= count($members) ?>人</span>
            </div>
            <div class="member-list">
                <?php foreach ($members as $member) { ?>
                    <div class="member">
                        <div class="avatar">
                            <?php if (!empty($member['profileIcon'])) { ?>
                                <img src="<?= htmlspecialchars($member['profileIcon']) ?>" alt="icon">
                            <?php } else { ?>
                                <img src="../image/DefaultIcon.svg" alt="icon">
                            <?php } ?>
                        </div>
                        <div class="info">
                            <p class="name"><?= htmlspecialchars($member['nickname']) ?></p>
                            <span class="role <?= $member['role'] === 'owner' ? 'owner' : '' ?>"><?= htmlspecialchars($member['role']) ?></span>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <a class="secondary-btn" href="./GroupList.php">グループ一覧へ戻る</a>
        </aside>

        <main class="chat-area">
            <div class="chat-header">
                <div>
                    <p class="eyebrow">Group Chat</p>
                    <h2><?= htmlspecialchars($group['groupName']) ?></h2>
                    <p class="sub">作成者: <?= htmlspecialchars($group['ownerName'] ?? '不明') ?></p>
                </div>
                <div class="pill">メッセージは自動更新されます</div>
            </div>
            <div class="chat-history" id="groupHistory"></div>

            <form class="chat-form" method="post" enctype="multipart/form-data">
                <div id="file-preview-container">
                    <img id="file-preview" />
                    <span id="file-name"></span>
                    <img src="../image/Dustbin.svg" id="delete-button" onclick="removeFile()" alt="削除">
                </div>
                <div class="input-row">
                    <textarea name="chat" class="text" placeholder="メッセージを入力" rows="2" spellcheck="false"></textarea>
                    <div class="form-actions">
                        <button type="button" class="icon-btn" onclick="triggerFileInput(event)">
                            <img src="../image/FileIcon.svg" alt="ファイルを添付">
                        </button>
                        <input type="file" id="file-input" name="file" style="display: none;" onchange="displayFileName(this)">
                        <button type="submit" class="icon-btn">
                            <img src="../image/SendIcon.svg" alt="送信">
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>
    <script src="./js/group-chat.js"></script>
</body>
</html>
