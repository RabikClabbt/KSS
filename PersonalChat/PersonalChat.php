<?php
session_start();
ob_start(); // ここで出力バッファリングを開始する
require 'db-connect.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/chat.css">
    <title>個人チャット画面</title>
</head>
<body>
    <header>
        <?php require 'Header.php'; ?>
    </header>
        <div class="chat-all">
        <div class="chat-history">
        <?php
            //コメントID記録用
            $count = 0;

            //データベース接続用
            $pdo=new PDO($connect,USER,PASS);
            $mstr='select * from Users inner join DirectMessage on Users.userID=DirectMessage.userID where ';

            //連絡する相手の確認
            $userID = 'user'; // 自身のユーザーID
            $partnerID = 'user2'; // 相手側のユーザーID($_GETで受け取る)

            //送信相手の確認
            $mstr = $mstr.'partnerID = ?';
            $mkeyArray = array($partnerID);
            $msql=$pdo->prepare($mstr);
            $msql->execute($mkeyArray);

                // 自身と相手のメッセージを時系列で取得
            $sql = 'select * from DirectMessage where (userID = ? AND partnerID = ?) or (userID = ? AND partnerID = ?) order by commentID ASC';
            $keyArray = array($userID, $partnerID, $partnerID, $userID);
            $history = $pdo->prepare($sql);
            $history->execute($keyArray);

            //チャット履歴を表示(commentIDの昇順)
            if ($history->rowCount()>=1) {
                foreach ($history as $row) {
                    if($userID==$row['userID']){
                        echo '<div class="my">';
                        echo $row['userID'];
                        echo '<br>';
                        echo $row['commentText'];
                        echo '</div>';
                        echo '<br>';
                    } else {
                        echo '<div class="partner">';
                        echo $row['userID'];
                        echo '<br>';
                        echo $row['commentText'];
                        echo '</div>';
                        echo '<br>';
                    }
                        echo '<br>';
                        $count = $row['commentID'];
                }
            } else {
                echo 'チャット履歴がありません。';
            }

                //登録するcommentIDの値を格納
                $count += 1;

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
                $count += 1;

                header("Location: PersonalChat.php");
                exit();
            }
            //相手が見つからない場合
            else if($msql->rowCount()==0){
                    echo '送信する相手が見つかりませんでした。';
            }
        ?>
            <form action="PersonalChat.php" method="post">
                <div class="Cfunction">
                    <input type="textarea" name="chat" value="" class="text" required>
                    <button type="submit" class="button">
                        <img src="./image/send-icon.svg" alt="送信">
                    </button>
                </div>
            </form>
        </div>
    <div class="space">
        <p>知り合いの表示</p>
    </div>
    </div>
</body>
</html>

<?php
ob_end_flush();
?>
