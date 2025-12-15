<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role_id"] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

$id = $_GET['id'] ?? 0;

// prevent admin deleting themselves
if ($id == $_SESSION["user"]["id"]) {
    echo "<script>alert('You cannot delete yourself.'); window.location='users.php';</script>";
    exit;
}

$stmt = $mysqli->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: users.php");
exit;
?>
