<?php
session_start();
require '../src/db-connect.php';
$pdo = new PDO($connect, user, pass);
if (isset($_SESSION['users'])) {
    $userID = $_SESSION['users']['id'];
} else {
    $userID = "Anonymous"; // 例: ログインしていないユーザーのためのデフォルト値
}
$commentText = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = htmlspecialchars($_POST['userID']);
    $commentID = htmlspecialchars($_POST['commentID']);
    $commentText = htmlspecialchars($_POST['commentText']);
    // ファイルがアップロードされた場合
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        if (!file_exists('File')) {
            if (!mkdir('File')) {
                die('Failed to create directory.');
            }
        }
        $file = './File/' . substr(sha1(basename($_FILES['file']['tmp_name']) . rand(0, 9)), 0, 15) . '.' .strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
        }
    }else{
        $file=null;
    }
    if (!empty($commentText) || $file !== null) {
        $rp = $pdo->prepare('INSERT INTO GlobalChat (userID, replyID, commentText, appendFile) VALUES (?, ?, ?, ?)');
        $rp->execute([$userid,$commentID,$commentText,$file]);
        $_POST = array();
        $sql = $pdo->prepare('SELECT g.*, u.* FROM GlobalChat g JOIN Users u ON g.userID = u.userID WHERE g.commentID = ?');
        $sql->execute([$commentID]);
        $comment = $sql->fetch(PDO::FETCH_ASSOC);
        $replySql = $pdo->prepare('SELECT g.*, u.* FROM GlobalChat g JOIN Users u ON g.userID = u.userID WHERE g.replyID = ? ORDER BY g.commentID ASC');
        $replySql->execute([$commentID]);
        $replies = $replySql->fetchAll(PDO::FETCH_ASSOC);
        //header('Location: ' . $_SERVER['PHP_SELF']);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?commentID=' . $commentID);
        exit();
    }
}else if (isset($_GET['commentID'])) {
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
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/Globalrply.css" />
    <link rel="icon" href="../image/SiteIcon.svg" type="image/svg">
    <title><?= $comment['nickname'] ?>さんの投稿:<?= $comment['commentText'] ?> | Yadi-X</title>
</head>
<body>
    <div class="headerr">
        <?php require '../Header/Header.php'; ?>
    </div>
    <div class="main-content">
        <?php
        if (!empty($comment)){
            $rply = $pdo->prepare('SELECT COUNT(*) as rplyCount FROM GlobalChat WHERE replyID = ?');
            $rply->execute([$commentID]);
            $rplya = $rply->fetch(PDO::FETCH_ASSOC);
            $rplyCount = $rplya['rplyCount']; ?>
            <div class="rplychat">
                <div class="chat-comment">
                    <div class="account">
                        <?php if ($comment['userID'] != "Anonymous") { ?>
                        <a href="../Profile/OtherProfile.php?userID=<?= htmlspecialchars($comment['userID']) ?>">
                        <?php } else { ?>
                        <a href="#" class="account">
                        <?php } ?>
                            <div class="circle"><?php
                                if (!empty($comment['profileIcon'])){ ?>
                                    <img src="<?= htmlspecialchars($comment['profileIcon']) ?>" alt="ProfileImage"><?php
                                }else{ ?>
                                    <img src="../image/DefaultIcon.svg" alt="ProfileImage"><?php
                                } ?>
                            </div>
                            <div class="nickname"><?= htmlspecialchars($comment['nickname']) ?></div>
                        </a>
                    </div>
                    <p class="comment"><?= htmlspecialchars($comment['commentText']) ?></p>
                    <div class="chat-comment-img">
                        <?php if($comment['appendFile']) { ?>
                            <img src="<?= $comment['appendFile'] ?>" alt="画像を読み込めません">
                        <?php } ?>
                    </div>
                    <div class="rply">
                        <img src="../image/RplyMark.svg" alt="rply" height="20" width="20">
                        <div class="balloon3-left">
                            <p><?= $rplyCount ?></p>
                        </div>
                    </div>
                </div>
                <!-- リプライ表示 -->
                <div class="rply-comments"><?php
                    foreach ($replies as $rply){ ?>
                        <div class="rply-comment">
                            <div class="account">
                                <?php if ($rply['userID'] != "Anonymous") { ?>
                                <a href="../Profile/OtherProfile.php?userID=<?= htmlspecialchars($rply['userID']) ?>">
                                <?php } else { ?>
                                <a href="#" class="account">
                                <?php } ?>
                                    <div class="circle"><?php
                                        if (!empty($rply['profileIcon'])){ ?>
                                            <img src="<?= htmlspecialchars($rply['profileIcon']) ?>" alt="ProfileImage"><?php
                                        }else{ ?>
                                            <img src="../image/DefaultIcon.svg" alt="ProfileImage"><?php
                                        } ?>
                                    </div>
                                    <div class="nickname"><?= htmlspecialchars($rply['nickname']) ?></div>
                                </a>
                            </div>
                            <a href="Globalrply.php?commentID=<?= $rply['commentID'] ?>" class="linkrply-atag">
                                <p class="comment"><?= htmlspecialchars($rply['commentText']) ?></p><?php
                                if($rply['appendFile']){?>
                                    <img src="<?= $rply['appendFile'] ?>" alt="画像を読み込めません"><?php
                                }?>
                            </a>
                        </div><?php
                    } ?>
                </div>
            </div>
            <!-- 入力フォーム -->
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data" class="text-box">
                    <div id="file-preview-container">
                        <img id="file-preview" />
                        <span id="file-name"></span>
                        <img src="../image/Dustbin.svg" id="delete-button" onclick="removeFile()" alt="削除">
                    </div>
                    <div class="send">
                        <input type="hidden" name="userID" value="<?= $userID ?>">
                        <input type="hidden" name="commentID" value="<?= $commentID ?>">
                        <input type="text" class="chat-text" placeholder="テキストを入力" name="commentText" value="<?= htmlspecialchars($commentText) ?>" spellcheck="false">
                        <label for="file-upload" class="send-file">
                            <img src="../image/FileIcon.svg" width="20" height="20" alt="ファイル添付">
                        </label>
                        <input type="file" id="file-upload" name="file" style="display: none;" onchange="displayFileName(this)">
                        <button type="submit" class="send-button">
                            <img src="../image/SendIcon.svg" width="20" height="20" alt="送信">
                        </button>
                    </div>
                </form>
        <?php }else{ ?>
            <p>コメントが見つかりませんでした。</p>
        <?php } ?>
    </div>
    <script src="./js/appendImage.js"></script>
</body>
</html>
