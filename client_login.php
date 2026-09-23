<?php
declare(strict_types=1);
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/helpers/auth.php';

if (isLoggedIn()) {
    header('Location: ' . (($_SESSION['auth']['user_type'] ?? '') === 'client' ? 'client_dashboard.php' : 'main_dashboard.php'));
    exit;
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <div class="card form-card">
        <a class="back" href="index.php">← Back</a>
        <h1>Client Login</h1>
        <p class="subtitle">Sign in with your client account</p>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="process_client_login.php" method="POST" autocomplete="on">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required autocomplete="username" placeholder="client@example.com">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Password">

            <button class="btn secondary" type="submit">Login</button>
        </form>

        <p class="switch">Team/User? <a href="internal_login.php">Go to Internal Login</a></p>
    </div>
</div>
</body>
</html>
