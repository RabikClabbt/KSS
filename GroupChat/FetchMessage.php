<?php
include '../db-connect.php';

$groupID = $_GET['groupID'];

if (empty($groupID)) {
    echo json_encode(['error' => 'Invalid group ID']);
    exit;
}

$sql = "SELECT * FROM GroupChat WHERE groupID = ? ORDER BY commentID ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $groupID);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $messages = array();
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    echo json_encode($messages);
} else {
    echo json_encode(['error' => 'Failed to fetch messages']);
}
$stmt->close();
$conn->close();
?>