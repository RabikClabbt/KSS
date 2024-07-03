<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php
    $pdo=new PDO($connect, user, pass);
    if(isset($_SESSION['users'])){
        $ID=$_SESSION['users']['userID'];
        $sql=$pdo->prepare('select * from Users where userID != ? and password=?');
        $sql->execute([$ID, $_POST['password']]);
    }else{
        $sql=$pdo->prepare('select * from Users where userID=?');
        $sql->execute([$_POST['password']]);
    }

    if(empty($sql->fetchAll())){
        $pass = password_hash($_POST['password'],PASSWORD_DEFAULT);
        if(isset($_SESSION['users'])){
            $sql=$pdo->prepare('update Users set userID=?,mailaddress=?,password=?');
            $sql->execute([
                $_POST['userID'],$_POST['mailaddress'],$_POST['password']]);
            $_SESSION['users']=[
                'userID'=>$ID,'mailaddress'=>$_POST['mailaddress'], 'password'=>$pass
                ];
            echo 'お客様情報を更新しました。';
        }else{
            $sql=$pdo->prepare('insert into Users (userID, mailaddress, password) VALUES (?, ?, ?)');
            $sql->execute([
                $_POST['userID'],$_POST['mailaddress'],
                $pass]);
            echo 'お客様情報を登録しました。';
            echo '<a href="login-in.php">ログイン</a>';
        }
    }else{
        echo 'ログイン名がすでに使用されています';
    }
?>