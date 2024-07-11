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

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15;
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

$sql = "SELECT q.userID, u.nickname, u.profileIcon, q.questionID, q.questionTitle, q.questionText
        FROM Question q
        JOIN Users u ON q.userID = u.userID
        WHERE q.questionTitle LIKE '%$keyword%' OR q.questionText LIKE '%$keyword%'
        ORDER BY q.questionID ASC
        LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($results)) {
    foreach ($results as $result) {
        $userID = $result['userID'];
        $nickname = $result['nickname'];
        $profileIcon = $result['profileIcon'];
        $questionID = $result['questionID'];
        $questionTitle = $result['questionTitle'];
        $questionText = $result['questionText'];
        ?>
        <div class="question">
            <div class="profile">
                <!-- 相手側のユーザーページへのリンク -->
                <a href="#">
                    <div class="circle">
                        <?php if (!empty($profileIcon)) { ?>
                            <img src="<?= $profileIcon ?>" alt="profileIcon">
                        <?php } else { ?>
                            <img src="../image/DefaultIcon.svg" alt="profileIcon">
                        <?php } ?>
                    </div>
                </a>
                <div class="nickname"><?= htmlspecialchars($nickname) ?></div>
            </div>
            <a href="./Detail.php?questionID=<?= $questionID ?>" class="questionLink">
                <div class="questionTitle"><?= htmlspecialchars($questionTitle) ?></div>
            </a>
            <div class="questionText"><?= htmlspecialchars($questionText) ?></div>
            <div class="actions"><button class="like">👍</button></div>
        </div>
        <?php
    }
} else {
    ?>
    <div class="noResult">
        <p>お探しの質問は見つかりませんでした。</p>
        <p>別のキーワードを検索してみてください。</p>
    </div>
    <?php
}