<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user"])) { header("Location: ../login.php"); exit; }

$user_id = $_SESSION["user"]["id"];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $deadline = $_POST["deadline"];

    if ($title == "") $errors[] = "Title is required.";

    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO tasks (user_id, title, description, deadline) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $description, $deadline);
        $stmt->execute();

        header("Location: tasks.php");
        exit;
    }
}
?>

<!doctype html>
<html>
<head>
<title>Add Task</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5 col-md-6">

    <div class="card shadow">
        <div class="card-body">
            <h3>Add New Task</h3>

            <?php foreach($errors as $e): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>

            <form method="post">

                <div class="mb-3">
                    <label>Task Title</label>
                    <input name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>

                <div class="mb-3">
                    <label>Deadline</label>
                    <input type="date" name="deadline" class="form-control">
                </div>

                <button class="btn btn-success w-100">Save Task</button>

            </form>

            <a href="tasks.php" class="btn btn-secondary w-100 mt-3">Back</a>
        </div>
    </div>

</div>

</body>
</html>
