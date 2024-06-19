<?php
session_start();
require '../db-connect.php';

function uploadImage($file) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetFile = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // 画像ファイルが本物かどうかをチェック
    /*
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return [false, "ファイルは画像ではありません。"];
    }
    */

    // 許可されたファイル形式をチェック
    /*
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedTypes)) {
        return [false, "申し訳ありませんが、JPG、JPEG、PNG、GIFファイルのみ許可されています。"];
    }
    */

    // 画像がアップロードされていない場合、デフォルトの画像を設定
    if ($file['error'] == UPLOAD_ERR_NO_FILE) {
        $defaultImage = 'defaultGroupIcon.svg';//デフォルトの画像
        return [true, $defaultImage];
    }

    $targetFile = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

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

    if (empty($groupName)) {
        $errorMsg = "グループ名を入力してください";
    } else if (empty($adminId)) {
        $errorMsg = "ユーザーIDを入力してください";
    } else {
        list($uploadOk, $uploadMsg) = uploadImage($_FILES["groupIcon"]);
        if ($uploadOk) {
            $resultMsg = insertGroupData($pdo, $groupName, $adminId, $uploadMsg);
            // グループ作成が成功したら、トップ画面に遷移
            header("Location: ../Top/TopPage.php");
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

            <?php if (isset($errorMsg)): ?>
                <p class="error"><?= htmlspecialchars($errorMsg) ?></p>
            <?php elseif (isset($resultMsg)): ?>
                <p class="success"><?= htmlspecialchars($resultMsg) ?></p>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">
                <p class="group-name">グループ名<br>
                    <input class="group-input" type="text" name="groupName" required>
                </p>

                <p class="group-icon">
                    <label for="groupIcon" class="file-label">グループのアイコンを設定</label>
                    <input id="groupIcon" type="file" name="groupIcon" accept="image/*" onchange="previewImage(event)">
                </p>

                <img id="preview" class="circle-icon" style="display:none;">

                <?php
                $userId = '';
                if (isset($_SESSION['users'])) {
                    $userId = $_SESSION['users']['id'];
                }
                ?>
                <p class="group-userId">管理者のユーザーIDを入力<br>
                    <input class="group-input" type="text" name="adminId" value="<?= htmlspecialchars($userId) ?>">
                </p>

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
                output.style.display = 'block';
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>