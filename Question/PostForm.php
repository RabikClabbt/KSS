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

$isLoggedIn = isset($_SESSION['users']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isLoggedIn) {
    $userId = $_SESSION['users']['id'];
    $category = $_POST['QuestionCategory'];
    $title = $_POST['QuestionTitle'];
    $content = $_POST['QuestionContent'];
    $appendFile = $_POST['FileUpload'] ?? null;

    $bindValues = [$userId, $title, $content, $appendFile];
    $placeholders = '(?, ?, ?, ?';
    if ($category !== null) {
        $bindValues[] = $category;
        $placeholders .= ', ?';
    }
    $placeholders .= ')';
    $query = "INSERT INTO Question " . $placeholders . " VALUES (?, ?, ?, ?" . str_repeat(', ?', count($bindValues) - 4) . ")";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute($bindValues)) {
        // 成功時の処理
        $query = "SELECT questionID FROM Question WHERE userID = ? AND questionTitle = ? AND questionText = ? ORDER BY id DESC LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$userId, $title, $content]);
        $question = $stmt->fetch();
        $questionID = $question['questionID'];
        header('Location: Detail.php?questionID=' . $questionID);
        exit;
    } else {
        $error = "質問の投稿に失敗しました。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <title>質問投稿</title>
    <link rel="stylesheet" type="text/css" href="./css/PostForm.css">
</head>
<body>
    <header>
        <?php require "../Header/Header.php"; ?>
    </header>
    <div class="container">
        <main>
            <div id="LoginModal" class="Modal">
                <div class="ModalContent">
                    <h2>ログインが必要です</h2>
                    <p>質問を投稿するにはログインしてください。</p>
                    <a href="../Login/LoginIn.php" class="LoginButton">ログイン</a>
                </div>
            </div>
            <form id="QuestionForm" method="POST">
                <div class="QuestionContents">
                    <div class="UserInfo">
                        <?php if(isset($_SESSION['users'])) { ?>
                            <a href="#">
                                <div class="circle">
                                    <?php
                                        if(!empty($_SESSION['users']['icon'])) {
                                            echo '<img src="'. $_SESSION['users']['icon'] .'" alt="profileIcon">';
                                        } else {
                                            echo '<img src="../image/DefaultIcon.svg" alt="profileIcon">';
                                        }
                                    ?>
                                </div>
                            </a>
                            <div class="nickname"><?= htmlspecialchars($_SESSION['users']['name']) ?></div>
                        <?php } else { ?>
                            <div class="circle">
                                <img src="../image/DefaultIcon.svg" alt="profileIcon">
                            </div>
                        <?php } ?>
                    </div>
                    <div class="QuestionForm">
                        <div class="TitleWrapper">
                            <label for="QuestionTitle">タイトル</label>
                            <input type="text" id="QuestionTitle" name="QuestionTitle" required>
                        </div>

                        <div class="CategoryWrapper">
                            <?php
                            try {
                                $sqlc = "SELECT categoryID, categoryName FROM Category WHERE 1";
                                $stmtc = $pdo->prepare($sqlc);
                                $stmtc->execute();
                                $categories = $stmtc->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                error_log("カテゴリ取得エラー: " . $e->getMessage());
                                $categories = []; // エラー時は空の配列を設定
                            }
                            ?>
                            <label for="QuestionCategory">カテゴリ</label>
                            <select id="QuestionCategory" name="QuestionCategory">
                                <option value="" disabled selected>カテゴリを選択</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= htmlspecialchars($category['categoryID']) ?>">
                                            <?= htmlspecialchars($category['categoryName']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <option value="">なし</option>
                            </select>
                        </div>

                        <div class="ContentWrapper">
                            <label for="QuestionContent">質問内容</label>
                            <textarea id="QuestionContent" name="QuestionContent" required></textarea>
                        </div>

                        <div class="FileUploadWrapper">
                            <label for="FileUpload">ファイル・画像のアップロード</label>
                            <input type="file" id="FileUpload" name="FileUpload">
                        </div>
                        
                        <div class="SubmitWrapper">
                            <button type="submit">投稿</button>
                        </div>
                    </div>
                </div>
            </form>
        </main>
        <side>
                <div class="topic">
                    <p>話題の質問</p>
                    <div class="topicList">

                    </div>
                </div>
                <div class="recruiting">
                    <p>回答募集中</p>
                    <div class="recruitingList">

                    </div>
                </div>
                <div>
                    <!-- 空のdiv -->
                </div>
        </side>
    </div>
    <footer>
        <!-- フッターの内容 -->
    </footer>
    <script>
        var isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
    </script>
    <script src="./js/PostForm.js"></script>
</body>
</html>