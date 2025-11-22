document.addEventListener('DOMContentLoaded', () => {
    const chatHistory = document.getElementById('groupHistory');
    let isFirstLoad = true;

    async function fetchHistory() {
        try {
            const response = await fetch(`./GroupHistory.php?groupID=${encodeURIComponent(groupID)}&userID=${encodeURIComponent(currentUserId)}`);
            if (!response.ok) return;
            const html = await response.text();
            chatHistory.innerHTML = html;
            if (isFirstLoad) {
                chatHistory.scrollTop = chatHistory.scrollHeight;
                isFirstLoad = false;
            }
        } catch (error) {
            console.error('Failed to fetch history', error);
        }
    }

    fetchHistory();
    setInterval(fetchHistory, 8000);
});

function triggerFileInput(event) {
    event.preventDefault();
    document.getElementById('file-input').click();
}

function displayFileName(input) {
    const file = input.files[0];
    const filePreviewContainer = document.getElementById('file-preview-container');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const deleteButton = document.getElementById('delete-button');

    if (file) {
        filePreviewContainer.style.display = 'flex';
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
        removeFile();
    }
}

function removeFile() {
    const fileInput = document.getElementById('file-input');
    const filePreviewContainer = document.getElementById('file-preview-container');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const deleteButton = document.getElementById('delete-button');

    fileInput.value = '';
    filePreviewContainer.style.display = 'none';
    filePreview.style.display = 'none';
    filePreview.src = '';
    fileName.textContent = '';
    deleteButton.style.display = 'none';
}
