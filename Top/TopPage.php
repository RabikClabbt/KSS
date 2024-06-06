<?php

session_start();
require '../db-connect.php';
$pdo = new PDO($connect, user, pass);
$userID = "";
if (isset($_SESSION['users'])) {
    $userID = $_SESSION['users'];
} else {
    $userID = "none"; // 例: ログインしていないユーザーのためのデフォルト値
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = htmlspecialchars($_POST['userID']);
    $commentText = htmlspecialchars($_POST['commentText']);
    $sql = $pdo->prepare('INSERT INTO GlobalChat (userID, commentText, appendFile) VALUES (?, ?, ?)');

    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        if (!file_exists('File')) {
            mkdir('File');
        }
        $file = 'File/' . basename($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
            //ファイルが正常にアップロードされました
        } else {
            //アップロードされたファイルの移動に失敗
        }
    }
    $file=null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/toppage.css" />
    <title>トップ画面</title>
    <script>
        function goToPage(url) {
            window.location.href = url;
        }

        function displayFileName(input) {
            const fileName = input.files[0]?.name || '';
            document.querySelector('.chat-text').value = fileName;
        }
    </script>
</head>
<body>
    <div class="headerr">
        <?php require '../Header/Header.php'; ?>
    </div>
    <div class="content">
        <div class="sideber">
            <div class="menu-icon" onclick="goToPage('../Question/ListForum.php')" title="Q&A">
                <img src="../image/Q&A.svg" alt="Q&A" class="icon-img">
            </div>
            <div class="menu-icon" onclick="goToPage('page2.php')" title="chat">
                <img src="../image/Chat.svg" alt="chat" class="icon-img">
            </div>
            <div class="menu-icon" onclick="goToPage('../GroupControl/GroupCreate.php')" title="group-chat">
                <img src="../image/GroupChat.svg" alt="group-chat" class="icon-img">
            </div>
        </div>
        <div class="main-content">
            <div class="global-chat">
                <?php
                $user = $pdo->prepare('SELECT g.commentID,g.replyID,g.commentText,g.appendFile, u.* FROM GlobalChat g JOIN Users u ON g.userID = u.userID');
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
                                    echo '<a href="../Profile/Profile.php"><img src="'. htmlspecialchars($row['profileIcon']) . '" alt="ProfileImage"></a>';
                                } else {
                                    echo '<a href="../Profile/Profile.php"><img src="../image/DefaultIcon.svg" alt="ProfileImage"></a>';
                                }?>
                            </div>
                        <a href="../Profile/Profile.php?id=<?= $row['userID'] ?>"><p class="account-name"><?= htmlspecialchars($row['nickname']) ?> </p></a>
                        </div>
                        <a href="Globalrply.php?commentID=<?= $row['commentID'] ?>" class="linkrply">
                                <p class="comment"><?= htmlspecialchars($row['commentText']) ?></p></a>
                        <div class="rply">
                                <img src="../image/RplyMark.svg" alt="rply" height="20" width="20">
                                <div class="balloon3-left">
                                    <p><?= $rplyCount ?></p>
                                </div>
                                <img src="../image/GoodSine.svg" alt="good" height="20" width="20">
                            </div>
                        </div><?php
                    }
                }
                ?>
            </div>
            <!-- 入力フォーム -->
            <div class="send">
                <form action="TopPage.php" method="post" enctype="multipart/form-data" class="text-box">
                    <input type="hidden" name="userID" value="">
                    <input type="text" class="chat-text" placeholder="テキストを入力" name="commentText" spellcheck="false">
                    <label for="file-upload" class="send-file">
                        <img src="../image/FileIcon.svg" width="20" height="20" alt="ファイル添付">
                    </label>
                    <input type="file" id="file-upload" name="file" style="display: none;" onchange="displayFileName(this)">
                    <button type="submit" class="send-button">
                        <img src="../image/SendIcon.svg" width="20" height="20" alt="送信">
                    </button>
                </form>
            </div>
            <!-- ------------ -->
        </div>
        <div class="sideber2">
            <p>トップ画面だよ</p>
        </div>
    </div>
</body>
</html>
