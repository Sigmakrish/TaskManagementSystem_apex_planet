<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user"])) { header("Location: ../login.php"); exit; }

$user_id = $_SESSION["user"]["id"];
$id = intval($_GET["id"]);

$stmt = $mysqli->prepare("SELECT * FROM tasks WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

if (!$task) {
    header("Location: tasks.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $deadline = $_POST["deadline"];
    $status = $_POST["status"];

    $stmt = $mysqli->prepare("UPDATE tasks SET title=?, description=?, deadline=?, status=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssssii", $title, $description, $deadline, $status, $id, $user_id);
    $stmt->execute();

    header("Location: tasks.php");
    exit;
}
?>

<!doctype html>
<html>
<head>
<title>Edit Task</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5 col-md-6">
    <div class="card shadow">
        <div class="card-body">
            <h3>Edit Task</h3>

            <form method="post">

                <div class="mb-3">
                    <label>Task Title</label>
                    <input name="title" class="form-control" value="<?= htmlspecialchars($task["title"]) ?>" required>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($task["description"]) ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Deadline</label>
                    <input type="date" name="deadline" class="form-control" value="<?= htmlspecialchars($task["deadline"]) ?>">
                </div>

                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" <?= $task["status"]=="pending"?"selected":"" ?>>Pending</option>
                        <option value="in_progress" <?= $task["status"]=="in_progress"?"selected":"" ?>>In Progress</option>
                        <option value="completed" <?= $task["status"]=="completed"?"selected":"" ?>>Completed</option>
                    </select>
                </div>

                <button class="btn btn-primary w-100">Update Task</button>

            </form>

            <a href="tasks.php" class="btn btn-secondary w-100 mt-3">Back</a>

        </div>
    </div>
</div>

</body>
</html>
