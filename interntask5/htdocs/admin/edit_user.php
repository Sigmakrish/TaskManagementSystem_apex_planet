<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role_id"] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/db.php";

// Get user ID
$id = $_GET['id'] ?? 0;

// Fetch user data
$stmt = $mysqli->prepare("SELECT name, username, email, role_id FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $username, $email, $role_id);
$stmt->fetch();
$stmt->close();

$errors = [];
$success = "";

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $role_id = $_POST["role_id"];

    $stmt = $mysqli->prepare("UPDATE users SET name=?, username=?, email=?, role_id=? WHERE id=?");
    $stmt->bind_param("sssii", $name, $username, $email, $role_id, $id);
    $stmt->execute();
    $stmt->close();

    $success = "User updated successfully!";
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Edit User</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">

<div class="container">
    <h3>Edit User</h3>
    <a href="users.php" class="btn btn-secondary my-2">⬅ Back</a>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post">

        <label class="form-label">Full Name</label>
        <input class="form-control mb-2" name="name" value="<?= htmlspecialchars($name) ?>">

        <label class="form-label">Username</label>
        <input class="form-control mb-2" name="username" value="<?= htmlspecialchars($username) ?>">

        <label class="form-label">Email</label>
        <input class="form-control mb-2" name="email" value="<?= htmlspecialchars($email) ?>">

        <label class="form-label">Role</label>
        <select name="role_id" class="form-control mb-3">
            <option value="1" <?= $role_id==1?"selected":"" ?>>Admin</option>
            <option value="2" <?= $role_id==2?"selected":"" ?>>User</option>
        </select>

        <button class="btn btn-primary">Update</button>
    </form>
</div>

</body>
</html>
