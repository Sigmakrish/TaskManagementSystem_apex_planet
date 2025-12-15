<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user"]["id"];

// Filters (used only for initial load)
$status = $_GET["status"] ?? "";
$search = $_GET["q"] ?? "";

// Pagination
$limit = 5;
$page = isset($_GET["page"]) ? max(1, intval($_GET["page"])) : 1;
$offset = ($page - 1) * $limit;

// Count total tasks
$count_sql = "SELECT COUNT(*) FROM tasks WHERE user_id=?";
$stmt = $mysqli->prepare($count_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total_tasks);
$stmt->fetch();
$stmt->close();

$total_pages = ceil($total_tasks / $limit);

// Fetch tasks
$sql = "SELECT * FROM tasks WHERE user_id=?";
$params = [$user_id];
$types = "i";

if ($status !== "") {
    $sql .= " AND status=?";
    $types .= "s";
    $params[] = $status;
}

if ($search !== "") {
    $sql .= " AND title LIKE ?";
    $types .= "s";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

function badge($status) {
    return match ($status) {
        "pending" =>
            '<span class="badge bg-warning text-dark">Pending</span>',

        "in_progress" =>
            '<span class="badge bg-info text-dark">In Progress</span>',

        "completed" =>
            '<span class="badge bg-success">Completed</span>',

        default =>
            '<span class="badge bg-secondary">Unknown</span>',
    };
}


?>

<!doctype html>
<html>
<head>
    <title>My Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>My Tasks</h2>
        <a href="../index.php" class="btn btn-secondary">Back</a>
    </div>

    <!-- FILTER FORM -->
    <div class="card shadow p-3 mb-4">
        <form id="filterForm" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Search task title...">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="add_task.php" class="btn btn-success w-100">+ Add Task</a>
            </div>
        </form>
    </div>

    <!-- TASK LIST (AJAX TARGET) -->
    <div id="taskList">
        <?php while ($task = $result->fetch_assoc()): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4><?= htmlspecialchars($task["title"]) ?></h4>
                        <?= badge($task["status"]) ?>
                    </div>

                    <p class="text-muted">
                        <?= htmlspecialchars(substr($task["description"], 0, 150)) ?>...
                    </p>

                    <?php if ($task["deadline"]): ?>
                        <span class="badge bg-danger">
                            Deadline: <?= htmlspecialchars($task["deadline"]) ?>
                        </span>
                    <?php endif; ?>

                    <div class="mt-3">
                        <a href="edit_task.php?id=<?= $task["id"] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete_task.php?id=<?= $task["id"] ?>"
                           
                           class="btn btn-danger btn-sm">Delete</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

</div>

<script src="../assets/js/app.js"></script>

</body>
</html>
