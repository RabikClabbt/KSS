$(document).ready(function() {
    function fetchChatHistory() {
        $.ajax({
            url: 'ChatHistory.php',
            type: 'GET',
            dataType: 'html',
            success: function(data) {
                $('.chat-history').html(data);
            },
            error: function() {
                console.error("Failed to fetch chat history.");
            }
        });
    }

    // 10秒ごとにチャット履歴を取得
    setInterval(fetchChatHistory, 10000);
});
