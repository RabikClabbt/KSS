<?php require 'db-connect.php' ?>
<?php
    ob_start();
    //コメントID記録用
    $count = 0;

    //データベース接続用
    $pdo=new PDO($connect,USER,PASS);
    $mstr='select * from Users inner join DirectMessage on Users.userID=DirectMessage.userID where ';
    $pstr='select * from Users inner join DirectMessage on Users.userID=DirectMessage.userID where ';
    
    //連絡する相手の確認
    $userID = 'user';//自身のユーザーID
    $partnerID = 'user2';//相手側のユーザーID($_GETで受け取る)

    //自身のメッセージ表示用
    $mstr = $mstr.'partnerID = ? order by commentID ASC';
    $mkeyArray = array($partnerID);
    $msql=$pdo->prepare($mstr);
    $msql->execute($mkeyArray);

    //相手のメッセージ表示用
    $pstr = $pstr.'partnerID = ? order by commentID ASC';
    $pkeyArray = array($userID);
    $psql=$pdo->prepare($pstr);
    $psql->execute($pkeyArray);


    //自身のチャット履歴を表示(commentIDの昇順)
    if($msql->rowCount()>=1){
        foreach($msql as $row){
            echo $row['userID'];
            echo '<br>';
            echo $row['commentText'];
            echo '<br>';
            echo '<br>';
            $mcount = $row['commentID'];
        }
    }
    
    //相手のチャット履歴を表示(commentIDの昇順)
    if($psql->rowCount()>=1){
        foreach($psql as $row){
            echo $row['userID'];
            echo '<br>';
            echo $row['commentText'];
            echo '<br>';
            echo '<br>';
            $pcount = $row['commentID'];
        }
    }

    //登録するcommentIDの値を格納
    if($mcount>$pcount){
        $count = $mcount;
    }
    else{
        $count = $pcount;
    }

    //対象の相手にチャット内容を送信する
    if($msql->rowCount()>=1 && isset($_POST['chat'])){
        echo $userID;
        echo '<br>';
        echo $_POST['chat'];
        echo '<br>';
        echo '<br>';
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

        header("Location: PersonalChat.php");
        exit();
    }
    //相手が見つからない場合
    else if($msql->rowCount()==0){
        echo '送信する相手が見つかりませんでした。';
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel ="stylesheet" href = "css/chat.css">
    <title>個人チャット画面</title>
</head>
<body>
<form action="PersonalChat.php" method="post">
<input type="text" name="chat" id="clear" value="" style="width: 600px;height:20px;" class="text" required>
<input type="submit" value="送信" class="button">
</form>
</body>
</html>

<?php
// End output buffering and flush output
ob_end_flush();
?>
