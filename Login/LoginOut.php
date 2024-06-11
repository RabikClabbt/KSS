<?php
session_start();

require '../db-connect.php';

unset($_SESSION['users']);
$pdo = new PDO($connect , user , pass);
$sql = $pdo->prepare('select * from Users where userID=? || mailaddress=?');
$sql->execute([$_POST['mailid'],$_POST['mailid']]);
$row = $sql->fetch();

if ($row && password_verify($_POST['pass'], $row['password'])) {
    $_SESSION['users'] = [
        'id'   => $row['userID'],
        'mail' => $row['mailaddress'],
        'pass' => $row['password'],
        'name' => $row['nickname'],
        'icon' => $row['profileIcon']
    ];
    header('Location: ../GroupControl/GroupCreateIn.php');
    exit;
} else {
    header('Location: LoginAgain.php');
}
?>