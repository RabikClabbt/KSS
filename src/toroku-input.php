<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規会員登録</title>
</head>

<body>
    <h1>アカウント作成</h1>
    <?php
    $userID=$mailaddress=$password='';
    if(isset($_SESSION['users'])){
        $userID=$_SESSION['users']['userID'];
        $mailaddress=$_SESSION['users']['mailaddress'];
        $password=$_SESSION['users']['password'];
    }
    ?>
    <?php
       echo '<form action="toroku-output.php" method="post">';
    echo '<p>';
    echo '<h4><label for="signin-ID">ユーザーID</label></h4>';
    echo '<input id="signin-id" type="text" required name="userID" value="',$userID,'">';
    echo '</p>';
    echo '<p>';
    echo '<h4><label for="signin-pass">メールアドレス</label></h4>';
    echo '<input id="signin-pass" type="text" required name="mailaddress" value="',$mailaddress,'">';
    echo '</p>';
    echo '<p>';
    echo '<h4><label for="signin-name">パスワード</label></h4>';
    echo '<input type="text" name="password" required name="password" value="',$password,'">';
    echo '<a href="login.php">アカウント作成済み</a>';
    echo '</p>';
    echo '<br>';
    echo ' <p><button name="touroku-button"type="submit">作成</button></p>';
    ?>
</body>

</html