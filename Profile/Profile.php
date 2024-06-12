<?php
session_start();
require '../db-connect.php';
require '../Header/Header.php';

if (!isset($_SESSION['users'])) {
    header('Location: Login.php');
    exit;
}

$user = $_SESSION['users'];
$pdo = new PDO($connect, user, pass);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>プロフィール画面プレビュー</title>
    <link rel="stylesheet" href="../css/Profile.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($user['icon']); ?>" alt="Profile Icon">
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <p>#<?php echo htmlspecialchars($user['id']); ?></p>
            </div>
        </div>
        <div class="profile-buttons">
            <button onclick="openProfileEditPopup()">プロフィール情報の変更</button>
            <button onclick="location.href='UserInfoEdit.php'">ユーザー情報の変更</button>
        </div>
        <div class="comments-section">
            <h2>最近投稿したもの</h2>
            <?php
            $sql = $pdo->prepare('SELECT * FROM GlobalChat WHERE userID = ?');
            $sql->execute([$user['id']]);
            while ($comment = $sql->fetch()) {
                echo '<div class="comment">';
                echo '<div class="account-image">';
                if (!empty($user['icon'])) {
                    echo '<img src="' . htmlspecialchars($user['icon']) . '" alt="Profile Icon">';
                } else {
                    echo '<img src="../image/DefaultIcon.svg" alt="ProfileImage">';
                }
                echo '</div>';
                echo '<div class="comment-content">';
                echo '<div class="comment-header"> <!-- 追加 -->';
                echo '<p class="nickname">' . htmlspecialchars($user['name']) . '</p>';
                echo '</div>';
                echo '<div class="comment-text">';
                echo '<p>' . htmlspecialchars($comment['commentText']) . '</p>';
                echo '</div>';
                echo '<div class="comment-reactions">';
                echo '<button>👍</button>';
                echo '<button>😂</button>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div id="profileEditPopup" class="popup">
        <form action="SaveProfile.php" method="post" enctype="multipart/form-data">
            <h2>プロフィール情報の変更</h2>
            <label>ニックネーム: <input type="text" name="nickname" value="<?php echo htmlspecialchars($user['name']); ?>"></label><br>
            <label>プロフィールアイコン: <input type="file" name="profileIcon" onchange="previewImage(event)"></label><br>
            <img id="preview" src="<?php echo htmlspecialchars($user['icon']); ?>" alt="Current Profile Icon" class="current-icon"><br>
            <button type="submit">保存</button>
            <button type="button" onclick="closeProfileEditPopup()">キャンセル</button>
        </form>
    </div>

    <button class="logout-button" onclick="confirmLogout()">ログアウト</button>

    <script>
        function openProfileEditPopup() {
            document.getElementById('profileEditPopup').style.display = 'block';
        }

        function closeProfileEditPopup() {
            document.getElementById('profileEditPopup').style.display = 'none';
        }

        function confirmLogout() {
            if (confirm('本当にログアウトしますか？')) {
                window.location.href = 'logout.php';
            }
        }

        function previewImage(event) {
            const preview = document.getElementById('preview');
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function() {
                preview.src = reader.result;
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
