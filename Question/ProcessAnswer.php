<?php
session_start();
require '../src/db-connect.php';

try {
    $pdo = new PDO($connect, user, pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION)) {
    $requestData = json_decode(file_get_contents('php://input'), true);

    $userID = $_SESSION['users']['id'];
    $comment = htmlspecialchars($requestData['comment'], ENT_QUOTES, 'UTF-8');
    $questionID = $requestData['questionID'];

    // デバッグ用logの出力
    var_dump("UserID: $userID, Comment: $comment, QuestionID: $questionID");

    $sql = "INSERT INTO Answer (userID, questionID, answerText) VALUES (:userID, :questionID, :answerText)";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
    $stmt->bindParam(':questionID', $questionID, PDO::PARAM_INT);
    $stmt->bindParam(':answerText', $comment, PDO::PARAM_STR);

    try {
        if ($stmt->execute()) {
            echo "コメントを送信しました";
        } else {
            echo "エラー: " . $stmt->errorInfo()[2];
        }
    } catch (PDOException $e) {
        echo "エラー: " . $e->getMessage();
    }
}
?>
