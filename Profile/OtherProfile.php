<?php
session_start();
require '../src/db-connect.php';
require '../Header/Header.php';

try {
    $pdo = new PDO($connect, user, pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

if (!isset($_GET['userID']) || $_GET['userID'] == 'Anonymous' ) {
    echo "ユーザーIDが指定されていません。";
    exit;
}

$otherUserID = htmlspecialchars($_GET['userID']);

// セッションIDとotherUserIDが一致する場合にProfile.phpにリダイレクト
if (isset($_SESSION['users']['id']) && $_SESSION['users']['id'] == $otherUserID) {
    echo '<script type="text/javascript">
              window.location.href = "Profile.php";
          </script>';
    exit;
}

$sql = $pdo->prepare('SELECT * FROM Users WHERE userID = ?');
$sql->execute([$otherUserID]);
$user = $sql->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "ユーザーが見つかりません。";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['directMessage'])) {
    if (!isset($_SESSION['users']['id'])) {
        echo "<script>alert('ログインしてください');</script>";
    } else {
        $senderID = $_SESSION['users']['id'];
        $partnerID = htmlspecialchars($_POST['partnerID']);
        $commentText = htmlspecialchars($_POST['commentText']);
        $appendFile = null;

        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            if (!file_exists('File')) {
                mkdir('File');
            }
            $file = '../PersonalChat/File/' . basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
                $appendFile = $file;
            } else {
                echo "アップロードされたファイルの移動に失敗しました。";
            }
        }

        try {
            $sql = $pdo->prepare('INSERT INTO DirectMessage (userID, partnerID, commentText, appendFile) VALUES (?, ?, ?, ?)');
            $sql->execute([$senderID, $partnerID, $commentText, $appendFile]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // 重複エラーコード
                echo "<script>alert('ここでは一度しかこのユーザーにメッセージを送ることができません。\\nTOPページからサイドバーにある上から2番目のChatからお願いします');
                      window.location.href='OtherProfile.php?userID=$otherUserID';
                      </script>";
                exit();
            } else {
                echo 'エラーが発生しました: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/OtherProfile.css" type="text/css" />
    <link rel="icon" href="../image/SiteIcon.svg" type="image/svg">
    <title><?= htmlspecialchars($user['nickname']) ?>さんのプロフィール | Yadi-X</title>
    <script>
        function triggerFileInput() {
            document.getElementById('file-input').click();
        }

        function displayFileName(input) {
            const file = input.files[0];
            const commentText = document.getElementById('commentText');
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const deleteButton = document.getElementById('delete-button');

            if (file) {
                commentText.value = file.name;
                fileName.textContent = file.name;
                deleteButton.style.display = 'block';

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        filePreview.src = e.target.result;
                        filePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    filePreview.style.display = 'none';
                    filePreview.src = '';
                }
            } else {
                commentText.value = '';
                filePreview.style.display = 'none';
                filePreview.src = '';
                fileName.textContent = '';
                deleteButton.style.display = 'none';
            }
        }

        function removeFile() {
            const fileInput = document.getElementById('file-input');
            const commentText = document.getElementById('commentText');
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const deleteButton = document.getElementById('delete-button');

            fileInput.value = '';
            commentText.value = '';
            filePreview.style.display = 'none';
            filePreview.src = '';
            fileName.textContent = '';
            deleteButton.style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-image">
                <?php
                if (!empty($user['profileIcon'])) {
                    echo '<img src="'. htmlspecialchars($user['profileIcon']) . '" alt="Profile Image">';
                } else {
                    echo '<img src="../image/DefaultIcon.svg" alt="Profile Image">';
                }
                ?>
            </div>
            <div class="profile-info">
                <h2><?= htmlspecialchars($user['nickname']) ?></h2>
                <p>#<?= htmlspecialchars($user['userID']) ?></p>
            </div>
        </div>
        <h3>ダイレクトメッセージ</h3>
        <div class="direct-message">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="partnerID" value="<?= htmlspecialchars($user['userID']) ?>">
                <div class="input-wrapper">
                    <input type="text" id="commentText" name="commentText" placeholder="ダイレクトメッセージ">
                    <button type="button" onclick="triggerFileInput()" class="upload-icon"><img src="../image/FileIcon.svg" alt="ファイルアップロード"></button>
                    <button type="submit" name="directMessage" class="send-button"><img src="../image/SendIcon.svg" alt="送信"></button>
                </div>
                <div>
                    <img src="../Image/DeleteIcon.svg" id="delete-button" alt="削除" onclick="removeFile()">
                    <img id="file-preview" alt="File Preview">
                    <span id="file-name"></span>
                </div>
                <input type="file" id="file-input" name="file" style="display:none;" onchange="displayFileName(this)">
            </form>
        </div>
        <div class="profile-comments">
            <h3>最近投稿したもの</h3>
            <?php
            $comments = $pdo->prepare('SELECT * FROM GlobalChat WHERE userID = ? ORDER BY commentID DESC');
            $comments->execute([$otherUserID]);
            foreach ($comments as $comment) {
                $commentUserID = $comment['userID'];
                $userStmt = $pdo->prepare('SELECT * FROM Users WHERE userID = ?');
                $userStmt->execute([$commentUserID]);
                $commentUser = $userStmt->fetch(PDO::FETCH_ASSOC);

                $commentID = $comment['commentID'];
                $rply = $pdo->prepare('SELECT COUNT(*) as rplyCount FROM GlobalChat WHERE replyID = ?');
                $rply->execute([$commentID]);
                $rplya = $rply->fetch(PDO::FETCH_ASSOC);
                $rplyCount = $rplya['rplyCount'];
                
                echo '<div class="comment">';
                echo '<div class="comment-user">';
                if (!empty($commentUser['profileIcon'])) {
                    echo '<img src="'. htmlspecialchars($commentUser['profileIcon']) . '" alt="User Image" class="comment-user-image">';
                } else {
                    echo '<img src="../image/DefaultIcon.svg" alt="User Image" class="comment-user-image">';
                }
                echo '<p>' . htmlspecialchars($commentUser['nickname']) . '</p>';
                echo '</div>';
                echo '<a href="../Top/Globalrply.php?commentID=' . htmlspecialchars($commentID) . '" class="linkrply atag">';
                echo '<p>' . htmlspecialchars($comment['commentText']) . '</p>';
                echo '</a>';
                echo '<div class="rply">';
                echo '<img src="../image/RplyMark.svg" alt="rply" height="20" width="20">';
                echo '<div class="balloon3-left">';
                echo '<p>' . $rplyCount . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
