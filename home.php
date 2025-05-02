<?php
session_start();
if (!isset($_SESSION['uname'])) {
    header('Location: auth/signin.php');
    exit;
}
echo "Welcome, " . $_SESSION['uname'] . "!<br>";
echo "<a href='comments/post.php'>Leave Feedback</a> | ";
echo "<a href='comments/display.php'>View Feedback</a> | ";
echo "<a href='auth/signout.php'>Logout</a>";
?>
