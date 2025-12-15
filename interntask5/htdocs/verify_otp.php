<?php
session_start();
require_once "config/db.php";

$error = "";
$email = $_GET["email"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $otp   = intval($_POST["otp"]); // ensure integer

    $stmt = $mysqli->prepare(
        "SELECT id FROM users 
         WHERE email=? AND otp=? AND is_verified=0 
         LIMIT 1"
    );
    $stmt->bind_param("si", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        // Mark user as verified
        $stmt = $mysqli->prepare(
            "UPDATE users SET is_verified=1, otp=NULL WHERE email=?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();

        header("Location: login.php?verified=1");
        exit;

    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!doctype html>
<html>
<head>
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body class="auth-body auth-bubbles">

<div class="auth-container">
    <div class="auth-card p-4">

        <h3 class="auth-title text-center mb-3">Verify Email</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger auth-alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

            <div class="mb-3">
                <label class="form-label">Enter OTP</label>
                <input type="text" name="otp" class="form-control auth-input" required>
            </div>

            <button class="auth-btn w-100">Verify</button>
        </form>

        <p class="text-center mt-3">
            <a href="register.php" class="auth-link">Register Again</a>
        </p>

    </div>
</div>

</body>
</html>

