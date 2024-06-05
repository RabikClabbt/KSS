<?php require '../db-connect.php'; ?>
<?php require '../Header/Header.php'; ?>
<!DOCTYPE html>
<html lang="ja">
	<head>
    <meta http-equiv="Cache-Control" content="no-cache">
		<meta charset="UTF-8">
        <link rel="stylesheet" href="../css/UserInfoEdit.css">
		<title>プロフィール編集画面</title>
	</head>
	<body>
        <header id="header"></header>
        <h1 class="mainwhite">プロフィール変更</h1>
    <form action="profileinfo_edit.php" method="post" class="a">
            <h2 class="white">メールアドレス</h2>
            <div class="center">
                <input class="text-size" type="text" name="mail" id="">
            </div>
            <h2 class="white">パスワード</h2>
            <h3 class="minwhite">元のパスワード</h3>
            <div class="center">
                <input class="text-size" type="text" name="pass" id="">
            </div>
            <h3 class="minwhite">新しいパスワード</h3>
            <div class="center">
                <input class="text-size" type="text" name="new_pass" id="">
            </div>
            <h3 class="minwhite">確認</h3>
            <div class="center">
                <input class="text-size" type="text" name="kaku_pass" id="">
            </div>
            <div class="button">
                <input type="submit" value="保存">
            </div>
    </form>
    </table>
    </body>
</html>
