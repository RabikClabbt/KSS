<?php
session_start();
require '../db-connect.php';

if (!isset($_SESSION['users'])) {
    header('Location: Login.php');
    exit;
}

$user = $_SESSION['users'];
$pdo = new PDO($connect , user , pass);
?>

<?php require '../Header/Header.php'; ?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>プロフィール画面プレビュー</title>
    <link rel="stylesheet" href="../css/Profile.css"> <!-- 絶対パスに変更 -->
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($user['icon']); ?>" alt="Profile Icon" id="profile-icon">
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <p>#<?php echo htmlspecialchars($user['id']); ?></p>
            </div>
        </div>
        <div class="profile-buttons">
            <button class="custom-button" onclick="openProfileEditPopup()">プロフィール情報の変更</button>
            <button class="custom-button" onclick="location.href='UserInfoEdit.php'">ユーザー情報の変更</button>
        </div>
        <div class="comments-section">
            <h2>最近投稿したもの</h2>
            <?php
            $sql = $pdo->prepare('SELECT * FROM GlobalChat WHERE userID = ?');
            $sql->execute([$user['id']]);
            while ($comment = $sql->fetch()) {
                echo '<div class="comment">';
                echo '<img src="' . htmlspecialchars($user['icon']) . '" alt="Profile Icon">';
                echo '<div class="comment-text">';
                echo '<p>' . htmlspecialchars($comment['commentText']) . '</p>';
                echo '</div>';
                echo '<div class="comment-reactions">';
                echo '<button class="custom-button">👍</button>';
                echo '<button class="custom-button">😂</button>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div id="profileEditPopup" class="popup">
        <form action="SaveProfile.php" method="post" enctype="multipart/form-data">
            <h2>プロフィール情報の変更</h2>
            <label>ニックネーム: <input class="input-field" type="text" name="nickname" value="<?php echo htmlspecialchars($user['name']); ?>"></label><br>
            <label>プロフィールアイコン: <input class="input-field" type="file" name="profileIcon" onchange="previewImage(event)"></label><br>
            <img src="<?php echo htmlspecialchars($user['icon']); ?>" alt="Current Profile Icon" id="current-icon" class="current-icon"><br>
            <button class="custom-button" type="submit">保存</button>
            <button class="custom-button" type="button" onclick="closeProfileEditPopup()">キャンセル</button>
        </form>
    </div>

    <script>
        function openProfileEditPopup() {
            document.getElementById('profileEditPopup').style.display = 'block';
        }

        function closeProfileEditPopup() {
            document.getElementById('profileEditPopup').style.display = 'none';
        }

        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('current-icon');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
