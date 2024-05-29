<?php require 'db-connect.php' ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel ="stylesheet" href = "css/chat.css">
    <title>個人チャット画面</title>
</head>
<body>
<form action="PersonalChat.php" method="post">
<input type="text" name="chat" style="width: 600px;height:20px;" class="text" required>
<input type="submit" value="送信" class="button">
</form>
<?php
    //コメントID記録用
    $count = 1;

    //データベース接続用
    $pdo=new PDO($connect,USER,PASS);
    $cstr='select * from Users inner join DirectMessage on Users.userID=DirectMessage.userID where ';
    $acstr='select * from Users inner join DirectMessage on Users.userID=DirectMessage.userID';
    
    //連絡する相手の確認
    $userID = 'user';//自身のユーザーID
    $partnerID = 'user2';//相手側のユーザーID
    $cstr = $cstr.'partnerID = ?';
    $keyArray = array($partnerID);
    $sql=$pdo->prepare($cstr);
    $sql->execute($keyArray);

    //チャット履歴を表示(commentIDの昇順)
    if($sql->rowCount()==1){
        $acsql=$pdo->query($acstr);
        foreach($acsql as $row){
            echo '<img src="image/icon.png" width="30" height="30">';
            echo $row['commentText'];
            echo '<br>';
        }
    }

    //対象の相手にチャット内容を送信する
    if($sql->rowCount()==1 && isset($_POST['chat'])){
        echo '<img src="image/icon.png" width="30" height="30">';
        echo $_POST['chat'];
        //チャットの保存
        $icstr = 'insert into DirectMessage values (?,?,?,?,?)';
        $insert = $pdo->prepare($icstr);
        $Array[0] = $userID;
        $Array[1] = $partnerID;
        $Array[2] = $count;
        $Array[3] = $_POST['chat'];
        $Array[4] = 'default';
        $insert->execute($Array);
        $count+=1;
    }
    //相手が見つからない場合
    else if($sql->rowCount()==0){
        echo '送信する相手が見つかりませんでした。';
    }
?>
</body>
</html>