<?php 
session_start(); 
require '../src/db-connect.php';

// POSTリクエストかつ検索クエリが存在する場合、セッションに保存してリダイレクト
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['search']) && !empty($_POST['search'])) {
        $_SESSION['search_query'] = $_POST['search'];
    } else {
        // 空の入力の場合はセッションをクリア
        unset($_SESSION['search_query']);
    }
    header('Location: Search.php');
    exit();
}

// セッションから検索クエリを取得
$search_query = isset($_SESSION['search_query']) ? $_SESSION['search_query'] : '';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/Search.css">
    <link rel="icon" href="../image/SiteIcon.svg" type="image/svg">
    <title>検索 | Yadi-X</title>
</head>
<body>
    <header>
        <?php require '../Header/Header.php'; ?>
    </header>
    <main>
        <form action="./Search.php" method="post">
            <div class="Sfunction">
                <input type="text" placeholder="検索" name="search" class="text">
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
                if (isset($search_query) && !empty($search_query)) {
                    $ustr = $ustr . 'nickname like ? AND userID <> "Anonymous"';
                    $ukeyArray[0] = '%' . $search_query . '%';
                    $sql = $pdo->prepare($ustr);
                    $sql->execute($ukeyArray);

                    if ($sql->rowCount() > 0) {
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
            </div>
            <div class="questionResult">
                <h2>質問</h2><br>
                <?php
                if (isset($search_query) && !empty($search_query)) {
                    $qstr = $qstr . '(questionText like ? or questionTitle like ?)';
                    $qkeyArray[0] = '%' . $search_query . '%';
                    $qkeyArray[1] = '%' . $search_query . '%';
                    $sql = $pdo->prepare($qstr);
                    $sql->execute($qkeyArray);

                    if ($sql->rowCount() > 0) {
                        foreach ($sql as $row) { ?>
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
                                <a href="../Question/Detail.php?questionID=<?= $row['questionID'] ?>" class="questionLink">
                                    <div class="questionTitle"><?= htmlspecialchars($row['questionTitle']) ?></div>
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
            </div>
        </div>
    </main>
</body>
</html>
