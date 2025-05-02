<?php
include '../includes/config.php';
$uname = $_POST['uname'];
$message = $_POST['message'];
$conn->query("INSERT INTO feedback (uname, message) VALUES ('$uname', '$message')");
echo "Tersimpan! <a href='display.php'>Lihat Semua</a>";
?>
