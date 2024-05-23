<?php session_start(); ?>
<?php require 'db-connect.php' ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel ="stylesheet" href = "css/Search.css">
    <title>検索</title>
</head>
<body>
<form action="Search.php" method="post">
<div class="Sfunction">
<div>
<input type="text" placeholder="検索" style="width: 450px;height:20px;" name="search" class="text">
</div>
<div>
<input type="submit" value="検索" class="button">
</div>
</div>
</form>
<?php
    $pdo=new PDO($connect,USER,PASS);
    $ustr='select * from Users where ';
    $qstr='select * from Question where ';
    echo '<div class="result">';
    echo 'ユーザー';
    //ユーザー名が入力されている
    if(isset($_POST['search'])){
        $ustr = $ustr.'userID like ?';
        $ukeyArray[0]='%'.$_POST['search'].'%';
        $sql=$pdo->prepare($ustr);
        $sql->execute($ukeyArray);
    }
    //初期状態またはユーザー名が入力されていない(全件表示)
    else{
            echo '<br>';
        $sql = $pdo->query('select * from Users');
        foreach($sql as $row){
            echo '<img src="image/icon.png" width="30" height="30">';
            echo '<br>';
            echo $row['userID'];
            echo '<br>';
            echo $row['mailaddress'];
            echo '<br>';
        }
    }
    //入力されていて、一致するものがあった場合
    if(isset($_POST['search']) && $sql->rowCount()>0){
            echo '<br>';
        foreach($sql as $row){
            echo '<img src="image/icon.png" width="30" height="30">';
            echo '<br>';
            echo $row['userID'];
            echo '<br>';
            echo $row['mailaddress'];
            echo '<br>';
        }
    }
    //入力されているが、一致するものが見つからなかった場合 全件表示する
    else if(isset($_POST['search']) && $sql->rowCount()==0){
        echo '<br>';
        echo '一致するものがありませんでした。';
    }
    echo '<br>';
    echo '質問';
    //質問名が入力されている
    if(isset($_POST['search'])){
        $qstr = $qstr.'userID like ?';
        $qkeyArray[0]='%'.$_POST['search'].'%';
        $sql=$pdo->prepare($qstr);
        $sql->execute($qkeyArray);
    }
    //初期状態または質問名が入力されていない(全件表示)
    else{
        $sql = $pdo->query('select * from Question');
            echo '<br>';
        foreach($sql as $row){
            echo '<img src="image/icon.png" width="30" height="30">';
            echo '<br>';
            echo $row['userID'];
            echo '<br>';
        }
    }
    //入力されていて、一致するものがあった場合
    if(isset($_POST['search']) && $sql->rowCount()>0){
            echo '<br>';
        foreach($sql as $row){
            echo '<img src="image/icon.png" width="30" height="30">';
            echo '<br>';
            echo $row['userID'];
            echo '<br>';
        }
    }
    //入力されているが、一致するものが見つからなかった場合 全件表示する
    else if(isset($_POST['search']) && $sql->rowCount()==0){
        echo '<br>';
        echo '一致するものがありませんでした。';
    }
    echo '</div>';
?>
</body>
</html>