<?php
include '../includes/config.php';
$res = $conn->query("SELECT * FROM feedback");
while ($row = $res->fetch_assoc()) {
    echo "<strong>{$row['uname']}</strong>: {$row['message']}<hr>";
}
?>
