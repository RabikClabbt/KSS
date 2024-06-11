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
        <!-- Header.phpを読み込む -->
        <include src="../Header/Header.php"></include>
    </header>
    <main>
        <h1>グループを作成</h1>

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
                echo "ファイルは画像です - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "ファイルは画像ではありません。";
                $uploadOk = 0;
            }

            // 許可されたファイル形式をチェック
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "申し訳ありませんが、JPG、JPEG、PNG、GIFファイルのみ許可されています。";
                $uploadOk = 0;
            }

            // エラーチェック後、ファイルをアップロード
            if ($uploadOk == 0) {
                echo "申し訳ありませんが、ファイルはアップロードされませんでした。";
            } else {
                if (move_uploaded_file($_FILES["groupIcon"]["tmp_name"], $targetFile)) {
                    echo "ファイル ". htmlspecialchars(basename($_FILES["groupIcon"]["name"])) . " がアップロードされました。";
                    // グループデータをデータベースに保存する。$groupName、$adminId、$targetFile（アイコンのパス）を使用する
                    // データベース接続と挿入ロジックをここに記述
                } else {
                    echo "申し訳ありませんが、ファイルのアップロード中にエラーが発生しました。";
                }
            }
        }
        ?>

        <form action="" method="post" enctype="multipart/form-data">
        <p>グループ名<br>
            <input type="text" name="groupName" required>
        </p>

        <p>グループのアイコンを設定する<br>
            <input type="file" name="groupIcon" accept="image/*" required>
        </p>

        <?php
        $userId = '';
        if(isset($_SESSION['users'])){
            $userId = $_SESSION['users']['id'];
        }
        echo '<p>管理者のユーザーIDを入力<br>
                  <input type="text" name="adminId" value="', $userId ,'">
              </p>';
        ?>

        <button type="submit">作成する</button>
        </form>
    </main>
    <footer>
          
    </footer>
</body>
</html>