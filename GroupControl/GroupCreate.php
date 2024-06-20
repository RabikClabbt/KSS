<?php
session_start();
require '../db-connect.php';

function uploadImage($file) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetFile = $targetDir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // 画像がアップロードされていない場合、デフォルトの画像を設定
    if ($file['error'] == UPLOAD_ERR_NO_FILE) {
        $defaultImage = '../Image/defaultGroupIcon.svg'; // デフォルトの画像
        return [true, $defaultImage];
    }

    // ファイルをアップロード
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return [true, $targetFile];
    } else {
        return [false, "ファイルのアップロード中にエラーが発生しました。"];
    }
}

function insertGroupData($pdo, $groupName, $adminId, $groupIcon) {
    try {
        $sql = $pdo->prepare('INSERT INTO `Group` (`groupName`, `groupIcon`, `admin`) VALUES (?, ?, ?)');
        if ($sql->execute([$groupName, $groupIcon, $adminId])) {
            return "グループが正常に作成されました。";
        } else {
            return "グループ作成中にエラーが発生しました。";
        }
    } catch (PDOException $e) {
        return 'データベースエラー: ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo = new PDO($connect, user, pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $groupName = $_POST['groupName'];
    $adminId = $_POST['adminId'];

    $sql = $pdo->prepare('select * from Users where userID = ?');
    $sql->execute([$adminId]);
    $user = $sql->fetch();

    if (empty($groupName)) {
        $errorMsg = "グループ名を入力してください";
    } else if (empty($adminId)) {
        $errorMsg = "ユーザーIDを入力してください";
    } else if (!$user){
        $errorMsg = "入力したユーザーIDは存在しません";
    } else {
        list($uploadOk, $uploadMsg) = uploadImage($_FILES["groupIcon"]);
        if ($uploadOk) {
            $resultMsg = insertGroupData($pdo, $groupName, $adminId, $uploadMsg);
            // グループ作成が成功したら、グループ管理画面に遷移
            header("Location: ./GroupControl.php");
            exit();
        } else {
            $errorMsg = $uploadMsg;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>グループ作成画面</title>
    <link rel="stylesheet" type="text/css" href="css/GroupCre.css">
</head>
<body>
    <header>
        <include src="../Header/Header.php"></include>
    </header>
    <main>
        <div class="group-container">
            <h1 class="group-title">グループ作成</h1>

            <form action="" method="post" enctype="multipart/form-data">
                <p class="group-name">グループ名<br>
                    <input class="group-input" type="text" name="groupName" required>
                </p>

                <p class="group-icon">
                    <label for="groupIcon" class="file-label">グループのアイコンを設定</label>
                    <input id="groupIcon" type="file" name="groupIcon" accept="image/*" onchange="previewImage(event)">
                </p>

                <!-- 初期状態でデフォルトアイコンを表示 -->
                <img id="preview" class="circle-icon" src="../Image/defaultGroupIcon.svg" alt="グループアイコン">

                <?php
                $userId = '';
                if (isset($_SESSION['users'])) {
                    $userId = $_SESSION['users']['id'];
                }
                ?>
                <p class="group-userId">管理者のユーザーIDを入力<br>
                    <input class="group-input" type="text" name="adminId" value="<?= htmlspecialchars($userId) ?>">
                </p>

                <?php if (isset($errorMsg)): ?>
                    <p class="error"><?= htmlspecialchars($errorMsg) ?></p>
                <?php elseif (isset($resultMsg)): ?>
                    <p class="success"><?= htmlspecialchars($resultMsg) ?></p>
                <?php endif; ?>

                <p><button class="group-button" type="submit">作成する</button></p>
            </form>
        </div>
    </main>
    <footer>
    </footer>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>