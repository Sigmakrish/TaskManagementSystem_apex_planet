<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user"])) { header("Location: ../login.php"); exit; }

$user_id = $_SESSION["user"]["id"];
$id = intval($_GET["id"]);

$stmt = $mysqli->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

header("Location: tasks.php");
exit;
