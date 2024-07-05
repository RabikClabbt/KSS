<?php
session_start();
session_destroy();
header('Location: login.php'); // ログインページにリダイレクトします。適切なパスに変更してください。
exit();
?>