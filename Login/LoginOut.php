<?php
session_start();

require '../db-connect.php';

unset($_SESSION['users']);
$pdo = new PDO($connect , user , pass);

//ユーザー情報の取得
$sql = $pdo->prepare('select * from Users where userID=? OR mailaddress=?');
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

    // GroupUserテーブルからuserIDに一致するgroupIDの取得
    $sql = $pdo->prepare('SELECT groupID FROM GroupUser WHERE userID = ?');
    $sql->execute([$row['userID']]);
    $groupRow = $sql->fetch();
    
    if ($groupRow) {
        // groupIDをセッションに保存
        $_SESSION['groupID'] = $groupRow['groupID'];
    }
    
    header('Location: ../GroupChat/GroupChatIndex.php');
    exit;
} else {
    header('Location: LoginAgain.php');
    exit;
}
?>