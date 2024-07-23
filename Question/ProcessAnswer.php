<?php
session_start();
require '../src/db-connect.php';

try {
    $pdo = new PDO($connect, user, pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => "接続エラー: " . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION)) {
    $userID = $_SESSION['users']['id'];
    $questionID = $_POST['questionID'];
    $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
    
    // ファイルのアップロード処理
    $appendFile = null;
    if (isset($_FILES['appendFile']) && $_FILES['appendFile']['error'] == 0) {
        $targetDir = "../Question/uploads/";
        $fileType = strtolower(pathinfo($_FILES['appendFile']['name'], PATHINFO_EXTENSION));

        // 許可されているファイル形式かどうかをチェック
        $allowedTypes = ["jpg", "png", "jpeg", "gif", "pdf"];
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'error' => "許可されていないファイル形式です。"]);
            exit;
        }

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                echo json_encode(['success' => false, 'error' => "ディレクトリの作成に失敗しました。"]);
                exit;
            }
        }
    
        $targetFile = $targetDir . basename(substr(sha1(basename($_FILES['appendFile']['name']) . rand(0, 9)), 0, 15) . '.' . $fileType);

        if (move_uploaded_file($_FILES['appendFile']['tmp_name'], $targetFile)) {
            $appendFile = $targetFile;
        } else {
            echo json_encode(['success' => false, 'error' => "ファイルのアップロードに失敗しました。"]);
            exit;
        }
    }

    $sql = "INSERT INTO Answer (userID, questionID, answerText, appendFile) VALUES (:userID, :questionID, :answerText, :appendFile)";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':userID', $userID, PDO::PARAM_STR);
    $stmt->bindParam(':questionID', $questionID, PDO::PARAM_INT);
    $stmt->bindParam(':answerText', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':appendFile', $appendFile, PDO::PARAM_STR);

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