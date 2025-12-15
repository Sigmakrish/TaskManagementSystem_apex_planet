<?php
session_start();
require_once "../config/db.php";

/* ---------------- SECURITY CHECK ---------------- */
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role_id"] != 1) {
    header("Location: ../login.php");
    exit;
}

/* ---------------- ANALYTICS QUERIES ---------------- */

// Task counts by status
$stats = [
    "pending" => 0,
    "in_progress" => 0,
    "completed" => 0
];

$statuses = ["pending", "in_progress", "completed"];

foreach ($statuses as $s) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM tasks WHERE status=?");
    $stmt->bind_param("s", $s);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stats[$s] = $count;
    $stmt->close();
}

// Total users
$userCount = 0;
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM users");
$stmt->execute();
$stmt->bind_result($userCount);
$stmt->fetch();
$stmt->close();

// Total tasks
$taskCount = 0;
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM tasks");
$stmt->execute();
$stmt->bind_result($taskCount);
$stmt->fetch();
$stmt->close();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Dashboard</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    body {
        background: #f4f6f9;
    }
    .admin-container {
        max-width: 1000px;
        margin: 50px auto;
    }
    .card-box {
        padding: 25px;
        border-radius: 15px;
        transition: 0.2s ease;
        cursor: pointer;
    }
    .card-box:hover {
        transform: translateY(-6px);
        box-shadow: 0 4px 14px rgba(0,0,0,.15);
    }
    .icon {
        font-size: 42px;
    }
</style>
</head>

<body>

<div class="admin-container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Admin Dashboard</h2>
        
    </div>

    <!-- SUMMARY CARDS -->
    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="card card-box text-center bg-white">
                <div class="icon">👤</div>
                <h5 class="fw-bold mt-2"><?= $userCount ?></h5>
                <p class="text-muted mb-0">Total Users</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-box text-center bg-white">
                <div class="icon">📝</div>
                <h5 class="fw-bold mt-2"><?= $taskCount ?></h5>
                <p class="text-muted mb-0">Total Tasks</p>
            </div>
        </div>

        <div class="col-md-4">
            <a href="../index.php" class="text-decoration-none text-dark">
                <div class="card card-box text-center bg-white">
                    <div class="icon">⬅️</div>
                    <h6 class="fw-bold mt-2">Back to Home</h6>
                </div>
            </a>
        </div>

    </div>

    <!-- MANAGEMENT LINKS -->
    <div class="row g-4 mb-5">

        <div class="col-md-6">
            <a href="manage_tasks.php" class="text-decoration-none text-dark">
                <div class="card card-box text-center">
                    <div class="icon">📋</div>
                    <h5 class="fw-bold">Manage Tasks</h5>
                    <p class="text-muted small">View, edit & delete all tasks</p>
                </div>
            </a>
        </div>

        <div class="col-md-6">
            <a href="users.php" class="text-decoration-none text-dark">
                <div class="card card-box text-center">
                    <div class="icon">🧑‍💼</div>
                    <h5 class="fw-bold">Manage Users</h5>
                    <p class="text-muted small">View, edit & delete users</p>
                </div>
            </a>
        </div>

    </div>

    <!-- ANALYTICS CHART -->
    <div class="card p-4 shadow-sm">
        <h5 class="fw-bold mb-3">Task Status Overview</h5>
        <canvas id="taskChart"></canvas>
    </div>

</div>

<script>
const ctx = document.getElementById('taskChart');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Pending', 'In Progress', 'Completed'],
        datasets: [{
            data: [
                <?= $stats["pending"] ?>,
                <?= $stats["in_progress"] ?>,
                <?= $stats["completed"] ?>
            ],
            backgroundColor: ['#ffc107', '#0dcaf0', '#198754']
        }]
    },
    options: {
        responsive: true
    }
});
</script>

</body>
</html>
