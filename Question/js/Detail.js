let commentInput = null;
let tableName = "Answer"; // Default to Answer
let parentID = null;
let commentType = "q"; // Default comment type to 'q'

window.onload = function() {
    const bestFlgText = document.querySelector('.bestFlg p');
    commentInput = document.getElementById('commentInput');
    switch (bestFlgText.textContent) {
        case "解決済":
            bestFlgText.style.color = "#ff4500";
            break;
        default:
            break;
    }
};

function focusCommentInput(type, parentID) {
    commentType = type;
    if (parentID) {
        this.parentID = parentID;
    }
    hintChange(commentType);
    console.log("ParentID: ", this.parentID, "ParentType: ", commentType);
    commentInput.focus();
}

function hintChange(type) {
    commentType = type;
    if (isLoggedIn) {
        switch (commentType) {
            case "q":
                tableName = "Answer";
                commentInput.placeholder = "質問への回答を送信する";
                break;
            case "a":
                tableName = "Reply";
                commentInput.placeholder = "回答へコメントを送信する";
                break;
            case "r":
                tableName = "Reply";
                commentInput.placeholder = "返信へコメントを送信する";
                break;
            default:
                console.error("Invalid comment type:", commentType);
                break;
        }
    }
}

function sendComment(questionID) {
    const commentText = commentInput.value.trim();
    const fileInput = document.getElementById('appendFileButton');
    const file = fileInput.files[0];
    if (commentText !== '') {
        const formData = new FormData();
        formData.append('comment', commentText);
        formData.append('questionID', questionID);

        if (file) {
            formData.append('appendFile', file);
        }

        if (this.parentID != null) {
            formData.append('parentID', this.parentID);
        }

        if (commentType === "r" || commentType === "a") {
            formData.append('commentType', commentType);
        }

        console.log(formData);

        const url = tableName === "Answer" ? './ProcessAnswer.php' : './ProcessReply.php';

        axios.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(response => {
            if (response.data.success) {
                console.log(response.data.message);
                commentInput.value = '';
                removeFile();
            } else {
                console.error('エラー:', response.data.error);
                alert(response.data.error);
            }
            window.location.reload();
        })
        .catch(error => {
            console.error('エラー:', error);
            alert('送信中にエラーが発生しました。');
            window.location.reload();
        });
    }
}

function bestAnswer(qID, aID, flg) {
    const data = {
        qID: qID,
        aID: aID,
        flg: flg === 1 ? 0 : 1
    };
    console.log(data);
    axios.post('./BestAnswer.php', data)
        .then(response => {
            console.log(response.data);
            window.location.reload();
        })
        .catch(error => {
            console.error('エラー:', error);
        });
}

function triggerFileInput() {
    event.preventDefault();
    document.getElementById('appendFileButton').click();
}
function displayFileName(input) {
    const file = input.files[0];
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const deleteButton = document.getElementById('deleteButton');

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
    const fileInput = document.getElementById('appendFileButton');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const deleteButton = document.getElementById('deleteButton');

    fileInput.value = '';
    filePreviewContainer.style.display = 'none'; // ファイルがないときは非表示
    filePreview.style.display = 'none';
    filePreview.src = '';
    fileName.textContent = '';
    deleteButton.style.display = 'none';
}