<?php
session_start();
require '../db-connect.php';
$pdo = new PDO($connect, user, pass);
if (isset($_SESSION['users'])) {
    $userID = $_SESSION['users']['id'];
} else {
    $userID = "Anonymous"; // 例: ログインしていないユーザーのためのデフォルト値
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = htmlspecialchars($_POST['userID']);
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
        }
    }else{
        $file=null;
    }
    $sql = $pdo->prepare('INSERT INTO GlobalChat (userID, commentText, appendFile) VALUES (?, ?, ?)');
    $sql->execute([$userid,$commentText,$file]);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/toppage.css" />
    <title>トップ画面</title>
</head>
<body>
    <div class="screen">
        <div class="headerr">
            <?php require '../Header/Header.php'; ?>
        </div>
        <div class="content">
            <div class="sideber">
                <ul>
                    <li>
                        <a href="../Question/ListForum.php">
                            <div class="menu-icon">
                                <img src="../image/Q&A.png" alt="Q&A" class="icon-img">
                            </div>
                            <span class="menu-text">Q&A</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <div class="menu-icon">
                                <img src="../image/Chat.png" alt="Chat" class="icon-img">
                            </div>
                            <span class="menu-text">Chat</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <div class="menu-icon">
                                <img src="../image/GroupChat.png" alt="Group Chat" class="icon-img">
                            </div>
                            <span class="menu-text">Group Chat</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="main-content">
                <div class="global-chat">
                    <?php
                    $user = $pdo->prepare('SELECT g.*, u.* FROM GlobalChat g JOIN Users u ON g.userID = u.userID ORDER BY g.commentID DESC');
                    $user->execute();
                    $questions = $user->fetchAll(PDO::FETCH_ASSOC);
                    $rply = $pdo->prepare('SELECT COUNT(*) as rplyCount FROM GlobalChat WHERE replyID = ?');
                    foreach ($questions as $row) {
                        if ($row['replyID'] == null) {
                            $rply->execute([$row['commentID']]);
                            $rplya = $rply->fetch(PDO::FETCH_ASSOC);
                            $rplyCount = $rplya['rplyCount'];?>
                            <div class="chat-comment">
                                <div class="account">
                                    <div class="account-image"><?php
                                        if (!empty($row['profileIcon'])) {
                                            ?><a href="../相手のプロフィール?userID=<?= $row['userID'] ?>"><img src="<?php htmlspecialchars($row['profileIcon']) ?>" alt="ProfileImage"></a><?php
                                        } else {
                                            ?><img src="../image/DefaultIcon.png" alt="ProfileImage"><?php
                                        }?>
                                    </div>
                                    <a href="../Profile/Profile.php?userID=<?= $row['userID'] ?>"><p class="account-name"><?= htmlspecialchars($row['nickname']) ?> </p></a>
                                </div>
                                <a href="Globalrply.php?commentID=<?= $row['commentID'] ?>" class="linkrply atag">
                                    <p class="comment"><?= htmlspecialchars($row['commentText']) ?></p><?php
                                    if($row['appendFile']){?>
                                        <img src="<?= $row['appendFile'] ?>" alt="画像を読み込めません"><?php
                                    }?>
                                </a>
                                <div class="rply">
                                    <img src="../image/RplyMark.png" alt="rply" height="20" width="20">
                                    <div class="balloon3-left">
                                        <p><?= $rplyCount ?></p>
                                    </div>
                                </div>
                            </div><?php
                        }
                    }
                    ?>
                </div>
                <!-- 入力フォーム -->
                <div class="send">
                    <form action="TopPage.php" method="post" enctype="multipart/form-data" class="text-box">
                        <input type="hidden" name="userID" value=<?= $userID ?>>
                        <input type="text" class="chat-text" placeholder="テキストを入力" name="commentText" spellcheck="false">
                        <div class="image-preview">
                            <img id="preview-image" src="" >
                        </div>
                        <label for="file-upload" class="send-file">
                            <img src="../image/FileIcon.png" width="20" height="20" alt="ファイル添付">
                        </label>
                        <input type="file" id="file-upload" name="file" style="display: none;" onchange="displayFileName()">
                        <button type="submit" class="send-button">
                            <img src="../image/SendIcon.png" width="20" height="20" alt="送信">
                        </button>
                    </form>
                </div>
                <!-- ------------ -->
            </div>
        </div>
    </div>
</body>
</html>
