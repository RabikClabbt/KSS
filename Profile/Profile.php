<?php
session_start();
require '../src/db-connect.php';
require '../Header/Header.php';

if (!isset($_SESSION['users'])) {
    echo "<script>
            alert('ログインしてください');
            window.location.href = '../Login/LoginIn.php';
          </script>";
    exit;
}

$user = $_SESSION['users'];
$pdo = new PDO($connect, user, pass);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <link rel="icon" href="../image/SendIcon.svg" type="image/svg+xml">
    <title><?= $user['name'] ?> (#<?= $user['id'] ?>) さん | Yadi-X</title>
    <link rel="stylesheet" type="text/css" href="./css/Profile.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <?php if (!empty($user['icon'])) { ?>
                <img src="<?= $user['icon'] ?>" alt="profileIcon">
            <?php } else { ?>
                <img src="../image/DefaultIcon.svg" alt="profileIcon">
            <?php } ?>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <p>#<?php echo htmlspecialchars($user['id']); ?></p>
            </div>
        </div>
        <div class="profile-buttons">
            <button onclick="openProfileEditPopup()">プロフィール情報の変更</button>
            <button onclick="location.href='UserInfoEdit.php'">ユーザー情報の変更</button>
        </div>
        <div class="profile-comments">
            <h2>最近投稿したもの</h2>
            <?php
            $comments = $pdo->prepare('SELECT * FROM GlobalChat WHERE userID = ? ORDER BY commentID DESC');
            $comments->execute([$user['id']]);
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

    <div id="profileEditPopup" class="popup">
        <form action="SaveProfile.php" method="post" enctype="multipart/form-data">
            <h2>プロフィール情報の変更</h2>
            <label>ニックネーム <input type="text" name="nickname" value="<?php echo htmlspecialchars($user['name']); ?>"></label><br>
            <label>プロフィールアイコン <input type="file" name="profileIcon" onchange="previewImage(event)"></label><br>
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
                window.location.href = 'LogOut.php';
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
