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

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>プロフィール画面</title>
    <link rel="stylesheet" href="../css/Profile.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($user['icon']); ?>" alt="Profile Icon">
            <h1><?php echo htmlspecialchars($user['name']); ?></h1>
            <p>#<?php echo htmlspecialchars($user['id']); ?></p>
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
                echo '<p>' . htmlspecialchars($comment['commentText']) . '</p>';
                echo '<div class="comment-reactions">';
                echo '<button>👍</button>';
                echo '<button>😂</button>';
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
            <label>プロフィールアイコン: <input type="file" name="profileIcon"></label><br>
            <img src="<?php echo htmlspecialchars($user['icon']); ?>" alt="Current Profile Icon" class="current-icon"><br>
            <button type="submit">保存</button>
            <button type="button" onclick="closeProfileEditPopup()">キャンセル</button>
        </form>
    </div>

    <script>
        function openProfileEditPopup() {
            document.getElementById('profileEditPopup').style.display = 'block';
        }

        function closeProfileEditPopup() {
            document.getElementById('profileEditPopup').style.display = 'none';
        }
    </script>
</body>
</html>
