<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers/auth.php';
requireLogin('client');

$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <div class="card dashboard">
        <span class="badge client">Client</span>
        <h1>Client Dashboard</h1>
        <p>You are successfully logged in.</p>

        <div class="user-info">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
            <p><strong>User Type:</strong> <?= htmlspecialchars($user['user_type']) ?></p>
            <p><strong>Logged In At:</strong> <?= htmlspecialchars($user['logged_at']) ?></p>
        </div>

        <div class="actions">
            <a class="btn secondary" href="api/who_is_logged_in.php">Who Is Logged In Check</a>
            <a class="btn danger" href="logout.php">Logout</a>
        </div>
    </div>
</div>
</body>
</html>
