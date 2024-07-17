<?php 
session_start(); 
require 'db-connect.php'; 

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $_SESSION['search'] = $_POST['search'];
    header('Location: Search.php');
    exit();
}

// 検索キーワードの取得
$search = isset($_SESSION['search']) ? $_SESSION['search'] : '';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
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
</head>
<body>
    <header>
        <?php require 'Header.php'; ?>
    </header>
    <main>
        <form action="Search.php" method="post">
            <div class="Sfunction">
                <input type="text" placeholder="検索" name="search" class="text" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="button">
                    <img src="./image/SearchIcon.svg" alt="検索">
                </button>
            </div>
        </form>
        <?php
            $pdo = new PDO($connect, USER, PASS);
            $ustr = 'select * from Users where ';
            $qstr = 'select * from Question where ';
        ?>
        <div class="result">
            <div class="userResult">
                <h2>ユーザー</h2><br>
                <?php
                if (!empty($search)) {
                    $ustr = $ustr . 'userID like ?';
                    $ukeyArray[0] = '%' . $search . '%';
                    $sql = $pdo->prepare($ustr);
                    $sql->execute($ukeyArray);

                    if ($sql->rowCount() > 0) {
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
            </div>
            <div class="questionResult">
                <h2>質問</h2><br>
                <?php
                if (!empty($search)) {
                    $qstr = $qstr . 'questionText like ?';
                    $qkeyArray[0] = '%' . $search . '%';
                    $sql = $pdo->prepare($qstr);
                    $sql->execute($qkeyArray);

                    if ($sql->rowCount() > 0) {
                        foreach ($sql as $row) { ?>
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
                        </div>
                    <?php }
                }
                ?>
            </div>
        </div>
    </main>
</body>
</html>
