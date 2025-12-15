<?php
session_start();
require_once "../config/db.php";

/* ===== Allow ONLY admin ===== */
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role_id"] != 1) {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

/* ===== Fetch task ===== */
$stmt = $mysqli->prepare("SELECT * FROM tasks WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();
$stmt->close();

if (!$task) {
    die("Task not found.");
}

/* ===== Update task ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title       = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $deadline    = $_POST["deadline"];
    $status      = $_POST["status"];

    // 🔒 HARD VALIDATION (VERY IMPORTANT)
    $allowedStatus = ["pending", "in_progress", "completed"];
    if (!in_array($status, $allowedStatus)) {
        $status = "pending";
    }

    $stmt = $mysqli->prepare("
        UPDATE tasks 
        SET title=?, description=?, deadline=?, status=? 
        WHERE id=?
    ");
    $stmt->bind_param("ssssi", $title, $description, $deadline, $status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_tasks.php");
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Edit Task (Admin)</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">

        <h3 class="mb-4">✏️ Edit Task (Admin)</h3>

        <form method="post">

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input
                    type="text"
                    name="title"
                    class="form-control"
                    value="<?= htmlspecialchars($task["title"]) ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea
                    name="description"
                    class="form-control"
                    rows="4"
                ><?= htmlspecialchars($task["description"]) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Deadline</label>
                <input
                    type="date"
                    name="deadline"
                    class="form-control"
                    value="<?= htmlspecialchars($task["deadline"]) ?>"
                    required
                >
            </div>

            <div class="mb-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="pending" <?= $task["status"]==="pending" ? "selected" : "" ?>>
                        Pending
                    </option>
                    <option value="in_progress" <?= $task["status"]==="in_progress" ? "selected" : "" ?>>
                        In Progress
                    </option>
                    <option value="completed" <?= $task["status"]==="completed" ? "selected" : "" ?>>
                        Completed
                    </option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success px-4">
                    Save Changes
                </button>
                <a href="manage_tasks.php" class="btn btn-secondary px-4">
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
