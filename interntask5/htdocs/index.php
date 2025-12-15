<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION["user"];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body { background: #f4f6f9; }
    .dash-container {
        max-width: 900px;
        margin: 60px auto;
        text-align: center;
    }
    .card-box {
        border-radius: 15px;
        padding: 25px;
        transition: 0.2s;
        cursor: pointer;
    }
    .card-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }
    .icon {
        font-size: 40px;
        margin-bottom: 10px;
    }
</style>
</head>

<body>

<div class="dash-container">

    <?php
$user = $_SESSION["user"];

if ($user["role_id"] == 1) {
    $greetingName = $user["username"] . " (Admin👑)";
} else {
    $greetingName = $user["username"] . " (User🙋‍♂️)";
}
?>

<h2>Hello, <?= htmlspecialchars($greetingName) ?></h2>


    <div class="row g-4">

        <!-- User Tasks -->
        <div class="col-md-4">
            <a href="user/tasks.php" class="text-decoration-none text-dark">
                <div class="card card-box text-center">
                    <div class="icon">📝</div>
                    <h5 class="fw-bold">My Tasks</h5>
                    <p class="text-muted small">View, add, and manage your tasks.</p>
                </div>
            </a>
        </div>

        <!-- Admin Panel -->
        <?php if ($user["role_id"] == 1): ?>
        <div class="col-md-4">
            <a href="admin/dashboard.php" class="text-decoration-none text-dark">
                <div class="card card-box text-center">
                    <div class="icon">⚙️</div>
                    <h5 class="fw-bold">Admin Panel</h5>
                    <p class="text-muted small">Manage users & all tasks.</p>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <!-- Logout -->
        <div class="col-md-4">
            <a href="logout.php" class="text-decoration-none text-dark">
                <div class="card card-box text-center" style="background:#ffe5e5;">
                    <div class="icon">🚪</div>
                    <h5 class="fw-bold text-danger">Logout</h5>
                    <p class="text-muted small">Sign out of your account.</p>
                </div>
            </a>
        </div>

    </div>
</div>

</body>
</html>
