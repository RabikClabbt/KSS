# チーム名：クBot システム ソリューションズ（KSS）
チームメンバー：

久保 雄太（L）

小林 夏己（SL）

井上 架（Mem）

川田 大地（Mem）

高野 颯馬（Mem）

平井 賢太（Mem）

# 企画名：Yadi-X(ヤディクス)
サービス名：Yadi-X(ヤディクス)

企画概要：コミュニケーション機能を有するWebサービスをテーマとしたシステム開発演習で、学生が気軽にコミュケーションやデータ共有、質問等を行える、学生向けSNSの開発

導入予定機能：チャット機能(必須機能)、ログイン機能、コメントに対する反応機能、スタンプ、投稿時の名前変更機能、ファイル添付機能、質問機能

導入検討機能：グループ作成機能、NGワード設定機能、通知機能、音声及びビデオ通話機能

使用ツール：<img src="https://img.shields.io/badge/-Eclipse-000000.svg?logo=eclipseide&style=plastic">
<img src="https://img.shields.io/badge/-Figma-000000.svg?logo=figma&style=plastic">
<img src="https://img.shields.io/badge/-Github-000000.svg?logo=github&style=plastic">

サーバ：<img src="https://img.shields.io/badge/-LOLIPOP-000000.svg?logo=LOLIPOP&style=plastic">

データベース：<img src="https://img.shields.io/badge/-Mysql-000000.svg?logo=mysql&style=plastic">

使用言語：<img src="https://img.shields.io/badge/-Html-000000.svg?logo=html5&style=plastic">
<img src="https://img.shields.io/badge/-Javascript-000000.svg?logo=javascript&style=plastic">
<img src="https://img.shields.io/badge/-Vue.js-000000.svg?logo=vue.js&style=plastic">
<img src="https://img.shields.io/badge/-PHP-000000.svg?logo=php&style=plastic">

使用予定言語：<img src="https://img.shields.io/badge/-Node.js-000000.svg?logo=Node.js&style=plastic">
<img src="https://img.shields.io/badge/-React-000000.svg?logo=React&style=plastic">

# GitHubの利用について
当リポジトリをクローン後、各自でブランチを作成してください。

ブランチ名は、名前_機能名など、明示的な名前にしてください。

ブランチの作成法については、「[ブランチを活用する方法](https://x.gd/DTn0g)」参照してください。

作成後は、ブランチからmasterへpull requestしてください。

エラーや問題が発生した際は、DiscordやGitHub Issuesなどで、知らせるか、直接聞いてください。

# Visual Stadio Codeの便利な拡張機能
おすすめの拡張機能の紹介

ChatGPT GPT-4 - Bito AI Code Assistant  VScode内でChatGPTを扱うことが出来る

Microsoft Edge Tools for VS Code  一々、ブラウザを開くことなく動作を確認できる

Tabnine: AI Autocomplete & Chat  Codeの続きを予測して表示してくれる。（Tabキーで確定できる）

GitHub Pull Requests  VScode内でpull requestやIssuesを行うことが出来る。

GitLens — Git supercharged　　よく分かんない…

# 命名規則について
ファイルや変数名などの命名規則についての説明です。

ファイルについては、大文字始まりで単語の区切りも大文字でお願いします。  例: ExsampleTextFile.txt

変数名は基本的に何を扱う変数なのかが分かるような命名にしてください。有効数字2桁を変数名の後ろに付ける、複数単語の変数名にする、単語の頭文字のみにするなど都度対応してください。
複数画面で扱うような変数の場合は、次項の変数名一覧に記載してください。

# 変数名一覧
LoginStatus - ユーザのログイン状態を示す。Char型で、「I」（ログイン）、「O」（ログアウト）の状態を示す。初期値は「O」でログイン後は「I」に変わる。

UserID - ユーザが登録時に入力したID

Nickname - プロフィール画面で変更が可能な表示名。デフォルトはUserID
