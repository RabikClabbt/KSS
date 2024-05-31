$(document).ready(function() {
    const itemsPerPage = 15;
    let currentPage = 1;
    let totalPages = 0;
    let keyword = '';

    function loadQuestions(page) {
        currentPage = page;
        const offset = (currentPage - 1) * itemsPerPage;

        $.ajax({
            url: 'LoadQuestions.php',
            method: 'GET',
            data: { offset: offset, limit: itemsPerPage, keyword: keyword },
            success: function(data) {
                $('#questionsContainer').html(data);
                renderPagination();
            },
            error: function() {
                console.log('エラーが発生しました。');
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
    loadQuestions(1);

    // 総件数を取得
    $.ajax({
        url: 'GetTotalCount.php',
        method: 'GET',
        data: { keyword: keyword },
        success: function(data) {
            totalPages = Math.ceil(data / itemsPerPage);
            renderPagination();
        },
        error: function() {
            console.log('エラーが発生しました。');
        }
    });
});