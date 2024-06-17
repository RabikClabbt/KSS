<?php
session_start();
require '../db-connect.php';

// $pdoを定義する
$pdo = new PDO($connect, user, pass);

if (!isset($_SESSION['users'])) {
    header('Location: Login.php');
    exit;
}

$userID = $_SESSION['users']['id'];
$nickname = isset($_POST['nickname']) ? $_POST['nickname'] : $_SESSION['users']['name'];

// プロフィール画像がアップロードされた場合の処理
if (isset($_FILES['profileIcon']) && $_FILES['profileIcon']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profileIcon']['tmp_name'];
    $fileName = $userID . '.jpg'; // ユーザーIDをファイル名として使用
    $uploadFileDir = '../Profile/uploads/'; // アップロードディレクトリ
    $destPath = $uploadFileDir . $fileName;

    // ファイルを移動
    if(move_uploaded_file($fileTmpPath, $destPath)) {
        // ファイルが正常にアップロードされた場合の処理
        $profileIcon = $destPath;
    } else {
        // エラーメッセージ
        echo 'ファイルのアップロード中にエラーが発生しました。';
        exit;
    }
} else {
    // プロフィールアイコンがアップロードされていない場合、既存のアイコンを使用
    $profileIcon = $_SESSION['users']['icon'];
}

// データベースを更新
$sql = $pdo->prepare('UPDATE Users SET nickname = ?, profileIcon = ? WHERE userID = ?');
$sql->execute([$nickname, $profileIcon, $userID]);

// セッションのユーザー情報を更新
$_SESSION['users']['name'] = $nickname;
$_SESSION['users']['icon'] = $profileIcon;

header('Location: Profile.php');
exit;
?>
