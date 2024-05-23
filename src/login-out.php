<?php
session_start();

require 'db-connect.php';

unset($_SESSION['users']);
$pdo = new PDO($connect , user , pass);
$sql = $pdo->prepare('select * from Users where userID=? || mailaddress=?');
$sql->execute([$_POST['mailid'],$_POST['mailid']]);
$row = $sql->fetch();

if ($row && password_verify($_POST['pass'], $row['password'])) {
    $_SESSION['users'] = [
        'id'   => $row['userID'],
        'mail' => $row['mailaddress'],
        'pass' => $row['password'],
        'name' => $row['nickname'],
        'icon' => $row['profileIcon']
    ];
    //header('Location: ???.php');
    exit;
} else {
    header('Location: login-again.php');
}
?>

<?php //require 'header.php'; ?>
<!--
<link rel="stylesheet" href="css/login.css">
-->
<?php
    /*require 'login_over.php';
if (isset($_SESSION['login_error'])) {
    echo '<p class="error">' . $_SESSION['login_error'] . '</p>';
    unset($_SESSION['login_error']); 
    require 'login_under.php';
}*/
?>