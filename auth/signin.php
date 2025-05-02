<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['uname'] == 'root' && $_POST['upass'] == 'toor') {
        $_SESSION['uname'] = 'root';
        header('Location: ../home.php');
        exit;
    }
    echo "Access Denied.";
}
?>
<form method="POST">
    User: <input name="uname"><br>
    Pass: <input type="password" name="upass"><br>
    <input type="submit" value="Log In">
</form>
