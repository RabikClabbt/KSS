<?php
session_start();
require '../src/db-connect.php';
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
        $file = './File/' . substr(sha1(basename($_FILES['file']['tmp_name']) . rand(0, 9)), 0, 15) . '.' . strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
        }
    } else {
        $file = null;
    }
    if (!empty($commentText) || $file !== null) {
        $sql = $pdo->prepare('INSERT INTO GlobalChat (userID, commentText, appendFile) VALUES (?, ?, ?)');
        $sql->execute([$userid, $commentText, $file]);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/toppage.css" />
    <link rel="icon" href="../image/SiteIcon.svg" type="image/svg">
    <title>Yadi-X</title>
</head>
<body>
    <div class="screen">
        <div class="headerr">
            <?php require '../Header/Header.php'; ?>
        </div>
        <div class="content">
            <div class="sideber">
                <?php
                $chatsql = $pdo->prepare("SELECT DISTINCT CASE WHEN dm.userID = :userID THEN dm.partnerID ELSE dm.userID END AS friendID, u.nickname, u.profileIcon
                        FROM DirectMessage dm JOIN Users u ON (CASE WHEN dm.userID = :userID THEN dm.partnerID ELSE dm.userID END) = u.userID
                        WHERE dm.userID = :userID OR dm.partnerID = :userID");
                $chatsql->execute(['userID' => $userID]);
                $directchat = $chatsql->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <ul>
                    <li>
                        <label>
                            <div class="menu-icon">
                                <form action="../Search/Search.php" method="post">
                                    <button type="submit" class="search-buttom">
                                        <img src="../image/SearchIcon.svg" alt="Search" class="icon-img-search">
                                    </button>
                                    <input type="hidden" id="search-input" name="search">
                                </form>
                            </div>
                            <form action="../Search/Search.php" method="post">
                                <input type="hidden" id="search-input" name="search">
                                <button type="submit" class="search-buttom">
                                    <span class="menu-text">Search</span>
                                </button>
                            </form>
                        </label>
                    </li>
                    <li>
                        <label>
                            <div class="menu-icon">
                                <a href="../Question/ListForum.php">
                                    <img src="../image/Q&A.svg" alt="Q&A" class="icon-img">
                                </a>
                            </div>
                            <a href="../Question/ListForum.php"><span class="menu-text">Q&A</span></a>
                        </label>
                    </li>
                    <li>
                        <label><?php
                            if (isset($_SESSION['users'])) { ?>
                                <div class="menu-icon">
                                    <img src="../image/Chat.svg" alt="Chat" class="icon-img">
                                </div>
                                <div class="accordion-list">
                                    <span class="menu-text">Chat</span>
                                    <div class="list">
                                        <?php
                                        foreach ($directchat as $chat) { ?>
                                            <p class="listname"><a href="../PersonalChat/PersonalChat.php?partnerID=<?= $chat['friendID'] ?>"> <?= $chat['nickname'] ?></a></p>
                                        <?php } ?>
                                    </div>
                                </div><?php
                            } else { ?>
                                <div class="menu-icon">
                                    <a href="../Login/LoginIn.php">
                                        <img src="../image/Chat.svg" alt="Chat" class="icon-img">
                                    </a>
                                </div>
                                <a href="../Login/LoginIn.php"><span class="menu-text">Chat</span></a><?php
                            } ?>
                        </label>
                    </li>
                    <li>
                        <label><?php if (isset($_SESSION['users'])) { ?>
                                <div class="menu-icon">
                                    <a href="../GroupChat/GroupList.php">
                                        <img src="../image/GroupChat.svg" alt="Group Chat" class="icon-img">
                                    </a>
                                </div>
                                <a href="../GroupChat/GroupList.php"><span class="menu-text">Group Chat</span></a>
                            <?php } else { ?>
                                <div class="menu-icon">
                                    <a href="../Login/LoginIn.php">
                                        <img src="../image/GroupChat.svg" alt="Group Chat" class="icon-img">
                                    </a>
                                </div>
                                <a href="../Login/LoginIn.php"><span class="menu-text">Group Chat</span></a>
                            <?php } ?>
                        </label>
                    </li>
                </ul>
            </div>
            <div class="main-content">
                <div class="global-header">
                    <div>
                        <p class="eyebrow">グローバルフィード</p>
                        <h1>全体のつぶやき</h1>
                        <p class="lead">最新の投稿や添付画像をタイムライン形式で確認できます。</p>
                    </div>
                    <div class="action-chip">リアルタイムで更新</div>
                </div>
                <div class="global-chat">
                    <?php
                    $user = $pdo->prepare('SELECT g.*, u.* FROM GlobalChat g JOIN Users u ON g.userID = u.userID ORDER BY g.commentID DESC');
                    $user->execute();
                    $questions = $user->fetchAll(PDO::FETCH_ASSOC);
                    $rply = $pdo->prepare('SELECT COUNT(*) as rplyCount FROM GlobalChat WHERE replyID = ?');
                    if (empty($questions)) { ?>
                        <p class="empty-state">まだ投稿がありません。最初のメッセージを共有してみましょう。</p>
                    <?php }
                    foreach ($questions as $row) {
                        if ($row['replyID'] == null) {
                            $rply->execute([$row['commentID']]);
                            $rplya = $rply->fetch(PDO::FETCH_ASSOC);
                            $rplyCount = $rplya['rplyCount']; ?>
                            <div class="chat-comment">
                                <?php if ($row['userID'] != "Anonymous") { ?>
                                <a href="../Profile/OtherProfile.php?userID=<?= $row['userID'] ?>" class="account">
                                <?php } else { ?>
                                <a href="#" class="account">
                                <?php } ?>
                                    <div class="account-image"><?php
                                        if (!empty($row['profileIcon'])) {
                                            ?><img src="<?= htmlspecialchars($row['profileIcon']) ?>" alt="画像が読み込めません"><?php
                                        } else {
                                            ?><img src="../image/DefaultIcon.svg" alt="ProfileImage"><?php
                                        } ?>
                                    </div>
                                    <p class="account-name"><?= htmlspecialchars($row['nickname']) ?> </p>
                                </a>
                                <a href="Globalrply.php?commentID=<?= $row['commentID'] ?>" class="linkrply-atag">
                                    <p class="comment"><?= htmlspecialchars($row['commentText']) ?></p><?php
                                    if ($row['appendFile']) { ?>
                                        <img src="<?= $row['appendFile'] ?>" alt="画像を読み込めません"><?php
                                    } ?>
                                </a>
                                <div class="rply">
                                    <img src="../image/RplyMark.svg" alt="rply" height="20" width="20">
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
                <form action="TopPage.php" method="post" enctype="multipart/form-data" class="text-box">
                    <div id="file-preview-container">
                        <img id="file-preview" />
                        <span id="file-name"></span>
                        <img src="../image/Dustbin.svg" id="delete-button" onclick="removeFile()" alt="削除">
                    </div>
                    <div class="send">
                        <input type="hidden" name="userID" value=<?= $userID ?>>
                        <input type="text" class="chat-text" placeholder="テキストを入力" name="commentText" spellcheck="false">
                        <div class="image-preview">
                            <img id="preview-image" src="">
                        </div>
                        <button type="button" onclick="triggerFileInput(event)" class="file-icon">
                            <img src="../image/FileIcon.svg" width="20" height="20" alt="ファイル添付">
                        </button>
                        <input type="file" id="file-upload" name="file" style="display: none;" onchange="displayFileName(this)">
                        <button type="submit" class="send-button">
                            <img src="../image/SendIcon.svg" width="20" height="20" alt="送信">
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="./js/appendImage.js"></script>
</body>
</html>
