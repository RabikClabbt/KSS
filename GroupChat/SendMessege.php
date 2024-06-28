<?php
include '../db-connect.php';

$groupID = $_POST['groupID'];
$userID = $_POST['userID'];
$commentText = $_POST['commentText'];
$appendFile = $_POST['appendFile']; // 別途処理

$sql = "INSERT INTO GroupChat (groupID, userID, commentText, appendFile) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $groupID, $userID, $commentText, $appendFile);

if ($stmt->execute()) {
    echo "新しいレコードが正常に作成されました";
} else {
    echo "エラー: " . $stmt->error;
}

$conn->close();
?>
