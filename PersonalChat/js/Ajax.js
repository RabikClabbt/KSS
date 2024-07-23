window.addEventListener('load', function() {
    const chatHistory = document.getElementById('chatHistoryContainer');
    let isFirstLoad = true;  // 初回ロードフラグ

    function scrollToBottom() {
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

    function fetchChatHistory() {
        $.ajax({
            url: './ChatHistory.php',
            type: 'GET',
            data: { 
                userID: globalUserID,
                partnerID: globalPartnerID
            },
            dataType: 'html',
            success: function(data) {
                $('.chat-history').html(data);
                if (isFirstLoad) {
                    scrollToBottom();  // 初回のみスクロール
                    isFirstLoad = false;  // フラグを更新
                }
            },
            error: function(xhr, status, error) {
                console.error("Failed to fetch chat history:", error);
            }
        });
    }

    // 画面読み込み時に即座に履歴を取得
    fetchChatHistory();

    // その後、10秒ごとにチャット履歴を更新（スクロールなし）
    setInterval(fetchChatHistory, 10000);
});