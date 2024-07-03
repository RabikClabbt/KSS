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

function sendComment(questionID) {
    const commentText = commentInput.value.trim();
    if (commentText !== '') {
        const data = {
            comment: commentText,
            questionID: questionID
        };

        if (this.parentID != null) {
            data.parentID = this.parentID;
            console.log("sendComment_ParentID: ", data.parentID);
        }

        if (commentType === "r" || commentType === "a") {
            data.commentType = commentType;
            console.log("sendComment_commentType: ", data.commentType);
        }

        const url = tableName === "Answer" ? './ProcessAnswer.php' : './ProcessReply.php';

        axios.post(url, data)
            .then(response => {
                console.log(response.data);
                commentInput.value = '';
            })
            .catch(error => {
                console.error('エラー:', error);
            });
    }
}
