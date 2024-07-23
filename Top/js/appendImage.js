function triggerFileInput() {
    event.preventDefault();
    document.getElementById('file-upload').click();
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

        if (file.type.startsWith('image/')) {
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
    const fileInput = document.getElementById('file-upload');
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