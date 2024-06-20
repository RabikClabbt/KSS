<?php 
session_start(); 
require 'db-connect.php'; 
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/search.css">
    <title>検索</title>
</head>
<body>
    <header>
        <?php require 'Header.php'; ?>
    </header>
    <main>
        <form action="Search.php" method="post">
            <div class="Sfunction">
                <input type="text" placeholder="検索" name="search" class="text">
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
                if (isset($_POST['search'])) {
                    $ustr = $ustr . 'userID like ?';
                    $ukeyArray[0] = '%' . $_POST['search'] . '%';
                    $sql = $pdo->prepare($ustr);
                    $sql->execute($ukeyArray);

                    if ($sql->rowCount() > 0) {
                        foreach ($sql as $row) { ?>
                            <div class="user">
                                <div class="circle">
                                    <?php
                                    if (!empty($row['profileIcon'])) {
                                        echo '<img src="' . $row['profileIcon'] . '" alt="profileIcon">';
                                    } else {
                                        echo '<img src="./image/DefaultIcon.svg" alt="profileIcon">';
                                    }
                                    ?>
                                </div>
                                <div class="nickname"><?= $row['nickname']; ?></div>
                            </div>
                        <?php }
                    } else {
                        echo '一致するものがありませんでした。';
                    }
                } else {
                    $sql = $pdo->query('select * from Users');
                    foreach ($sql as $row) { ?>
                        <div class="user">
                            <div class="circle">
                                <?php
                                if (!empty($row['profileIcon'])) {
                                    echo '<img src="' . $row['profileIcon'] . '" alt="profileIcon">';
                                } else {
                                    echo '<img src="./image/DefaultIcon.svg" alt="profileIcon">';
                                }
                                ?>
                            </div>
                            <div class="nickname"><?= $row['nickname']; ?></div>
                        </div>
                    <?php }
                } ?>
            </div>
            <div class="questionResult">
                <h2>質問</h2><br>
                <?php
                if (isset($_POST['search'])) {
                    $qstr = $qstr . 'questionText like ?';
                    $qkeyArray[0] = '%' . $_POST['search'] . '%';
                    $sql = $pdo->prepare($qstr);
                    $sql->execute($qkeyArray);

                    if ($sql->rowCount() > 0) {
                        foreach ($sql as $row) { ?>
                            <div class="question">
                                <img src="image/icon.png" width="30" height="30"><br>
                                <div class="userID"><?= $row['userID']; ?></div><br>
                                <div class="questionTitle"><?= $row['questionTitle']; ?></div><br>
                                <div class="questionText"><?= $row['questionText']; ?></div><br>
                            </div>
                        <?php }
                    } else {
                        echo '一致するものがありませんでした。';
                    }
                } else {
                    $sql = $pdo->query('select * from Question');
                    foreach ($sql as $row) { ?>
                        <div class="question">
                            <img src="image/icon.png" width="30" height="30"><br>
                            <div class="userID"><?= $row['userID']; ?></div><br>
                            <div class="questionTitle"><?= $row['questionTitle']; ?></div><br>
                            <div class="questionText"><?= $row['questionText']; ?></div><br>
                        </div>
                    <?php }
                }
                ?>
            </div>
        </div>
    </main>
</body>
</html>
