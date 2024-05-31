<?php
session_start();
require '../db-connect.php';

if (!isset($_SESSION['users'])) {
    header('Location: Login.php');
    exit;
}

$user = $_SESSION['users'];
$pdo = new PDO($connect , user , pass);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = $_POST['nickname'];
    $profileIcon = $user['icon'];

    if (isset($_FILES['profileIcon']) && $_FILES['profileIcon']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['profileIcon']['name']);
        if (move_uploaded_file($_FILES['profileIcon']['tmp_name'], $uploadFile)) {
            $profileIcon = $uploadFile;
        }
    }

    $sql = $pdo->prepare('UPDATE Users SET nickname = ?, profileIcon = ? WHERE userID = ?');
    $sql->execute([$nickname, $profileIcon, $user['id']]);

    $_SESSION['users']['name'] = $nickname;
    $_SESSION['users']['icon'] = $profileIcon;

    header('Location: Profile.php');
}
?>
