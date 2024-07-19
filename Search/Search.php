<?php 
session_start(); 
<<<<<<< HEAD
require '../src/db-connect.php';

// POSTリクエストかつ検索クエリが存在する場合、セッションに保存してリダイレクト
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['search']) && !empty($_POST['search'])) {
        $_SESSION['search_query'] = $_POST['search'];
    } else {
        // 空の入力の場合はセッションをクリア
        unset($_SESSION['search_query']);
    }
=======
require 'db-connect.php'; 

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $_SESSION['search'] = $_POST['search'];
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
    header('Location: Search.php');
    exit();
}

<<<<<<< HEAD
// セッションから検索クエリを取得
$search_query = isset($_SESSION['search_query']) ? $_SESSION['search_query'] : '';
=======
// 検索キーワードの取得
$search = isset($_SESSION['search']) ? $_SESSION['search'] : '';

>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <link rel="stylesheet" href="./css/Search.css">
    <link rel="icon" href="../image/SiteIcon.svg" type="image/svg">
    <title>検索 | Yadi-X</title>
=======
    <link rel="stylesheet" href="css/search.css">
    <title>検索</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const users = document.querySelectorAll('.user');

            users.forEach(function(user) {
                user.addEventListener('click', function() {
                    const url = this.getAttribute('user-url');
                    if (url) {
                        window.location.href = url;
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const questions = document.querySelectorAll('.question');

            questions.forEach(function(question) {
                question.addEventListener('click', function() {
                    const url = this.getAttribute('question-url');
                    if (url) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
</head>
<body>
    <header>
        <?php require '../Header/Header.php'; ?>
    </header>
    <main>
        <form action="./Search.php" method="post">
            <div class="Sfunction">
                <input type="text" placeholder="検索" name="search" class="text" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="button">
                    <img src="../image/SearchIcon.svg" alt="検索">
                </button>
            </div>
        </form>
        <?php
            $pdo = new PDO($connect, user, pass);
            $ustr = 'select userID, nickname, profileIcon from Users where ';
            $qstr = 'select q.userID, u.nickname, u.profileIcon, q.questionID, q.questionTitle from Question as q left join Users as u on u.userID = q.userID where ';
        ?>
        <div class="result">
            <div class="userResult">
                <h2>ユーザー</h2><br>
                <?php
<<<<<<< HEAD
                if (isset($search_query) && !empty($search_query)) {
                    $ustr = $ustr . 'nickname like ? AND userID <> "Anonymous"';
                    $ukeyArray[0] = '%' . $search_query . '%';
=======
                if (!empty($search)) {
                    $ustr = $ustr . 'userID like ?';
                    $ukeyArray[0] = '%' . $search . '%';
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
                    $sql = $pdo->prepare($ustr);
                    $sql->execute($ukeyArray);

                    if ($sql->rowCount() > 0) {
<<<<<<< HEAD
                        foreach ($sql as $row) { ?>
                            <div class="user">
                                <a href="../Profile/OtherProfile.php?userID=<?= $row['userID'] ?>">
                                    <div class="circle">
                                        <?php if (!empty($row['profileIcon'])) { ?>
                                            <img src="<?= $row['profileIcon'] ?>" alt="profileIcon">
                                        <?php } else { ?>
                                            <img src="../image/DefaultIcon.svg" alt="profileIcon">
                                        <?php } ?>
                                    </div>
                                    <div class="nickname"><?= htmlspecialchars($row['nickname']) ?></div>
                                </a>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="noResult">
                            <p>一致するものがありませんでした</p>
                        </div>
                    <?php }
                } else { ?>
                    <div class="noSearch">
                        <p>キーワードを入力して検索してみてください</p>
                    </div>
                <?php } ?>
=======
                        foreach ($sql as $row) { 
                            if($row['userID'] != 'Anonymous'){ ?>
                                <div class="user" user-url="<?php echo 'OtherProfile.php?id=' . $row['userID']; ?>">
                                    <div class="circle">
                                        <?php
                                        if (!empty($row['profileIcon'])) {
                                            echo '<img src="' . $row['profileIcon'] . '" alt="profileIcon">';
                                        } else {
                                            echo '<img src="./image/DefaultIcon.svg" alt="profileIcon">';
                                        }
                                        ?>
                                    </div>
                                    <div class="nickname"><?php echo $row['nickname']; ?></div>
                                </div>
                        <?php }
                        }
                    } else {
                        echo '一致するユーザーがいませんでした。';
                    }
                } else {
                    $sql = $pdo->query('select * from Users');
                    foreach ($sql as $row) { 
                        if($row['userID'] != 'Anonymous'){ ?>
                            <div class="user" user-url="<?php echo 'OtherProfile.php?id=' . $row['userID']; ?>">
                                <div class="circle">
                                    <?php
                                    if (!empty($row['profileIcon'])) {
                                        echo '<img src="' . $row['profileIcon'] . '" alt="profileIcon">';
                                    } else {
                                        echo '<img src="./image/DefaultIcon.svg" alt="profileIcon">';
                                    }
                                    ?>
                                </div>
                                <div class="nickname"><?php echo $row['nickname']; ?></div>
                            </div>
                    <?php }
                    }
                } ?>
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
            </div>
            <div class="questionResult">
                <h2>質問</h2><br>
                <?php
<<<<<<< HEAD
                if (isset($search_query) && !empty($search_query)) {
                    $qstr = $qstr . '(questionText like ? or questionTitle like ?)';
                    $qkeyArray[0] = '%' . $search_query . '%';
                    $qkeyArray[1] = '%' . $search_query . '%';
=======
                if (!empty($search)) {
                    $qstr = $qstr . 'questionText like ?';
                    $qkeyArray[0] = '%' . $search . '%';
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
                    $sql = $pdo->prepare($qstr);
                    $sql->execute($qkeyArray);

                    if ($sql->rowCount() > 0) {
                        foreach ($sql as $row) { ?>
<<<<<<< HEAD
                            <div class="question">
                                <div class="profile">
                                    <a href="../Profile/OtherProfile.php?userID=<?= $row['userID'] ?>">
                                        <div class="circle">
                                            <?php if (!empty($row['profileIcon'])) { ?>
                                                <img src="<?= $row['profileIcon'] ?>" alt="profileIcon">
                                            <?php } else { ?>
                                                <img src="../image/DefaultIcon.svg" alt="profileIcon">
                                            <?php } ?>
                                        </div>
                                        <div class="nickname"><?= htmlspecialchars($row['nickname']) ?></div>
                                    </a>
                                </div>
                                <a href="./Detail.php?questionID=<?= $row['questionID'] ?>" class="questionLink">
                                    <div class="questionTitle"><?= htmlspecialchars($row['questionTitle']) ?></div>
                                </a>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="noResult">
                            <p>一致するものがありませんでした</p>
=======
                            <div class="question" question-url="<?php echo 'question.php?id=' . $row['questionID']; ?>">
                                <img src="image/icon.png" width="30" height="30">
                                <div class="userID"><?php echo $row['userID']; ?></div><br>
                                <div class="questionTitle"><?php echo $row['questionTitle']; ?></div><br>
                            </div>
                        <?php }
                    } else {
                        echo '一致する質問がありませんでした。';
                    }
                } else {
                    $sql = $pdo->query('select * from Question');
                    foreach ($sql as $row) { ?>
                        <div class="question" question-url="<?php echo 'question.php?id=' . $row['questionID']; ?>">
                            <img src="image/icon.png" width="30" height="30">
                            <div class="userID"><?php echo $row['userID']; ?></div><br>
                            <div class="questionTitle"><?php echo $row['questionTitle']; ?></div><br>
>>>>>>> 1f1c9b7552b0ad7983a86a7feab6eebf6eb37e83
                        </div>
                    <?php }
                } else { ?>
                    <div class="noSearch">
                        <p>キーワードを入力して検索してみてください</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>
</body>
</html>
