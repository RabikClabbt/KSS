<?php
require 'db-connect.php';
$pdo = new PDO($connect, user, pass);
$icons = [
    ['url' => 'page1.php', 'label' =>'Q&A','img'=> 'image/Q&A.svg'],
    ['url' => 'page2.php', 'label' =>'chat','img'=> 'image/chat.svg'],
    ['url' => 'page3.php', 'label' =>'group-chat','img'=> 'image/group-chat.svg']
];
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $sql = $pdo->prepare('INSERT INTO GlobalChat (userID, commentText,appendFile ) VALUES (?, ?,?)');
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        if (!file_exists('File')) {
            if (!mkdir('File')) {
                die('Failed to create directory.');
            }
        }
        $file = './File/' . basename($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
            //ファイルが正常にアップロードされました
        } else {
            die('Failed to move uploaded file.');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/toppage.css" />
    <title>トップ画面</title>
    <script>
        function goToPage(url) {
            window.location.href = url;
        }

        function displayFileName(input) {
            const fileName = input.files[0]?.name || '';
            document.querySelector('.chat-text').value = fileName;
        }
    </script>
</head>
<body>
    <div class="headerr">
        <?php require 'Header.html'; ?>
    </div>
    <div class="content">
        <div class="sideber">
            <?php foreach ($icons as $icon): ?>
                <div class="menu-icon" onclick="goToPage('<?php echo $icon['url']; ?>')" title="<?php echo $icon['label']; ?>">
                    <img src="<?php echo $icon['img']; ?>" alt="<?php echo $icon['label']; ?>" class="icon-img">
                </div>
            <?php endforeach; ?>
        </div>
        <div class="main-content">
            <?php
            $user = $pdo->prepare('select * from Users');
            $sql = $pdo->prepare('select * from GlobalChat');
            $sql->execute();
            ?>
            <div class="global-chat">
                <?php
                foreach($sql as $row){
                echo '<div class=chat-comment>';
                echo '<div class="icon">
                        <a href="">
                            <div class="circle">
                                <img src="" alt="ProfileImage">
                            </div>
                        </a>
                    </div>';
                echo '<p class="account-name">ヤシの木</p>';
                echo '<p class="comment">',$row['commentText'],'</p>';
                echo '</div>';
                }
                ?>
            </div>
            <!-- 入力フォーム -->
            <div class="send">
                <form action="TopPage.php" method="post" enctype="multipart/form-data" class="text-box">
                    <input type="text" autocomplete="off" class="chat-text" placeholder="テキストを入力" name="HeaderSearch" spellcheck="false">
                    <button type="submit" class="send-button">
                        <img src="image/send-icon.svg" width="20" height="20" alt="送信">
                    </button>
                    <label for="file-upload" class="send-file">
                        <img src="image/file-icon.svg" width="20" height="20" alt="ファイル添付">
                    </label>
                    <input type="file" id="file-upload" name="file" style="display: none;" onchange="displayFileName(this)">
                </form>
            </div>
            <!-- ------------ -->
        </div>
        <div class="sideber2">
            <p>トップ画面だよ</p>
        </div>
    </div>
</body>
</html>