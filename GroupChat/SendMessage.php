<?php
include '../db-connect.php';

$groupID = $_POST['groupID'];
$userID = $_POST['userID'];
$commentText = $_POST['commentText'];
$appendFile = $_POST['appendFile']; // 別途処理

if (empty($groupID) || empty($userID) || empty($commentText)) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$sql = "INSERT INTO GroupChat (groupID, userID, commentText, appendFile) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $groupID, $userID, $commentText, $appendFile);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Message sent']);
} else {
    echo json_encode(['error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>