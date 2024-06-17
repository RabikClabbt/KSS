<?php
session_start();
require 'db-connect.php';

try {
    $pdo = new PDO($connect, user, pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_POST['userID'] ?? null;
    $mailaddress = $_POST['mailaddress'] ?? null;
    $password = $_POST['password'] ?? null;

    if (empty($userID) || empty($mailaddress) || empty($password)) {
        $_SESSION['error_message'] = '全てのフィールドを入力してください。';
        header('Location: toroku.php');
        exit;
    }

    if (isset($_SESSION['users'])) {
        $ID = $_SESSION['users']['userID'];
        $sql = $pdo->prepare('SELECT * FROM Users WHERE userID != ? AND password = ?');
        $sql->execute([$ID, $password]);
    } else {
        $sql = $pdo->prepare('SELECT * FROM Users WHERE userID = ?');
        $sql->execute([$userID]);
    }

    if ($sql->rowCount() == 0) {
        $pass = password_hash($password, PASSWORD_DEFAULT);
        if (isset($_SESSION['users'])) {
            $sql = $pdo->prepare('UPDATE Users SET userID = ?, mailaddress = ?, password = ? WHERE userID = ?');
            $sql->execute([$userID, $mailaddress, $pass, $ID]);
            $_SESSION['users'] = [
                'userID' => $userID,
                'mailaddress' => $mailaddress,
                'password' => $pass
            ];
            $_SESSION['success_message'] = 'お客様情報を更新しました。';
        } else {
            $sql = $pdo->prepare('INSERT INTO Users (userID, mailaddress, password) VALUES (?, ?, ?)');
            $sql->execute([$userID, $mailaddress, $pass]);
            $_SESSION['success_message'] = 'お客様情報を登録しました。<a href="login.php">ログイン</a>';
        }
    } else {
        $_SESSION['error_message'] = 'ログイン名がすでに使用されています';
    }

    header('Location: toroku-input.php');
    exit;
}
?>
