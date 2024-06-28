<?php
include '../db-connect.php';

$groupID = $_GET['groupID'];

//グループを選択しないといけない

$sql = "SELECT * FROM GroupChat WHERE groupID = ? ORDER BY commentID ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $groupID);
$stmt->execute();
$result = $stmt->get_result();

$messages = array();
while($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>