<?php
session_start();
session_destroy();
header('Location: ../Top/TopPage.php'); // ログインページにリダイレクトします。適切なパスに変更してください。
exit();
?>