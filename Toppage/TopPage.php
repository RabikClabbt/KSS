<?php
$icons = [
    ['url' => '../page1.php', 'label' =>'Q&A','img'=> './Image/Q&A.svg'],
    ['url' => '../page2.php', 'label' =>'chat','img'=> './Image/chat.svg'],
    ['url' => '../page3.php', 'label' =>'group-chat','img'=> './Image/group-chat.svg']
];
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
    <?php require '../db-connect.php'; ?>
    <div class="headerr">
        <?php require '../Header/Header.html'; ?>
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
            <div class="global-chat">
                <?php
                echo '<div class=chat-comment>';
                echo '<img src="" alt="icon">';
                echo '<p class="account-name">ヤシの木</p>';
                echo '<p class="comment">こんにちは</p>';
                echo '</div>';
                ?>
            </div>
            <div class="send">
                <!-- 入力フォーム -->
                <form action="TopPage.php" method="post" enctype="multipart/form-data" class="text-box">
                    <input type="text" autocomplete="off" class="chat-text" placeholder="テキストを入力" name="HeaderSearch" spellcheck="false">
                    <button type="submit" class="send-button">
                        <img src="./Image/send-icon.svg" width="20" height="20" alt="送信">
                    </button>
                    <label for="file-upload" class="send-file">
                        <img src="./Image/file-icon.svg" width="20" height="20" alt="ファイル添付">
                    </label>
                    <input type="file" id="file-upload" name="file" style="display: none;" onchange="displayFileName(this)">
                </form>
            </div>
        </div>
        <div class="sideber2">
            <p>トップ画面だよ</p>
        </div>
    </div>
</body>
</html>