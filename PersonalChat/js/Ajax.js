$(document).ready(function() {
    function fetchChatHistory() {
        $.ajax({
<<<<<<< HEAD
            url: './ChatHistory.php',
            type: 'GET',
            data: { 
                userID: globalUserID,
                partnerID: globalPartnerID
            },
=======
            url: 'ChatHistory.php',
            type: 'GET',
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
            dataType: 'html',
            success: function(data) {
                $('.chat-history').html(data);
            },
<<<<<<< HEAD
            error: function(xhr, status, error) {
                console.error("Failed to fetch chat history:", error);
=======
            error: function() {
                console.error("Failed to fetch chat history.");
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
            }
        });
    }

<<<<<<< HEAD
    // 画面読み込み時に即座に履歴を取得
    fetchChatHistory();

    // その後、10秒ごとにチャット履歴を更新
    setInterval(fetchChatHistory, 10000);
});
=======
    // 10秒ごとにチャット履歴を取得
    setInterval(fetchChatHistory, 10000);
});
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
