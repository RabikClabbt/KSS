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
    $appendFile = NULL;

    // ファイルがアップロードされているかチェックする
    if (isset($_FILES['FileUpload'])) {
        $targetDir = "../Question/uploads/";
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // 許可されているファイル形式かどうかをチェック
        $allowedTypes = ["jpg", "png", "jpeg", "gif", "pdf"];
        if (!in_array($fileType, $allowedTypes)) {
            $error = "許可されていないファイル形式です。";
            $uploadOk = 0;
        }
    
        // ファイル名に疑似乱数を使用し、0~9の値を最後尾に付与する。
        // その後、文字列をハッシュ化し、初めの15文字を取得する。
        $targetFile = $targetDir . basename(
            substr(
                sha1(basename($_FILES['FileUpload']['tmp_name']) . rand(0, 9)),
                0,
                15
            )
            . '.' . $fileType
        );

        error_log($targetFile);

        // エラーがあればアップロードを中止
        if ($uploadOk == 0) {
            $error = "ファイルはアップロードされませんでした。";
        } else {
            if (move_uploaded_file($_FILES['FileUpload']['tmp_name'], $targetFile)) {
                $appendFile = $targetFile; // アップロードされたファイルのパスを保存
            } else {
                $error = "ファイルのアップロードに失敗しました。";
            }
        }
        
        if (isset($error)) {
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        }
    }

    try {
        if (empty($category)) {
            $query = "INSERT INTO Question (userID, questionTitle, questionText, appendFile) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            if ($stmt->execute([$userId, $title, $content, $appendFile])) {
                // 成功時の処理
                $query = "SELECT questionID FROM Question WHERE userID = ? AND questionTitle = ? AND questionText = ? ORDER BY questionID DESC LIMIT 1";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$userId, $title, $content]);
                $question = $stmt->fetch();
                $questionID = $question['questionID'];
                header('Location: Detail.php?questionID=' . $questionID);
                exit;
            } else {
                $error = "質問の投稿に失敗しました。";
            }
        } else {
            $query = "INSERT INTO Question (userID, category, questionTitle, questionText, appendFile) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            if ($stmt->execute([$userId, $category, $title, $content, $appendFile])) {
                // 成功時の処理
                $query = "SELECT questionID FROM Question WHERE userID = ? AND questionTitle = ? AND questionText = ? ORDER BY questionID DESC LIMIT 1";
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
    } catch (PDOException $e) {
        error_log("エラー: " . $e->getMessage());
        $error = "データベースエラーが発生しました。";
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <link rel="icon" href="../image/SiteIcon.svg" type="image/svg">
    <title><?= $_SESSION['users']['name'] ?>さん 疑問や質問を投稿しよう！ | Yadi-X</title>
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
                        <div id="imagePreview" style="display:none;">
                            <img id="uploadedImage" src="" alt="アップロードされた画像" style="max-width: 300px;">
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
                <div class="back">
                    <button class="historyBack" onclick="history.back()">戻る</button>
                </div>
            </div>
        </side>
    </div>
    <footer>
        <!-- フッターの内容 -->
    </footer>
    <script>
        let isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
    </script>
    <script src="./js/PostForm.js"></script>
</body>
</html>