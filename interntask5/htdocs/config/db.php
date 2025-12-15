<?php
$mysqli = new mysqli("localhost", "root", "", "apex_task4");

if ($mysqli->connect_errno) {
    die("Database connection failed: " . $mysqli->connect_error);
}
?>
