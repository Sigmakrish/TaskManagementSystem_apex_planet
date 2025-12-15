<?php
session_start();

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');
error_reporting(E_ALL);

require_once "config/db.php";
require_once "config/mail.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 🔴 HARD CHECK: ensure POST keys exist
    if (!isset($_POST["email"])) {
        $errors[] = "Email field missing in request.";
    } else {

        $name     = trim($_POST["name"] ?? "");
        $emailRaw = trim($_POST["email"] ?? "");
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        $password2 = $_POST["password2"] ?? "";

        // 🔎 LOG what server actually receives
        file_put_contents(
            __DIR__ . '/error_log.txt',
            "POST email value: " . var_export($emailRaw, true) . PHP_EOL,
            FILE_APPEND
        );

        if ($password !== $password2) {
            $errors[] = "Passwords do not match.";
        }

        // ✅ Validate email properly
        $email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $errors[] = "Invalid email address.";
        }

        // Check existing user
        if (empty($errors)) {
            $stmt = $mysqli->prepare(
                "SELECT id FROM users WHERE email=? OR username=? LIMIT 1"
            );
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errors[] = "Email or username already exists.";
            }
        }

        if (empty($errors)) {

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role_id = 2;
            $otp = rand(100000, 999999);

            // Insert user
            $stmt = $mysqli->prepare(
                "INSERT INTO users 
                (name, username, email, password, role_id, otp, is_verified)
                VALUES (?,?,?,?,?,?,0)"
            );
            $stmt->bind_param(
                "ssssii",
                $name,
                $username,
                $email,
                $hash,
                $role_id,
                $otp
            );
            $stmt->execute();

            // SEND OTP
            if (!sendOtpMail($email, $otp)) {
                $errors[] = "Failed to send OTP email.";
            } else {
                header("Location: verify_otp.php?email=" . urlencode($email));
                exit;
            }
        }
    }
}
?>


<!doctype html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body class="auth-body auth-bubbles">

<div class="auth-container">
    <div class="auth-card p-4">

        <h3 class="auth-title text-center mb-2">Create Account ✨</h3>
        <p class="text-center text-muted small mb-4">
            Join us and start managing your tasks.
        </p>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-danger auth-alert"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <form method="post">

            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input name="name" class="form-control auth-input" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input name="username" class="form-control auth-input" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control auth-input" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control auth-input" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Confirm Password</label>
                <input type="password" name="password2" class="form-control auth-input" required>
            </div>

            <button class="auth-btn w-100 mt-2">Register</button>
        </form>

        <p class="text-center mt-3 auth-footer">
            Already have an account?
            <a href="login.php" class="auth-link">Login</a>
        </p>

    </div>
</div>

</body>
</html>
