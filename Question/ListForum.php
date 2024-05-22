<main?php 
    session_start();
    require '../src/db-connect.php';
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
            <?php include '../Header/Header.html' ?>
            <div class="categorylist">
                <a href="#">生活</a>
                <a href="#">学校</a>
                <a href="#">勉強</a>
                <a href="#">テクノロジー</a>
                <a href="#">その他</a>
            </div>
      </header>
      <div class="container">
        <div class="control">
            <button class="post-question">質問を投稿する</button>

            <div class="post-search">
                <!-- 検索フォーム（検索画面が出来次第更新予定） -->
                <form action="検索への遷移" method="post">
                    <input type="text" autocomplete="off" aria-autocomplete="list" aria-controls="react-autowhatever-1" class="post-search-input" placeholder="キーワードを入力" name="PostSearch" value="" spellcheck="false" data-ms-editor="true">
                    <button type="submit" class="post-search-button">
                        <img src="../Header/Image/SearchIcon.svg" width="20" height="20" alt="検索">
                    </button>
                </form>
            </div>
        </div>

        <div class="questions">
            <div class="question">
                <div class="user-icon">

                </div>
                <div class="question-text">
                
                </div>
                <div class="actions">
                    <button class="like">👍</button>
                    <button class="smile"></button>
                </div>
            </div>
        </div>

        <div class="questions">
            <div class="question">
                <div class="user-icon">👤</div>
                <div class="question-text">レイアウトが思いつきません。どうしたらいいですか？</div>
                <div class="actions">
                    <button class="like">👍</button>
                    <button class="smile">😁</button>
                </div>
            </div>
        </div>
      </main>
      <footer>
          
      </footer>
  </body>
</html>
