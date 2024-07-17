<?php
session_start();
require 'db-connect.php';

$pdo = new PDO($connect, USER, PASS);

$userID = 'user'; // 自身のユーザーID
$partnerID = 'user2'; // 相手側のユーザーID

$sql = 'SELECT * FROM DirectMessage WHERE (userID = ? AND partnerID = ?) OR (userID = ? AND partnerID = ?) ORDER BY commentID ASC';
$keyArray = array($userID, $partnerID, $partnerID, $userID);
$history = $pdo->prepare($sql);
$history->execute($keyArray);

//チャット履歴を更新する
if ($history->rowCount() >= 1) {
    foreach ($history as $row) {
        if ($userID == $row['userID']) { ?>
            <div class="my">
                <?= $row['userID'] ?>
                <img src="./image/DefaultIcon.svg" alt="profileIcon" width="20" height="20">
                <br>
            <?php
            if ($row['appendFile'] != NULL) { ?>
                <div class="appendimg">
                <img src="./<?= htmlspecialchars($row['appendFile'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <br>
            <?php
            } ?>
            <?= $row['commentText'] ?>
            </div>
            <br>
            <?php
        } else { ?>
            <div class="partner">
            <img src="./image/DefaultIcon.svg" alt="profileIcon" width="20" height="20">
            <?= $row['userID'] ?>
            <br>
            <?php
            if ($row['appendFile'] != NULL) { ?>
                <div class="appendimg">
                <img src="./<?= htmlspecialchars($row['appendFile'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <br>
            <?php
            } ?>
            <?= $row['commentText'] ?>
            </div>
            <br>
            <?php
        } ?>
            <br>
            <?php
    } ?>
    <?php
} else { 
    echo 'チャット履歴がありません。';
}
?>
