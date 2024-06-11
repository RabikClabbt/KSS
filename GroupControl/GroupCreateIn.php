<?php session_start(); ?>
 
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
 
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $groupName = $_POST['groupName'];
            $adminId = $_POST['adminId'];
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($_FILES["groupIcon"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
 
            // 画像ファイルが本物かどうかをチェック
            $check = getimagesize($_FILES["groupIcon"]["tmp_name"]);
            if ($check !== false) {
                echo "ファイルは画像です - " . $check["mime"] . ".<br>";
                $uploadOk = 1;
            } else {
                echo "ファイルは画像ではありません。<br>";
                $uploadOk = 0;
            }
 
            // 許可されたファイル形式をチェック
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "申し訳ありませんが、JPG、JPEG、PNG、GIFファイルのみ許可されています。<br>";
                $uploadOk = 0;
            }
 
            // エラーチェック後、ファイルをアップロード
            if ($uploadOk == 0) {
                echo "申し訳ありませんが、ファイルはアップロードされませんでした。<br>";
            } else {
                if (move_uploaded_file($_FILES["groupIcon"]["tmp_name"], $targetFile)) {
                    echo "ファイル ". htmlspecialchars(basename($_FILES["groupIcon"]["name"])) . " がアップロードされました。<br>";
                    echo '<img src="' . htmlspecialchars($targetFile) . '" class="circle-icon">';
                    // グループデータをデータベースに保存する。$groupName、$adminId、$targetFile（アイコンのパス）を使用する
                    // データベース接続と挿入ロジックをここに記述
                } else {
                    echo "申し訳ありませんが、ファイルのアップロード中にエラーが発生しました。<br>";
                }
            }
        }
        ?>
 
        <form action="GroupCreateOut.php" method="post" enctype="multipart/form-data">
        <p class="group-name">グループ名<br>
            <input class="group-input" type="text" name="groupName" required>
        </p>
 
        <p class="group-icon">
            <label for="groupIcon" class="file-label">グループのアイコンを設定</label>
            <input id="groupIcon" type="file" name="groupIcon" accept="image/*" required onchange="previewImage(event)">
        </p>
 
        <img id="preview" class="circle-icon">
 
        <?php
        $userId = '';
        if(isset($_SESSION['users'])){
            $userId = $_SESSION['users']['id'];
        }
        echo '<p class="group-userId">管理者のユーザーIDを入力<br>
                  <input class="group-input" type="text" name="adminId" value="', $userId ,'">
              </p>';
        ?>
 
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