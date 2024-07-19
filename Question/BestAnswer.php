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

    $qID = $requestData['qID'];
    $aID = $requestData['aID'];
    $flg = $requestData['flg'];

    // 同じ質問に対するanswerで既にbestFlgが１の場合はエラーを返す
    if ($flg == 1) {
        $sql = "SELECT count(*) as count FROM Answer WHERE questionID = :questionID AND bestFlg = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':questionID', $qID, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        if ($count >= 1) {
            echo "DataError: The best answer has already been selected";
            exit;
        }
    }

    // Answerテーブルの渡された値に合致するbestFlgの更新処理
    $sql = "UPDATE Answer SET bestFlg = :bestFlg WHERE questionID = :questionID AND answerID = :answerID";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':questionID', $qID, PDO::PARAM_INT);
    $stmt->bindParam(':answerID', $aID, PDO::PARAM_INT);
    $stmt->bindParam(':bestFlg', $flg, PDO::PARAM_INT);

    $stmt->execute();
} else {
    echo "DataError: Incorrect value sent";
    exit;
}
?>
