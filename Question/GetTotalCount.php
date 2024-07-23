<?php
session_start();
require '../src/db-connect.php';

try {
    $conn = new PDO($connect, user, pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
    exit;
}

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$throwCategory = isset($_GET['tc']) ? (int)$_GET['tc'] : null;

$sql = "SELECT COUNT(*) FROM Question";

if (!empty($keyword)) {
    $sql .= " WHERE q.questionTitle LIKE '%$keyword%' OR q.questionText LIKE '%$keyword%'";
}

if (!is_null($throwCategory)) {
    $sql .= " WHERE category = $throwCategory";
}

$stmt = $conn->prepare($sql);
$stmt->execute();
$totalCount = $stmt->fetchColumn();

echo $totalCount;