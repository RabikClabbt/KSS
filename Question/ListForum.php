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

// 総件数を取得
$totalCount = $conn->query("SELECT COUNT(*) FROM Question WHERE questionTitle LIKE '%$keyword%' OR questionText LIKE '%$keyword%'")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>タイトル</title>
    <link rel="stylesheet" type="text/css" href="./css/ListForum.css">
</head>
<body>
      <header>
            <!-- Header.htmlを読み込む -->
            <div id="external-content"></div>
            <?php include '../Header/Header.php' ?>
            <div class="categorylist">
                <a href="#">生活</a>
                <a href="#">学校</a>
                <a href="#">勉強</a>
                <a href="#">テクノロジー</a>
                <a href="#">その他</a>
            </div>
      </header>
      <main class="container">
        <div class="control">
            <button class="post-question">質問を投稿する</button>
            <div class="post-search">
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="get">
                    <input type="text" autocomplete="off" aria-autocomplete="list" aria-controls="react-autowhatever-1" class="post-search-input" placeholder="質問キーワードを入力" name="keyword" value="<?= htmlspecialchars($keyword) ?>" spellcheck="false" data-ms-editor="true">
                    <button type="submit" class="post-search-button">
                        <img src="../image/SearchIcon.svg" width="20" height="20" alt="検索">
                    </button>
                </form>
            </div>
        </div>
        <!-- 残りのHTML -->
        <div class="questions" id="questionsContainer"></div>
        <div class="pagination" id="paginationContainer"></div>
        <br>
        <br>
    </main>
    <footer>
        <!-- 将来的にFooter.htmlを読み込むかも -->
    </footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="./js/Pagination.js"></script>