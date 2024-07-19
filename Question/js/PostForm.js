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

document.getElementById('FileUpload').addEventListener('change', function(e) {
    var file = e.target.files[0];
    var formData = new FormData();

    console.log(file, formData);

    formData.append('FileUpload', file);

    fetch('./PostForm.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('imagePreview').style.display = 'block';
            document.getElementById('uploadedImage').src = data.file;
        } else {
            alert(data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});