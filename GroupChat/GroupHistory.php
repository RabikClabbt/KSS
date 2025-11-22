<?php
require '../src/db-connect.php';

$pdo = new PDO($connect, user, pass);
$groupID = $_GET['groupID'] ?? null;
$viewerID = $_GET['userID'] ?? '';

if (!$groupID) {
    exit();
}

$sql = 'SELECT gm.messageID, gm.userID, gm.commentText, gm.appendFile, gm.createdAt, u.nickname, u.profileIcon
        FROM GroupMessages gm
        JOIN Users u ON gm.userID = u.userID
        WHERE gm.groupID = ?
        ORDER BY gm.createdAt ASC, gm.messageID ASC';
$history = $pdo->prepare($sql);
$history->execute([$groupID]);

if ($history->rowCount() >= 1):
    foreach ($history as $row):
        $isMine = ($row['userID'] === $viewerID);
?>
        <div class="message <?= $isMine ? 'mine' : 'other' ?>">
            <div class="user">
                <div class="avatar">
                    <?php if (!empty($row['profileIcon'])) { ?>
                        <img src="<?= htmlspecialchars($row['profileIcon']) ?>" alt="profileIcon">
                    <?php } else { ?>
                        <img src="../image/DefaultIcon.svg" alt="profileIcon">
                    <?php } ?>
                </div>
                <div class="user-meta">
                    <p class="nickname"><?= htmlspecialchars($row['nickname']) ?></p>
                    <span class="timestamp"><?= htmlspecialchars($row['createdAt']) ?></span>
                </div>
            </div>
            <p class="body"><?= nl2br(htmlspecialchars($row['commentText'], ENT_QUOTES, 'UTF-8')) ?></p>
            <?php if (!empty($row['appendFile'])): ?>
                <div class="appendimg">
                    <img src="<?= htmlspecialchars($row['appendFile'], ENT_QUOTES, 'UTF-8') ?>" alt="添付ファイル">
                </div>
            <?php endif; ?>
        </div>
<?php
    endforeach;
else:
?>
    <p class="empty">まだメッセージがありません。最初のメッセージを投稿しましょう。</p>
<?php
endif;
?>
