$(document).ready(function() {
    const itemsPerPage = 15;
    let currentPage = 1;
    let totalPages = 0;
    let keyword = '';
    let tc = '';

    function loadQuestions(page) {
        currentPage = page;
        const offset = (currentPage - 1) * itemsPerPage;

        let data = { offset: offset, limit: itemsPerPage };
        if (keyword) data.keyword = keyword;
        if (tc) data.tc = tc;

        $.ajax({
            url: 'LoadQuestions.php',
            method: 'GET',
            data: data,
            success: function(data) {
                $('#questionsContainer').html(data);
                updateTotalCount();
            },
            error: function(xhr, status, error) {
                console.log('エラーが発生しました:', error);
            }
        });
    }

    function updateTotalCount() {
        const data = {};
        if (keyword) data.keyword = keyword;
        if (tc) data.tc = tc;
        $.ajax({
            url: 'GetTotalCount.php',
            method: 'GET',
            data: data,
            success: function(count) {
                totalPages = Math.ceil(parseInt(count, 10) / itemsPerPage);
                renderPagination();
            },
            error: function(xhr, status, error) {
                console.log('エラーが発生しました:', error);
            }
        });
    }

    function renderPagination() {
        const paginationContainer = $('#paginationContainer');
        paginationContainer.empty();

        if (totalPages > 1) {
            let paginationHtml = '<div class="pagination-links">';
            for (let i = 1; i <= totalPages; i++) {
                const pageLink = `<a href="#" class="pagination-link ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</a>`;
                paginationHtml += pageLink;
            }
            paginationHtml += '</div>';
            paginationContainer.html(paginationHtml);

            $('.pagination-link').click(function(event) {
                event.preventDefault();
                const page = $(this).data('page');
                loadQuestions(page);
            });
        }
    }

    // 初期表示
    const urlParams = new URLSearchParams(window.location.search);
    keyword = urlParams.get('keyword') || '';
    tc = urlParams.get('tc') || '';
    loadQuestions(1);

    // カテゴリーボタンのイベントリスナー
    $('.categoryButton').click(function(event) {
        event.preventDefault();
        tc = $(this).closest('form').find('input[name="tc"]').val();
        keyword = ''; // カテゴリー変更時にキーワードをリセット
        $('.post-search-input').val(''); // 検索フォームの入力をクリア
        currentPage = 1;
        loadQuestions(currentPage);
    });

    // 検索フォームのサブミットイベント
    $('.post-search form').submit(function(event) {
        event.preventDefault();
        keyword = $(this).find('input[name="keyword"]').val();
        tc = ''; // キーワード検索時にカテゴリーをリセット
        currentPage = 1;
        loadQuestions(currentPage);
    });
});