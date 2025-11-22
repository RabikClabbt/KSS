<?php
session_start();
require '../src/db-connect.php';
$pdo = new PDO($connect, user, pass);

$currentUser = $_SESSION['users'] ?? null;
$userID = $currentUser['id'] ?? null;
$statusMessage = '';

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$userID) {
        header('Location: ../Login/LoginIn.php');
        exit();
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $groupName = trim($_POST['groupName'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($groupName === '') {
            $statusMessage = 'グループ名を入力してください。';
        } else {
            $createGroup = $pdo->prepare('INSERT INTO GroupRooms (groupName, description, ownerID) VALUES (?, ?, ?)');
            $createGroup->execute([$groupName, $description, $userID]);
            $groupID = $pdo->lastInsertId();

            $insertOwner = $pdo->prepare('INSERT INTO GroupMembers (groupID, userID, role) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE role = VALUES(role)');
            $insertOwner->execute([$groupID, $userID, 'owner']);
            $statusMessage = 'グループを作成しました。';
        }
    } elseif ($action === 'join') {
        $groupID = $_POST['groupID'] ?? '';
        if ($groupID !== '') {
            $join = $pdo->prepare('INSERT INTO GroupMembers (groupID, userID, role) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE role = VALUES(role)');
            $join->execute([$groupID, $userID, 'member']);
            $statusMessage = 'グループに参加しました。';
        }
    } elseif ($action === 'leave') {
        $groupID = $_POST['groupID'] ?? '';
        if ($groupID !== '') {
            $leave = $pdo->prepare('DELETE FROM GroupMembers WHERE groupID = ? AND userID = ?');
            $leave->execute([$groupID, $userID]);
            $statusMessage = 'グループから退出しました。';
        }
    }
}

$groupSql = 'SELECT g.groupID, g.groupName, g.description, g.ownerID, g.createdAt, u.nickname AS ownerName, COUNT(m.userID) AS memberCount
             FROM GroupRooms g
             LEFT JOIN GroupMembers m ON g.groupID = m.groupID
             LEFT JOIN Users u ON g.ownerID = u.userID
             GROUP BY g.groupID
             ORDER BY g.createdAt DESC';
$groups = $pdo->query($groupSql)->fetchAll(PDO::FETCH_ASSOC);

$memberships = [];
if ($userID) {
    $membershipStmt = $pdo->prepare('SELECT groupID, role FROM GroupMembers WHERE userID = ?');
    $membershipStmt->execute([$userID]);
    foreach ($membershipStmt as $memberRow) {
        $memberships[$memberRow['groupID']] = $memberRow['role'];
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/group-list.css">
    <link rel="icon" href="../image/SiteIcon.svg" type="image/svg">
    <title>グループ一覧 | Yadi-X</title>
</head>
<body>
    <?php require '../Header/Header.php'; ?>
    <div class="group-page">
        <section class="hero">
            <div>
                <p class="eyebrow">Group</p>
                <h1>グループを見つけよう</h1>
                <p class="lead">興味のあるトピックやクラスメイトとつながれるグループを作成・参加できます。</p>
            </div>
            <?php if ($statusMessage) { ?>
                <div class="status"><?= h($statusMessage) ?></div>
            <?php } ?>
        </section>

        <section class="grid">
            <div class="panel">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">Create</p>
                        <h2>新しいグループを作成</h2>
                    </div>
                </div>
                <?php if ($userID) { ?>
                    <form class="create-form" method="post">
                        <input type="hidden" name="action" value="create">
                        <label class="field">
                            <span>グループ名</span>
                            <input type="text" name="groupName" placeholder="例：チーム開発メンバー" required>
                        </label>
                        <label class="field">
                            <span>紹介文</span>
                            <textarea name="description" rows="3" placeholder="活動内容や目的を共有しましょう"></textarea>
                        </label>
                        <button type="submit" class="primary-btn">グループを作成</button>
                    </form>
                <?php } else { ?>
                    <div class="cta">
                        <p>グループ作成にはログインが必要です。</p>
                        <a class="primary-btn" href="../Login/LoginIn.php">ログインする</a>
                    </div>
                <?php } ?>
            </div>

            <div class="panel groups">
                <div class="panel-header">
                    <div>
                        <p class="eyebrow">Join</p>
                        <h2>参加可能なグループ</h2>
                    </div>
                    <span class="count-chip">全<?= count($groups) ?>件</span>
                </div>
                <div class="group-cards">
                    <?php if (empty($groups)) { ?>
                        <p class="empty">まだグループがありません。最初のグループを作成しましょう。</p>
                    <?php }
                    foreach ($groups as $group) {
                        $groupId = $group['groupID'];
                        $isMember = array_key_exists($groupId, $memberships);
                        $ownerName = $group['ownerName'] ?? '不明';
                        $createdAt = $group['createdAt'] ?? null;
                        $createdText = $createdAt ? date('Y/m/d', strtotime($createdAt)) : '日付未設定';
                        ?>
                        <div class="group-card">
                            <div class="card-header">
                                <div>
                                    <p class="sub">作成者: <?= h($ownerName) ?></p>
                                    <h3><?= h($group['groupName']) ?></h3>
                                </div>
                                <?php if ($isMember) { ?>
                                    <span class="badge">参加中</span>
                                <?php } ?>
                            </div>
                            <p class="description"><?= nl2br(h($group['description'] ?: 'まだ紹介文がありません。')) ?></p>
                            <div class="meta">
                                <span><?= $group['memberCount'] ?>人が参加</span>
                                <span>作成日: <?= $createdText ?></span>
                            </div>
                            <div class="actions">
                                <?php if ($userID) { ?>
                                    <?php if ($isMember) { ?>
                                        <form method="post">
                                            <input type="hidden" name="groupID" value="<?= h($groupId) ?>">
                                            <input type="hidden" name="action" value="leave">
                                            <button type="submit" class="secondary-btn">退出する</button>
                                        </form>
                                        <a href="./GroupRoom.php?groupID=<?= h($groupId) ?>" class="primary-btn ghost">チャットを開く</a>
                                    <?php } else { ?>
                                        <form method="post">
                                            <input type="hidden" name="groupID" value="<?= h($groupId) ?>">
                                            <input type="hidden" name="action" value="join">
                                            <button type="submit" class="primary-btn">参加する</button>
                                        </form>
                                        <button class="secondary-btn" disabled>チャットを開く</button>
                                    <?php } ?>
                                <?php } else { ?>
                                    <a class="primary-btn" href="../Login/LoginIn.php">ログインして参加</a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
