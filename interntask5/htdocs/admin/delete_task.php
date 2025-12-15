<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role_id"] != 1) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"] ?? 0;

if ($id) {
    $stmt = $mysqli->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: manage_tasks.php");
exit;
