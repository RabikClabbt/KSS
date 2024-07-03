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
    <script>
        function triggerFileInput() {
            document.getElementById('file-input').click();
        }

        function displayFileName(input) {
            const file = input.files[0];
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const deleteButton = document.getElementById('delete-button');
            const textInput = document.querySelector('.text'); // テキストボックスを取得

            if (file) {
                fileName.textContent = file.name;
                deleteButton.style.display = 'block';
                textInput.style.width = 'calc(100% - 260px)'; // テキストボックスの幅を調整

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        filePreview.src = e.target.result;
                        filePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    filePreview.style.display = 'none';
                    filePreview.src = '';
                }
            } else {
                filePreview.style.display = 'none';
                filePreview.src = '';
                fileName.textContent = '';
                deleteButton.style.display = 'none';
                textInput.style.width = 'calc(100% - 200px)'; // テキストボックスの幅を元に戻す
            }
        }

        function removeFile() {
            const fileInput = document.getElementById('file-input');
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const deleteButton = document.getElementById('delete-button');
            const textInput = document.querySelector('.text'); // テキストボックスを取得

            fileInput.value = '';
            filePreview.style.display = 'none';
            filePreview.src = '';
            fileName.textContent = '';
            deleteButton.style.display = 'none';
            textInput.style.width = 'calc(100% - 200px)'; // テキストボックスの幅を元に戻す
        } 
    </script>
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
                            echo '<img src="./image/DefaultIcon.svg" alt="profileIcon" width="20" height="20">';
                            echo '<br>';
                            //ファイルがあれば表示する
                            if($row['appendFile']!='NULL'){
                            echo $row['appendFile'];
                            echo '<br>';
                            }
                            echo $row['commentText'];
                            echo '</div>';
                            echo '<br>';
                        } else {
                            echo '<div class="partner">';
                            echo '<img src="./image/DefaultIcon.svg" alt="profileIcon" width="20" height="20">';
                            echo $row['userID'];
                            echo '<br>';
                            //ファイルがあれば表示する
                            if($row['appendFile']!='NULL'){
                            echo $row['appendFile'];
                            echo '<br>';
                            }
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
                    
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        if (isset($_POST['chat'])) {
                            $chat = $_POST['chat'];
                            $uploadedFile = $_FILES['file'];
                    
                            if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
                                $uploadDir = 'uploads/';
                                $uploadFilePath = $uploadDir . basename($uploadedFile['name']);
                    
                                if (move_uploaded_file($uploadedFile['tmp_name'], $uploadFilePath)) {
                                    // ファイルアップロード成功
                                    $filePath = $uploadFilePath;
                                } else {
                                    // ファイルアップロード失敗
                                    $filePath = 'default';
                                }
                            } else {
                                // ファイルがアップロードされなかった場合
                                $filePath = 'NULL';
                            }
                    //チャットの保存
                    $icstr = 'insert into DirectMessage values (?,?,?,?,?)';
                    $insert = $pdo->prepare($icstr);
                    $Array[0] = $userID;
                    $Array[1] = $partnerID;
                    $Array[2] = $count;
                    $Array[3] = $_POST['chat'];
                    $Array[4] = $filePath;
                    $insert->execute($Array);
                    $count += 1;

                    header("Location: PersonalChat.php");
                    exit();
                        }
                    }
                }
                //相手が見つからない場合
                else if($msql->rowCount()==0){
                        echo '送信する相手が見つかりませんでした。';
                }
            ?>
            <form action="PersonalChat.php" method="post" enctype="multipart/form-data">
                <div class="Cfunction">
                    <input type="textarea" name="chat" value="" class="text" required>
                    <button type="button" onclick="triggerFileInput()" class="upload-icon">
                        <img src="./image/file-icon.png">
                    </button>
                    <input type="file" id="file-input" name="file" style="display: none;" onchange="displayFileName(this)">
                    <div id="file-preview-container">
                        <img id="file-preview" style="display: none;" />
                        <span id="file-name"></span>
                        <img src="./image/delete-box.png" id="delete-button" onclick="removeFile()" alt="削除">
                    </div>
                    <button type="submit" class="button">
                        <img src="./image/send-icon.svg" alt="送信">
                    </button>
                </div>
            </form>
            </div>
            <div class="space">
                <h2>知り合い</h2>
            <?php
            //自身のユーザーIDがpartnerIDとして登録されているものを探す(SQL)
                $pstr='select * from Users inner join DirectMessage on Users.userID=DirectMessage.userID where ';
                $pstr = $pstr.'partnerID = ?';
                $pkeyArray = array($userID);
                $psql=$pdo->prepare($pstr);
                $psql->execute($pkeyArray);
            //結果を表示する
            if($psql->rowCount()>0){
                foreach ($psql as $row) {
                    echo '<div class="friend">';
                    echo '<img src="./image/DefaultIcon.svg" alt="profileIcon" width="20" height="20">';
                    echo $row['userID'];
                    echo '</div>';
                }
            }
            else{
                echo '<div class="friend">';
                echo '見つかりませんでした。';
                echo '</div>';
            }
            ?>
            </div>
        </div>
</body>
</html>

<?php
ob_end_flush();
?>
