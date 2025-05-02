<?php
$conn = new mysqli("localhost", "root", "", "lab_xss");
if ($conn->connect_error) {
    die("DB Error");
}
?>
