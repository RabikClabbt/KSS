document.addEventListener('DOMContentLoaded', function() {
    const loginModal = document.getElementById('LoginModal');
    const questionForm = document.getElementById('QuestionForm');

    // ユーザーがログインしていない場合、ログインモーダルを表示する
    if (!isLoggedIn) {
        loginModal.style.display = 'block';
    }

    // ユーザーがログインしていない場合、フォーム送信を防止し、ログインモーダルを表示する。
    questionForm.addEventListener('submit', function(event) {
        if (!isLoggedIn) {
            event.preventDefault();
            loginModal.style.display = 'block';
        }
    });

    // ログインしている場合のみ、外部でクリックされた場合はログインモーダルを閉じる。
    window.addEventListener('click', function(event) {
        if (isLoggedIn && event.target === loginModal) {
            loginModal.style.display = 'none';
        }
    });
});
