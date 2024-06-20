<?php
session_start();
require '../db-connect.php';
$pdo = new PDO($connect, user, pass);
if (isset($_SESSION['users'])) {
    $userID = $_SESSION['users']['id'];
} else {
    $userID = "Anonymous"; // 例: ログインしていないユーザーのためのデフォルト値
}
$commentText = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = htmlspecialchars($_POST['userID']);
    $rplyID = htmlspecialchars($_POST['commentID']);
    $commentText = htmlspecialchars($_POST['commentText']);
    // ファイルがアップロードされた場合
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        if (!file_exists('File')) {
            if (!mkdir('File')) {
                die('Failed to create directory.');
            }
        }
        $file = './File/' . basename($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
            echo 'File uploaded successfully.';
        } else {
            die('Failed to move uploaded file.');
        }
    }else{
        $file="";
    }
    $rp = $pdo->prepare('INSERT INTO GlobalChat (userID, replyID, commentText, appendFile) VALUES (?, ?, ?, ?)');
    $rp->execute([$userid,$rplyID,$commentText,$file]);
    $_POST = array();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
if (isset($_GET['commentID'])) {
    $commentID = htmlspecialchars($_GET['commentID']);
    // 元のコメントを取得
    $sql = $pdo->prepare('SELECT g.*, u.* FROM GlobalChat g JOIN Users u ON g.userID = u.userID WHERE g.commentID = ?');
    $sql->execute([$commentID]);
    $comment = $sql->fetch(PDO::FETCH_ASSOC);
    // リプライを取得
    $replySql = $pdo->prepare('SELECT g.*, u.* FROM GlobalChat g JOIN Users u ON g.userID = u.userID WHERE g.replyID = ? ORDER BY g.commentID ASC');
    $replySql->execute([$commentID]);
    $replies = $replySql->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo 'コメントを取得できませんでした。';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/Globalrply.css" />
    <title>グローバルチャット詳細</title>
</head>
<body>
    <div class="headerr">
        <?php require '../Header/Header.php'; ?>
    </div>
    <div class="main-content"><?php
        if (!empty($comment)){
            $rply = $pdo->prepare('SELECT COUNT(*) as rplyCount FROM GlobalChat WHERE replyID = ?');
            $rply->execute([$commentID]);
            $rplya = $rply->fetch(PDO::FETCH_ASSOC);
            $rplyCount = $rplya['rplyCount'];?>
            <div class="post">
                <div class="chat-comment">
                    <div class="account">
                        <div class="account-image"><?php
                            if (!empty($comment['profileIcon'])){ ?>
                                <a href="../Profile/Profile.php"><img src="<?= htmlspecialchars($comment['profileIcon']) ?>" alt="ProfileImage"></a><?php
                            }else{ ?>
                                <a href="../Profile/Profile.php"><img src="../image/DefaultIcon.png" alt="ProfileImage"></a><?php
                            } ?>
                        </div>
                        <a href="../Profile/Profile.php?id=<?= htmlspecialchars($comment['userID']) ?>"><p class="account-name"><?= htmlspecialchars($comment['nickname']) ?></p></a>
                    </div>
                    <p class="comment"><?= htmlspecialchars($comment['commentText']) ?></p><?php
                    if($comment['appendFile']){?>
                        <img src="<?= $comment['appendFile'] ?>" alt="画像を読み込めません"><?php
                    }?>
                    <div class="rply">
                        <img src="../image/RplyMark.png" alt="rply" height="20" width="20">
                        <div class="balloon3-left">
                            <p><?= $rplyCount ?></p>
                        </div>
                        <img src="../image/GoodSine.png" alt="good" height="20" width="20">
                    </div>
                </div>
                <!-- 入力フォーム -->
                <div class="send">
                    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data" class="text-box">
                        <input type="hidden" name="userID" value="<?= $userID ?>">
                        <input type="hidden" name="commentID" value="<?= $commentID ?>">
                        <input type="text" class="chat-text" placeholder="テキストを入力" name="commentText" value="<?= htmlspecialchars($commentText) ?>" spellcheck="false">
                        <label for="file-upload" class="send-file">
                            <img src="../image/FileIcon.png" width="20" height="20" alt="ファイル添付">
                        </label>
                        <input type="file" id="file-upload" name="file" style="display: none;" onchange="displayFileName(this)">
                        <button type="submit" class="send-button">
                            <img src="../image/SendIcon.png" width="20" height="20" alt="送信">
                        </button>
                    </form>
                </div>
            </div>
            <!-- リプライ表示 -->
            <div class="rply-comments"><?php
                foreach ($replies as $rply){ ?>
                    <div class="rply-comment">
                        <div class="rplyaccount">
                            <div class="rplyaccount-image">
                                <?php if (!empty($rply['profileIcon'])): ?>
                                    <a href="../Profile/Profile.php"><img src="<?= htmlspecialchars($rply['profileIcon']) ?>" alt="ProfileImage"></a>
                                <?php else: ?>
                                    <img src="../image/DefaultIcon.png" alt="ProfileImage">
                                <?php endif; ?>
                            </div>
                            <a href="../Profile/Profile.php?id=<?= htmlspecialchars($rply['userID']) ?>"><p class="account-name"><?= htmlspecialchars($rply['nickname']) ?></p></a>
                        </div>
                        <a href="Globalrply.php?commentID=<?= $rply['commentID'] ?>" class="linkrply atag">
                            <p class="comment"><?= htmlspecialchars($rply['commentText']) ?></p>
                        </a>
                        <div class="rply">
                            <img src="../image/GoodSine.png" alt="good" height="20" width="20">
                        </div>
                    </div><?php
                } ?>
            </div><?php
        }else{?>
            <p>コメントが見つかりませんでした。</p><?php
        }?>
    </div>
</body>
</html>
