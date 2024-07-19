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
        }<?php
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
                        <?php
                        $chatsql=$pdo->prepare("SELECT d.*, u.* FROM DirectMessage d JOIN Users u ON d.partnerID = u.userID WHERE d.userID = ?");
                        $chatsql->execute([$userID]);
                        $directchat = $chatsql->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <ul>
                            <li>
                                <label>
                                    <div class="menu-icon">
                                        <a href="../Search/Search.php">
                                            <img src="../Image/SearchIcon.svg" alt="Search" class="icon-img-search">
                                        </a>
                                    </div>
                                    <a href="../Search/Search.php"><span class="menu-text">Search</span></a>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <div class="menu-icon">
                                        <a href="../Question/ListForum.php">
                                            <img src="../image/Q&A.svg" alt="Q&A" class="icon-img">
                                        </a href="../Question/ListForum.php">
                                    </div>
                                    <a href="../Question/ListForum.php"><span class="menu-text">Q&A</span></a>
                                </label>
                            </li>
                            <li>
                                <label><?php
                                    if(isset($_SESSION['users'])){?>
                                        <div class="menu-icon">
                                            <img src="../image/Chat.svg" alt="Chat" class="icon-img">
                                        </div>
                                        <div class="accordion-list">
                                            <span class="menu-text">Chat</span>
                                            <div class="list">
                                                <?php
                                                foreach($directchat as $chat){
                                                    ?><p class="listname"><a href="../PersonalChat/PersonalChat.php?partnerID=<?= $chat['userID'] ?>"> <?= $chat['nickname'] ?></a></p><?php
                                                }
                                                ?>
                                            </div>
                                        </div><?php
                                    } else {?>
                                        <div class="menu-icon">
                                            <a href="../Login/LoginIn.php">
                                                <img src="../image/Chat.svg" alt="Chat" class="icon-img">
                                            </a>
                                        </div>
                                        <a href="../Login/LoginIn.php"><span class="menu-text">Chat</span></a><?php
                                    }?>
                                </label>
                            </li>
                            <li>
                                <label><?php
                                    if(isset($_SESSION['users'])){?>
                                        <div class="menu-icon">
                                            <a href="../Login/LoginIn.php">
                                                <img src="../image/GroupChat.svg" alt="Group Chat" class="icon-img">
                                            </a>
                                        </div>
                                        <a href="../Login/LoginIn.php"><span class="menu-text">Group Chat</span></a><?php
                                    }else{?>
                                        <div class="menu-icon">
                                            <img src="../image/GroupChat.svg" alt="Group Chat" class="icon-img">
                                        </div>
                                        <span class="menu-text">Group Chat</span><?php
                                    }?>
                                </label>
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
                                                    ?><a href="../Profile/OtherProfile.php?userID=<?= $row['userID'] ?>"><img src="<?= htmlspecialchars($row['profileIcon']) ?>" alt="ProfileImage"></a><?php
                                                } else {
                                                    ?><img src="../image/DefaultIcon.svg" alt="ProfileImage"><?php
                                                }?>
                                            </div>
                                            <a href="../Profile/OtherProfile.php?userID=<?= $row['userID'] ?>"><p class="account-name"><?= htmlspecialchars($row['nickname']) ?> </p></a>
                                        </div>
                                        <a href="Globalrply.php?commentID=<?= $row['commentID'] ?>" class="linkrply atag">
                                            <p class="comment"><?= htmlspecialchars($row['commentText']) ?></p><?php
                                            if($row['appendFile']){?>
                                                <img src="<?= $row['appendFile'] ?>" alt="画像を読み込めません"><?php
                                            }?>
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
                        <div class="send">
                            <form action="TopPage.php" method="post" enctype="multipart/form-data" class="text-box">
                                <input type="hidden" name="userID" value=<?= $userID ?>>
                                <input type="text" class="chat-text" placeholder="テキストを入力" name="commentText" spellcheck="false">
                                <div class="image-preview">
                                    <img id="preview-image" src="" >
                                </div>
                                <label for="file-upload" class="send-file">
                                    <img src="../image/FileIcon.svg" width="20" height="20" alt="ファイル添付">
                                </label>
                                <input type="file" id="file-upload" name="file" style="display: none;" onchange="displayFileName()">
                                <button type="submit" class="send-button">
                                    <img src="../image/SendIcon.svg" width="20" height="20" alt="送信">
                                </button>
                            </form>
                        </div>
                        <!-- ------------ -->
                    </div>
                </div>
            </div>
        </body>
        </html>
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
<<<<<<< HEAD
                $chatsql=$pdo->prepare("SELECT DISTINCT CASE WHEN dm.userID = :userID THEN dm.partnerID ELSE dm.userID END AS friendID, u.nickname, u.profileIcon 
                        FROM DirectMessage dm JOIN Users u ON (CASE WHEN dm.userID = :userID THEN dm.partnerID ELSE dm.userID END) = u.userID 
                        WHERE dm.userID = :userID OR dm.partnerID = :userID");
                $chatsql->execute(['userID' => $userID]);
=======
                $chatsql=$pdo->prepare("SELECT d.*, u.* FROM DirectMessage d JOIN Users u ON (d.userID = u.userID OR d.partnerID = u.userID) WHERE (d.userID = ? AND d.partnerID = u.userID) OR (d.partnerID = ? AND d.userID = u.userID)");
                $chatsql->execute([$userID,$userID]);
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
                $directchat = $chatsql->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <ul>
                    <li>
                        <label>
                            <div class="menu-icon">
<<<<<<< HEAD
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
=======
                                <a href="../Search/Search.php">
                                    <img src="../image/SearchIcon.png" alt="Search" class="icon-img-search">
                                </a>
                            </div>
                            <a href="../Search/Search.php"><span class="menu-text">Search</span></a>
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
                        </label>
                    </li>
                    <li>
                        <label>
                            <div class="menu-icon">
                                <a href="../Question/ListForum.php">
                                    <img src="../image/Q&A.svg" alt="Q&A" class="icon-img">
                                </a href="../Question/ListForum.php">
                            </div>
                            <a href="../Question/ListForum.php"><span class="menu-text">Q&A</span></a>
                        </label>
                    </li>
                    <li>
                        <label><?php
                            if(isset($_SESSION['users'])){?>
                                <div class="menu-icon">
                                    <img src="../image/Chat.svg" alt="Chat" class="icon-img">
                                </div>
                                <div class="accordion-list">
                                    <span class="menu-text">Chat</span>
                                    <div class="list">
                                        <?php
<<<<<<< HEAD
                                        foreach($directchat as $chat){ ?>
                                            <p class="listname"><a href="../PersonalChat/PersonalChat.php?partnerID=<?= $chat['friendID'] ?>"> <?= $chat['nickname'] ?></a></p>
                                        <?php } ?>
=======
                                        foreach($directchat as $chat){
                                            ?><p class="listname"><a href="../PersonalChat/PersonalChat.php?partnerID=<?= $chat['userID'] ?>"> <?= $chat['nickname'] ?></a></p><?php
                                        }
                                        ?>
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
                                    </div>
                                </div><?php
                            } else {?>
                                <div class="menu-icon">
                                    <a href="../Login/LoginIn.php">
                                        <img src="../image/Chat.svg" alt="Chat" class="icon-img">
                                    </a>
                                </div>
                                <a href="../Login/LoginIn.php"><span class="menu-text">Chat</span></a><?php
                            }?>
                        </label>
                    </li>
                    <li>
                        <label><?php
                            if(isset($_SESSION['users'])){?>
                                <div class="menu-icon">
                                    <a href="../Login/LoginIn.php">
                                        <img src="../image/GroupChat.svg" alt="Group Chat" class="icon-img">
                                    </a>
                                </div>
                                <a href="../Login/LoginIn.php"><span class="menu-text">Group Chat</span></a><?php
                            }else{?>
                                <div class="menu-icon">
                                    <img src="../image/GroupChat.svg" alt="Group Chat" class="icon-img">
                                </div>
                                <span class="menu-text">Group Chat</span><?php
                            }?>
                        </label>
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
<<<<<<< HEAD
                                            ?><a href="../Profile/OtherProfile.php?userID=<?= $row['userID'] ?>"><img src="<?=htmlspecialchars($row['profileIcon'])?>" alt="画像が読み込めません"></a><?php
=======
                                            ?><a href="../Profile/OtherProfile.php?userID=<?= $row['userID'] ?>"><img src="<?php htmlspecialchars($row['profileIcon']) ?>" alt="画像が読み込めません"></a><?php
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
                                        } else {
                                            ?><img src="../image/DefaultIcon.svg" alt="ProfileImage"><?php
                                        }?>
                                    </div>
                                    <a href="../Profile/OtherProfile.php?userID=<?= $row['userID'] ?>"><p class="account-name"><?= htmlspecialchars($row['nickname']) ?> </p></a>
                                </div>
                                <a href="Globalrply.php?commentID=<?= $row['commentID'] ?>" class="linkrply-atag">
                                    <p class="comment"><?= htmlspecialchars($row['commentText']) ?></p><?php
                                    if($row['appendFile']){?>
                                        <img src="<?= $row['appendFile'] ?>" alt="画像を読み込めません"><?php
                                    }?>
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
                <div class="send">
                    <form action="TopPage.php" method="post" enctype="multipart/form-data" class="text-box">
                        <input type="hidden" name="userID" value=<?= $userID ?>>
                        <input type="text" class="chat-text" placeholder="テキストを入力" name="commentText" spellcheck="false">
                        <div class="image-preview">
                            <img id="preview-image" src="" >
                        </div>
                        <label for="file-upload" class="send-file">
                            <img src="../image/FileIcon.svg" width="20" height="20" alt="ファイル添付">
                        </label>
                        <input type="file" id="file-upload" name="file" style="display: none;" onchange="displayFileName()">
                        <button type="submit" class="send-button">
                            <img src="../image/SendIcon.svg" width="20" height="20" alt="送信">
                        </button>
                    </form>
                </div>
                <!-- ------------ -->
            </div>
        </div>
    </div>
</body>
</html>