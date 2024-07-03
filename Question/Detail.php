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

if (isset($_GET['questionID'])) {
    $questionID = $_GET['questionID'];
    $sqlq = "SELECT q.userID, u.nickname, u.profileIcon, q.questionTitle, q.questionText, q.appendFile
            FROM Question q
            JOIN Users u ON q.userID = u.userID
            WHERE q.questionID = ?";
    $stmtq = $pdo->prepare($sqlq);
    $stmtq->execute([$questionID]);
    $question = $stmtq->fetch(PDO::FETCH_ASSOC);
} else {
    // questionIDが渡されていない場合の処理
    header("Location: ./ListForum.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <title><?= htmlspecialchars($question['questionTitle']) ?></title>
    <link rel="stylesheet" type="text/css" href="./css/Detail.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="./js/Detail.js"></script>
</head>
<body>
    <header>
        <!-- Header.htmlを読み込む -->
        <div id="external-content"></div>
        <?php require '../Header/Header.php' ?>
    </header>
    <div class="container">
        <main>
            <div class="questionHeader">
                <div class="questionHeaderLeft">
                    <div class="back" aria-label="戻る">
                        <a href="./ListForum.php">
                            <img src="../image/BackArrow.svg" alt="back">
                        </a>
                    </div>
                    <div class="questionTitle">
                        <p>質問名：<?= htmlspecialchars($question['questionTitle']) ?></p>
                    </div>
                </div>
                <div class="questionHeaderRight">
                    <div class="bestFlg">
                        <?php
                        $sql = "SELECT COUNT(CASE WHEN bestFlg = 1 THEN 1 END) AS resolved
                                FROM Answer
                                WHERE questionID = ?";
                        $stmtb = $pdo->prepare($sql);
                        $stmtb->execute([$questionID]);
                        $result = $stmtb->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <?php if ($result['resolved'] == 0) { ?>
                        <p>募集中</p>
                        <?php } else { ?>
                        <p>解決済</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="questionComments">
                <div class="questionDetail">
                    <div class="profile">
                        <!-- 相手側のユーザーページへのリンク -->
                        <a href="#">
                            <div class="circle">
                                <?php if (!empty($question['profileIcon'])) { ?>
                                    <img src="<?= $question['profileIcon'] ?>" alt="profileIcon">
                                <?php } else { ?>
                                    <img src="../image/DefaultIcon.svg" alt="profileIcon">
                                <?php } ?>
                            </div>
                        </a>
                        <div class="nickname"><?= htmlspecialchars($question['nickname']) ?></div>
                    </div>
                    <div class="questionText"><?= htmlspecialchars($question['questionText']) ?></div>
                    <?php if (!empty($question['appendFile'])) { ?>
                        <div class="appendFile">
                            <a href="<?= $question['appendFile'] ?>" target="_blank">添付ファイルを開く</a>
                        </div>
                    <?php } ?>
                    <!-- 回答用のボタン -->
                    <div class="controlBtn">
                        <button type="button" onclick="focusCommentInput('q', null)">
                            <img src="../image/ReplyArrow.svg" alt="reply">
                        </button>
                    </div>
                </div>
                <div class="answers">
                    <?php
                    $sqla = "SELECT a.userID, a.answerID, a.bestFlg, u.nickname, u.profileIcon, a.answerText, a.appendFile
                        FROM Answer a
                        JOIN Users u ON a.userID = u.userID
                        WHERE a.questionID = ?
                        ORDER BY a.answerID";
                    $stmta = $pdo->prepare($sqla);
                    $stmta->execute([$questionID]);
                    $answers = $stmta->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($answers as $answer) { ?>
                        <div class="answerDetail">
                            <div class="bestAnswer">
                                <?php if($answer['bestFlg'] == 1) { ?>
                                        <p>ベストアンサー</p>
                                <?php } ?>
                            </div>
                            <div class="profile" >
                                <a href="#">
                                    <div class="circle">
                                        <?php if (!empty($answer['profileIcon'])) { ?>
                                            <img src="<?= $answer['profileIcon'] ?>" alt="profileIcon">
                                        <?php } else { ?>
                                            <img src="../image/DefaultIcon.svg" alt="profileIcon">
                                        <?php } ?>
                                    </div>
                                </a>
                                <div class="nickname"><?= htmlspecialchars($answer['nickname']) ?></div>
                            </div>
                            <div class="questionText"><?= htmlspecialchars($answer['answerText']) ?></div>
                            <?php if (!empty($answer['appendFile'])) { ?>
                                <div class="appendFile">
                                    <a href="<?= $answer['appendFile'] ?>" target="_blank">添付ファイルを開く</a>
                                </div>
                            <?php } ?>
                            <!-- 返信用のボタン -->
                            <div class="controlBtn">
                                <button type="button" onclick="focusCommentInput('a', '<?php echo $answer['answerID']; ?>')">
                                    <img src="../image/ReplyArrow.svg" alt="reply">
                                </button>
                                <?php if(!empty($_SESSION) && $question['userID'] == $_SESSION['users']['id'] && $answer['bestFlg'] == 0) { ?>
                                <button type="button" onclick="bestAnswer(<?= $questionID ?>, <?= $answerID ?>)" aria-label="ベストアンサーに選ぶ">
                                    <img src="../image/BestFlg.svg" alt="best">
                                </button>
                                <?php } ?>
                            </div>
                        </div>
                        <?php
                        $sqlr = "SELECT r.userID, r.replyID, u.nickname, u.profileIcon, r.replyText, r.appendFile
                            FROM Reply r
                            JOIN Users u ON r.userID = u.userID
                            WHERE r.parentID = ? AND r.questionID = ? AND r.parentType = 'a'
                            ORDER BY r.replyID";
                        $stmtr = $pdo->prepare($sqlr);
                        $stmtr->execute([$answer['answerID'], $questionID]);
                        $replies = $stmtr->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="replies">
                            <?php foreach ($replies as $reply) { ?>
                                <div class="replyComment">
                                    <div class="profile">
                                        <a href="#">
                                            <div class="circle">
                                                <?php if (!empty($reply['profileIcon'])) { ?>
                                                    <img src="<?= $reply['profileIcon'] ?>" alt="profileIcon">
                                                <?php } else { ?>
                                                    <img src="../image/DefaultIcon.svg" alt="profileIcon">
                                                <?php } ?>
                                            </div>
                                        </a>
                                        <div class="nickname"><?= htmlspecialchars($reply['nickname']) ?></div>
                                    </div>
                                    <div class="questionText"><?= htmlspecialchars($reply['replyText']) ?></div>
                                    <?php if (!empty($reply['appendFile'])) { ?>
                                        <div class="appendFile">
                                            <a href="<?= $reply['appendFile'] ?>" target="_blank">添付ファイルを開く</a>
                                        </div>
                                    <?php } ?>
                                    <!-- 返信用のボタン -->
                                    <div class="controlBtn">
                                        <button type="button" onclick="focusCommentInput('r', '<?php echo $reply['replyID']; ?>')">
                                            <img src="../image/ReplyArrow.svg" alt="reply">
                                        </button>
                                    </div>
                                </div>
                                <div class="childReplies">
                                    <?php
                                    $sqlr = "SELECT r.userID, r.replyID, u.nickname, u.profileIcon, r.replyText, r.appendFile
                                        FROM Reply r
                                        JOIN Users u ON r.userID = u.userID
                                        WHERE r.parentID = ? AND r.questionID = ? AND r.parentType = 'r'
                                        ORDER BY r.replyID";
                                    $stmtr = $pdo->prepare($sqlr);
                                    $stmtr->execute([$reply['replyID'], $questionID]);
                                    $creplies = $stmtr->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($creplies as $creply) { ?>
                                        <div class="creplyComment">
                                            <div class="profile">
                                                <a href="#">
                                                    <div class="circle">
                                                        <?php if (!empty($creply['profileIcon'])) { ?>
                                                            <img src="<?= $creply['profileIcon'] ?>" alt="profileIcon">
                                                        <?php } else { ?>
                                                            <img src="../image/DefaultIcon.svg" alt="profileIcon">
                                                        <?php } ?>
                                                    </div>
                                                </a>
                                                <div class="nickname"><?= htmlspecialchars($creply['nickname']) ?></div>
                                            </div>
                                            <div class="questionText"><?= htmlspecialchars($creply['replyText']) ?></div>
                                            <?php if (!empty($creply['appendFile'])) { ?>
                                                <div class="appendFile">
                                                    <a href="<?= $creply['appendFile'] ?>" target="_blank">添付ファイルを開く</a>
                                                </div>
                                            <?php } ?>
                                            <!-- 返信用のボタン -->
                                            <div class="controlBtn">
                                                <button type="button" onclick="focusCommentInput('r', '<?php echo $creply['replyID']; ?>')">
                                                    <img src="../image/ReplyArrow.svg" alt="reply">
                                                </button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="sendComment">
                <div class="sendCommentContent">
                    <form action="" method="post" class="sendCommentForm">
                        <textarea type="text" autocomplete="off" aria-autocomplete="list" aria-controls="react-autowhatever-1"　
                        placeholder="<?php if(isset($_SESSION['users'])){ echo '質問へのコメントを送信する'; } else { echo 'ログインしてください'; } ?>" 
                        id="commentInput" name="comment" spellcheck="false" data-ms-editor="true"></textarea>
                        <div class="sendCommentButton">
                            <button id="sendCommentButton" onclick="sendComment('<?php echo $questionID; ?>')">
                                <img src="../image/SendIcon.svg" alt="send">
                            </button>
                            <button id="appendFileButton" onclick="sendFile()">
                                <img src="../image/FileIcon.svg" alt="appendFile">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
            <div class="postQuestionContainer">
                <button class="post-question" onclick="location.href='./PostForm.php'">質問を投稿する</button>
            </div>
        </side>
    </div>
    <footer>
        <!-- 将来的にFooter.htmlを読み込むかも -->
    </footer>
</body>
</html>