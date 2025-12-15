<?php
// logout.php
session_start();

// Unset all of the session variables.
$_SESSION = [];

// If there's a session cookie, delete it too (helps fully log out).
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"] ?? false,
        $params["httponly"] ?? true
    );
}

// Destroy the session.
session_destroy();

// Optional: clear a custom 'remember me' cookie if you used one
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect to login page (or homepage)
header("Location: login.php");
exit;
