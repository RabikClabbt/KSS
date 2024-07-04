<?php
    session_start();
    if (!isset($_SESSION['users'])) {
        header('Location: ../Login/LoginIn.php');
        exit;
    }
    $userID = $_SESSION['users']['id'];
    // セッションを使い、所属するgroupIDを取得する
    $groupID = $_SESSION['groupID'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>グループチャット</title>
    <style>
        #chat {
            width: 500px;
            height: 300px;
            border: 1px solid #ccc;
            overflow-y: scroll;
        }
        #messageInput {
            width: 80%;
        }
        #sendButton {
            width: 18%;
        }
    </style>
</head>
<body>
    <div id="chat"></div>
    <input type="text" id="messageInput" placeholder="メッセージを入力">
    <button id="sendButton">送信</button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var groupID = "<?php echo $groupID; ?>"; // 動的にgroupIDを設定
        var userID = "<?php echo $userID; ?>"; // 動的にuserIDを設定

        function fetchMessages() {
            $.ajax({
                url: 'FetchMessage.php',
                method: 'GET',
                data: { groupID: groupID },
                success: function(data) {
                    var messages = JSON.parse(data);
                    var chat = $('#chat');
                    chat.html('');
                    messages.forEach(function(message) {
                        chat.append('<div>' + message.userID + ': ' + message.commentText + '</div>');
                    });
                }
            });
        }

        function sendMessage() {
            var commentText = $('#messageInput').val();
            $.ajax({
                url: 'SendMessage.php',
                method: 'POST',
                data: {
                    groupID: groupID,
                    userID: userID,
                    commentText: commentText,
                    appendFile: '' // ファイルアップロードは別途処理
                },
                success: function(response) {
                    $('#messageInput').val('');
                    fetchMessages();
                }
            });
        }

        $(document).ready(function() {
            fetchMessages();
            setInterval(fetchMessages, 3000); // 3秒ごとにメッセージを取得

            $('#sendButton').click(function() {
                sendMessage();
            });
        });
    </script>
</body>
</html>