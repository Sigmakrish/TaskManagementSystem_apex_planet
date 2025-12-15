<?php
session_start();
require_once "../config/db.php";

// Only admin
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role_id"] != 1) {
    header("Location: ../login.php");
    exit;
}

// Fetch users
$result = $mysqli->query("
    SELECT id, name, username, email, role_id, created_at
    FROM users
    ORDER BY id DESC
");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Manage Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
<div class="container">

    <h2 class="mb-4">Admin: Manage Users</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Back to Dashboard</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($u = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $u["id"] ?></td>
                <td><?= htmlspecialchars($u["name"]) ?></td>
                <td><?= htmlspecialchars($u["username"]) ?></td>
                <td><?= htmlspecialchars($u["email"]) ?></td>
                <td><?= $u["role_id"] == 1 ? "Admin" : "User" ?></td>
                <td><?= $u["created_at"] ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_user.php?id=<?= $u['id'] ?>"
                       
                       class="btn btn-sm btn-danger">
                       Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>

    </table>
</div>
</body>
</html>
