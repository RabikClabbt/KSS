<?php
<<<<<<< HEAD
require '../src/db-connect.php';

$pdo=new PDO($connect,user,pass);

$userID = $_GET['userID']; // 自身のユーザーID
$partnerID = $_GET['partnerID']; // 相手側のユーザーID

$sql = 'SELECT userID, partnerID, commentID, commentText, appendFile FROM DirectMessage WHERE (userID = :userID AND partnerID = :partnerID) OR (userID = :partnerID AND partnerID = :userID) ORDER BY commentID ASC';
$keyArray = array('userID' => $userID, 'partnerID' => $partnerID);
$history = $pdo->prepare($sql);
$history->execute($keyArray);

if ($history->rowCount() >= 1):
    foreach ($history as $row):

        $usql = 'SELECT userID, nickname, profileIcon FROM Users WHERE userID = ?';
        $ukeyArray = array($row['userID']);
        $user = $pdo->prepare($usql);
        $user->execute($ukeyArray);
        $user = $user->fetch();

        if ($userID == $row['userID']): ?>
            <div class="my">
                <div class="user">
                    <a href="../Profile/OtherProfile.php?userID=<?= $user['userID'] ?>">
                        <div class="nickname"><?= htmlspecialchars($user['nickname']) ?></div>
                        <div class="circle">
                            <?php if (!empty($user['profileIcon'])) { ?>
                                <img src="<?= $user['profileIcon'] ?>" alt="profileIcon">
                            <?php } else { ?>
                                <img src="../image/DefaultIcon.svg" alt="profileIcon">
                            <?php } ?>
                        </div>
                    </a>
                </div>
                <?= nl2br(htmlspecialchars($row['commentText'], ENT_QUOTES, 'UTF-8')) ?>
                <!-- ファイルがあれば表示する -->
                <?php if (!empty($row['appendFile'])): ?>
                    <div class="appendimg">
                        <img src="./<?= htmlspecialchars($row['appendFile'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <br>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="partner">
                <div class="user">
                    <a href="../Profile/OtherProfile.php?userID=<?= $user['userID'] ?>">
                        <div class="circle">
                            <?php if (!empty($user['profileIcon'])) { ?>
                                <img src="<?= $user['profileIcon'] ?>" alt="profileIcon">
                            <?php } else { ?>
                                <img src="../image/DefaultIcon.svg" alt="profileIcon">
                            <?php } ?>
                        </div>
                        <div class="nickname"><?= htmlspecialchars($user['nickname']) ?></div>
                    </a>
                </div>
                <?= nl2br(htmlspecialchars($row['commentText'], ENT_QUOTES, 'UTF-8')) ?>
                <!-- ファイルがあれば表示する -->
                <?php if (!empty($row['appendFile'])): ?>
                    <div class="appendimg">
                        <img src="./<?= htmlspecialchars($row['appendFile'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                <?php endif; ?>
            </div>
            <br>
        <?php endif; ?>
        <?php $count = $row['commentID']; ?>
    <?php endforeach; ?>
<?php else: ?>
    <p>チャット履歴がありません。</p>
<?php endif;?>
=======
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
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
