<?php
session_start();
ob_start(); // ここで出力バッファリングを開始する
require '../src/db-connect.php';

//データベース接続用
$pdo=new PDO($connect,user,pass);

//連絡する相手の確認
$userID = $_SESSION['users']['id']; // 自身のユーザーID
$partnerID = $_GET['partnerID']; // 相手側のユーザーID


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chat = $_POST['chat'];
    $postPartnerID = $_POST['partnerID'];
    $uploadedFile = $_FILES['file'];

    //送信相手の確認
    $mstr='SELECT 1 FROM Users WHERE userID = ?';
    $mkeyArray = array($postPartnerID);
    $msql=$pdo->prepare($mstr);
    $msql->execute($mkeyArray);

    //対象の相手にチャット内容を送信する
    if ($msql) {
        // コメントもファイルも空でないかチェック
        if (!empty($chat) || (!empty($uploadedFile['name']) && $uploadedFile['error'] === UPLOAD_ERR_OK)) {
            if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
                $uploadDir = './uploads/';
                $uploadFilePath = $uploadDir . basename($uploadedFile['name']);

                if (move_uploaded_file($uploadedFile['tmp_name'], $uploadFilePath)) {
                    // ファイルアップロード成功
                    $filePath = $uploadFilePath;
                } else {
                    // ファイルアップロード失敗
                    $filePath = NULL;
                }
            } else {
                // ファイルがアップロードされなかった場合
                $filePath = NULL;
            }

            // チャットの保存
            $icstr = 'INSERT INTO DirectMessage (userID, partnerID, commentText, appendFile) VALUES (?, ?, ?, ?)';
            $insert = $pdo->prepare($icstr);
            $Array[0] = $userID;
            $Array[1] = $postPartnerID;
            $Array[2] = $chat;
            $Array[3] = $filePath;
            $insert->execute($Array);

            header("Location: ./PersonalChat.php?partnerID=$postPartnerID");
            exit();
        } else {
            // エラーメッセージを表示するなどの処理
            error_log('コメントかファイルのどちらかを入力してください');
        }
    } else {
        error_log('送信する相手が見つかりませんでした');
    }
} else {
    error_log('transmission error: Post data not found');
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="./css/Chat.css">
        <link rel="icon" href="../image/SiteIcon.svg" type="image/svg">
        <title><?= $partnerID ?> | Yadi-X</title>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="js/Ajax.js"></script>
        <script>
            var globalUserID = <?php echo json_encode($_SESSION['users']['id']); ?>;
            var globalPartnerID = <?php echo json_encode($_GET['partnerID']); ?>;

            // ファイルの添付・削除
            function triggerFileInput() {
                document.getElementById('file-input').click();
            }

            function displayFileName(input) {
                const file = input.files[0];
                const filePreviewContainer = document.getElementById('file-preview-container');
                const filePreview = document.getElementById('file-preview');
                const fileName = document.getElementById('file-name');
                const deleteButton = document.getElementById('delete-button');

                if (file) {
                    filePreviewContainer.style.display = 'flex'; // ファイルが選択されたときに表示
                    fileName.textContent = file.name;
                    deleteButton.style.display = 'block';

                    if (file.type.startsWith('../PersonalChat/uploads/')) {
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
                    removeFile(); // ファイルが選択されなかった場合に削除
                }
            }

            function removeFile() {
                const fileInput = document.getElementById('file-input');
                const filePreviewContainer = document.getElementById('file-preview-container');
                const filePreview = document.getElementById('file-preview');
                const fileName = document.getElementById('file-name');
                const deleteButton = document.getElementById('delete-button');

                fileInput.value = '';
                filePreviewContainer.style.display = 'none'; // ファイルがないときは非表示
                filePreview.style.display = 'none';
                filePreview.src = '';
                fileName.textContent = '';
                deleteButton.style.display = 'none';
            }
        </script>
    </head>
    <body>
        <header>
            <?php require '../Header/Header.php'; ?>
        </header>
        <div class="chat-all">
            <div class="chat-content">
                <div class="chat-history" id="chatHistoryContainer"></div>
                <div class="chat-form">
                    <form action="./PersonalChat.php?partnerID=<?= $_GET['partnerID'] ?>" method="post" enctype="multipart/form-data">
                        <div id="file-preview-container">
                            <img src="../image/Dustbin.svg" id="delete-button" onclick="removeFile()" alt="削除">
                            <img id="file-preview" style="display: none;" />
                            <span id="file-name"></span>
                        </div>
                        <div class="Cfunction">
                            <input type="textarea" name="chat" value="" class="text" cols="25" rows="5" wrap="hard">
                            <div class="Cfunctionbtn">
                                <button type="button" onclick="triggerFileInput()" class="upload-icon">
                                    <img src="../image/FileIcon.svg">
                                </button>
                                <input type="file" id="file-input" name="file" style="display: none;" onchange="displayFileName(this)">
                                <input type="hidden" name="partnerID" value="<?= $_GET['partnerID'] ?>">
                                <button type="submit" class="button">
                                    <img src="../image/SendIcon.svg" alt="送信">
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="space">
                <h2>知り合い</h2>
                <div class="space-list">
                    <?php
                    // 自身のユーザーIDがpartnerIDまたはuserIDとして登録されているものを探す(SQL)
                    $pstr = 'SELECT DISTINCT CASE WHEN dm.userID = :userID THEN dm.partnerID ELSE dm.userID END AS friendID, u.nickname, u.profileIcon 
                    FROM DirectMessage dm JOIN Users u ON (CASE WHEN dm.userID = :userID THEN dm.partnerID ELSE dm.userID END) = u.userID 
                    WHERE dm.userID = :userID OR dm.partnerID = :userID';
                    $pkeyArray = array('userID' => $userID);
                    $psql = $pdo->prepare($pstr);
                    $psql->execute($pkeyArray);

                    // 結果を表示する
                    $friendCount = 0;
                    if ($psql->rowCount() > 0) {
                        foreach ($psql as $row) { 
                            if ($row['friendID'] <> $partnerID) { ?>
                            <div class="profile">
                                <a href="./PersonalChat.php?partnerID=<?= $row['friendID'] ?>">
                                    <div class="circle">
                                        <?php if (!empty($row['profileIcon'])) { ?>
                                            <img src="<?= $row['profileIcon'] ?>" alt="profileIcon">
                                        <?php } else { ?>
                                            <img src="../image/DefaultIcon.svg" alt="profileIcon">
                                        <?php } ?>
                                    </div>
                                    <div class="nickname"><?= htmlspecialchars($row['nickname']) ?></div>
                                </a>
                            </div>
                            <?php $friendCount++; }
                        }
                    } else { ?>
                        <div class="nofriend">
                            知り合いを増やそう！
                        </div>
                    <?php } 
                    if ($friendCount === 0) { ?>
                        <div class="nofriend">
                            知り合いを増やそう！
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>

<?php
ob_end_flush();
?>