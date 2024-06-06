<?php
session_start();
require '../db-connect.php';
$pdo = new PDO($connect, user, pass);
$sql = $pdo->prepare('SELECT g.commentID,g.replyID,g.commentText,g.appendFile, u.* FROM GlobalChat g JOIN Users u ON g.userID = u.userID WHERE g.commentID=?');
$sql -> execute([$_GET['commentID']]);
$question = $sql->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/rply.css" />
    <title>グローバルチャット詳細</title>
</head>
<body>
    <div class="headerr">
        <?php require '../Header/Header.php'; ?>
    </div>
    <div class = "main-content">
        <?php
        foreach($question as $row){
            ?>
            <div class="chat-comment">
                <div class="account">
                    <div class="account-image">
                        <?php
                        if (!empty($row['profileIcon'])) {
                            echo '<a href="../Profile/Profile.php"><img src="', htmlspecialchars($row['profileIcon']), '" alt="ProfileImage"></a>';
                        } else {
                            echo '<a href="../Profile/Profile.php"><img src="../image/DefaultIcon.svg" alt="ProfileImage"></a>';
                        }
                        ?>
                    </div>
                    <a href="../Profile/Profile.php?id=',$row['userID'],'"><p class="account-name"><?php htmlspecialchars($row['nickname']) ?></p></a>
                </div>
                <a href="Globalrply.php?id=',$row['commentID'],'" class="linkrply">
                <p class="comment"><?php htmlspecialchars($row['commentText']) ?></p></a>';
                <div class="rply">
                    <img src="../Image/rplyicon.svg" alt="rply" height="20" width="20">
                    <div class="balloon3-left">
                        <p><?php $rplyCount ?></p>
                    </div>
                    <img src="../Image/goodicon.svg" alt="good" height="20" width="20">
                </div>';
            </div>
            <?php
        }
        ?>
    </div>
</body>