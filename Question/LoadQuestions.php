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
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 30;
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$throwCategory = isset($_GET['tc']) ? (int)$_GET['tc'] : null;


$sql = "SELECT q.userID, u.nickname, u.profileIcon, q.questionID, q.questionTitle, q.questionText
        FROM Question q
        JOIN Users u ON q.userID = u.userID";

if (!empty($keyword)) {
    $sql .= " WHERE q.questionTitle LIKE '%$keyword%' OR q.questionText LIKE '%$keyword%'";
}

if (!is_null($throwCategory)) {
    $sql .= " WHERE category = $throwCategory";
}

$sql .= " ORDER BY q.questionID DESC LIMIT $limit OFFSET $offset";

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
                <a href="../Profile/OtherProfile.php?userID=<?= $userID ?>">
                    <div class="circle">
                        <?php if (!empty($profileIcon)) { ?>
                            <img src="<?= $profileIcon ?>" alt="profileIcon">
                        <?php } else { ?>
                            <img src="../image/DefaultIcon.svg" alt="profileIcon">
                        <?php } ?>
                    </div>
                    <div class="nickname"><?= htmlspecialchars($nickname) ?></div>
                </a>
            </div>
            <a href="./Detail.php?questionID=<?= $questionID ?>" class="questionLink">
                <div class="questionTitle"><?= htmlspecialchars($questionTitle) ?></div>
            </a>
            <div class="questionText" data-full-text="<?= htmlspecialchars($questionText) ?>">
                <?= htmlspecialchars(mb_substr($questionText, 0, 30, 'UTF-8')) ?>
            </div>
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
?>