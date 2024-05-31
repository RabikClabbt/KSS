<?php 
    session_start();
    require '../src/db-connect.php';
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>タイトル</title>
      <link rel="stylesheet" type="text/css" href="./css/Detail.css">
  </head>
  <body>
      <header>
            <!-- Header.htmlを読み込む -->
            <div id="external-content"></div>
            <?php require '../Header/Header.html' ?>
            <div class="categorylist">
                <p>生活</p>
                <p>学校</p>
                <p>勉強</p>
                <p>テクノロジー</p>
                <p>その他</p>
            </div>
      </header>
      <main>
          <p>フタチマルの性は度量多くしてせんず</p>
          <h2>ここまでテンプレ</h2>
      </main>
      <footer>
          
      </footer>
  </body>
</html>