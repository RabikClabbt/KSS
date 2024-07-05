<?php
session_start();
require '../db-connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = new PDO($connect, user, pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetFile = $targetDir . basename($_FILES["groupIcon"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // 画像ファイルが本物かどうかをチェック
        $check = getimagesize($_FILES["groupIcon"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo '<p>ファイルは画像ではありません。</p>';
            $uploadOk = 0;
        }

        // 許可されたファイル形式をチェック
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo '<p>申し訳ありませんが、JPG、JPEG、PNG、GIFファイルのみ許可されています。</p>';
            $uploadOk = 0;
        }

        // エラーチェック後、ファイルをアップロード
        if ($uploadOk == 0) {
            echo '<p>申し訳ありませんが、ファイルはアップロードされませんでした。</p>';
        } else {
            if (move_uploaded_file($_FILES["groupIcon"]["tmp_name"], $targetFile)) {
                // グループデータの挿入
                $sql = $pdo->prepare('INSERT INTO `Group` (`groupName`, `groupIcon`, `admin`) VALUES (?, ?, ?)');
                if (empty($_POST['groupName'])) {
                    echo '<p>グループ名を入力してください</p>';
                    header('Location: ../GroupControl/GroupCreateIn.php');
                    exit;
                } else if (empty($_POST['adminId'])) {
                    echo '<p>ユーザーIDを入力してください</p>';
                    header('Location: ../GroupControl/GroupCreateIn.php');
                    exit;
                } else if ($sql->execute([$_POST['groupName'], $targetFile, $_POST['adminId']])) {
                    echo 'グループが正常に作成されました。';
                } else {
                    echo 'グループ作成中にエラーが発生しました。';
                }
            } else {
                echo '<p>申し訳ありませんが、ファイルのアップロード中にエラーが発生しました。</p>';
            }
        }
    } catch (PDOException $e) {
        echo 'データベースエラー: ' . $e->getMessage();
    }
}
?>