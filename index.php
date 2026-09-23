<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!empty($_SESSION["logged_in"]) || !empty($_SESSION["user_id"])) {
    if (($_SESSION["user_role"] ?? "") === "admin" || ($_SESSION["auth"]["user_type"] ?? "") === "internal") {
        header("Location: main_dashboard.php");
    } else {
        header("Location: client-portal-dashboard/index.php");
    }
    exit;
}

$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogixPulse Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h1>LogixPulse</h1>
        <p class="subtitle">Traditional Login</p>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="login-options">
            <a class="btn" href="internal_login.php">Internal / Team Login</a>
            <a class="btn secondary" href="client_login.php">Client Login</a>
            <a class="btn" style="background: #2563eb;" href="index.html">Client Portal Login</a>
        </div>

        <div class="demo-box">
            <strong>Demo accounts</strong>
            <p>Internal: admin@logixpulse.test / Admin@123</p>
            <p>Client: client@logixpulse.test / Client@123</p>
        </div>
    </div>
</div>
</body>
</html>
