<?php
declare(strict_types=1);
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/helpers/auth.php';

if (isLoggedIn()) {
    header('Location: ' . (($_SESSION['auth']['user_type'] ?? '') === 'internal' ? 'main_dashboard.php' : 'client_dashboard.php'));
    exit;
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <div class="card form-card">
        <a class="back" href="index.php">← Back</a>
        <h1>Internal Login</h1>
        <p class="subtitle">Sign in with your team/user account</p>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="process_internal_login.php" method="POST" autocomplete="on">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required autocomplete="username" placeholder="team@example.com">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Password">

            <button class="btn" type="submit">Login</button>
        </form>

        <p class="switch">Client? <a href="client_login.php">Go to Client Login</a></p>
    </div>
</div>
</body>
</html>
