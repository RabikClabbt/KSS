<?php
session_start();
require '../db-connect.php';
$pdo = new PDO($connect, user, pass);
$icons = [
    ['url' => 'page1.php', 'label' => 'Q&A', 'img' => 'Image/Q&A.svg'],
    ['url' => 'page2.php', 'label' => 'chat', 'img' => 'Image/chat.svg'],
    ['url' => 'page3.php', 'label' => 'group-chat', 'img' => 'Image/group-chat.svg']
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = $pdo->prepare('INSERT INTO GlobalChat (userID, commentText, appendFile) VALUES (?, ?, ?)');
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        if (!file_exists('File')) {
            if (!mkdir('File')) {
                die('Failed to create directory.');
            }
        }
        $file = 'File/' . basename($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
            //ファイルが正常にアップロードされました
        } else {
            die('Failed to move uploaded file.');
        }
    }
    $sql->execute([$_POST['userID'], $_POST['HeaderSearch'], $file ?? null]);
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
            <div class="menu-icon" onclick="goToPage('page1.php')" title="Q&A">
                <img src="Image/Q&A.svg" alt="Q&A" class="icon-img">
            </div>
            <div class="menu-icon" onclick="goToPage('page2.php')" title="chat">
                <img src="Image/chat.svg" alt="chat" class="icon-img">
            </div>
            <div class="menu-icon" onclick="goToPage('page3.php')" title="group-chat">
                <img src="Image/group-chat.svg" alt="group-chat" class="icon-img">
            </div>
        </div>
        <div class="main-content">
            <div class="global-chat">
                <?php
                $user = $pdo->prepare('SELECT g.*, u.* FROM GlobalChat g JOIN Users u ON g.userID = u.userID');
                $user->execute();
                $questions = $user->fetchAll(PDO::FETCH_ASSOC);
                $rply = $pdo->prepare('SELECT COUNT(*) as rplyCount FROM GlobalChat WHERE replyID = ?');
                foreach ($questions as $row) {
                    if ($row['replyID'] == null) {
                        $rply->execute([$row['commentID']]);
                        $rplya = $rply->fetch(PDO::FETCH_ASSOC);
                        $rplyCount = $rplya['rplyCount'];
                        echo '<div class="chat-comment">';
                        echo '<div class="account">
                                    <div class="account-image">
                                    <a href="../page1" class="profile"><img src="', $row['profileIcon'], '" alt="ProfileImage"></a>
                                    </div>
                                    <a href="" class="profile2"><p class="account-name">', $row['nickname'], '</p></a>
                            </div>';
                        echo '<p class="comment">', $row['commentText'], '</p>';
                        echo '<a href="Globalrply.php" class="linkrply"></a>';
                        echo '<div class="rply">
                                <img src="Image/rplyicon.svg" alt="rply" height="20" width="20">
                                <div class="balloon3-left">
                                    <p>', $rplyCount, '</p>
                                </div>
                                <img src="Image/goodicon.svg" alt="good" height="20" width="20">
                            </div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <!-- 入力フォーム -->
            <?php
            if (isset($_SESSION['users'])) {
                $userID = $_SESSION['users']['userID'];
            } else {
                $userID = 0; // 例: ログインしていないユーザーのためのデフォルト値
            }
            ?>
            <div class="send">
                <form action="TopPage.php" method="post" enctype="multipart/form-data" class="text-box">
                    <input type="hidden" name="userID" value="<?php echo $userID; ?>">
                    <input type="text" autocomplete="off" class="chat-text" placeholder="テキストを入力" name="HeaderSearch" spellcheck="false">
                    <button type="submit" class="send-button">
                        <img src="Image/send-icon.svg" width="20" height="20" alt="送信">
                    </button>
                    <label for="file-upload" class="send-file">
                        <img src="Image/file-icon.svg" width="20" height="20" alt="ファイル添付">
                    </label>
                    <input type="file" id="file-upload" name="file" style="display: none;" onchange="displayFileName(this)">
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
