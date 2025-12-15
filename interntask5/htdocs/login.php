<?php
session_start();
require_once "config/db.php";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {

        if (!password_verify($password, $user["password"])) {
            $errors[] = "Incorrect password.";
        }
        // 🔐 BLOCK LOGIN IF EMAIL NOT VERIFIED
        else if ($user["is_verified"] == 0) {
            $errors[] = "Please verify your email before logging in.";
        }
        else {
            $_SESSION["user"] = $user;
            header("Location: index.php");
            exit;
        }

    } else {
        $errors[] = "Email not found.";
    }
}
?>

<!doctype html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body class="auth-body auth-bubbles">

<div class="auth-container">
    <div class="auth-card p-4 shadow-lg">

        <h3 class="auth-title text-center mb-3">Welcome Back 👋</h3>
        <p class="text-center text-muted small mb-4">Log in to manage your tasks.</p>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger auth-alert"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <?php if (isset($_GET["verified"])): ?>
            <div class="alert alert-success auth-alert">
                Email verified successfully. You can log in now.
            </div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control auth-input" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control auth-input" required>
            </div>

            <button class="auth-btn w-100">Login</button>
        </form>

        <p class="text-center mt-3 auth-footer">
            <a href="register.php" class="auth-link">Create an Account</a>
        </p>

    </div>
</div>

</body>
</html>
