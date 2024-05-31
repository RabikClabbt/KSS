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

$totalCount = $conn->query("SELECT COUNT(*) FROM Question WHERE questionTitle LIKE '%$keyword%' OR questionText LIKE '%$keyword%'")->fetchColumn();
echo $totalCount;
?>