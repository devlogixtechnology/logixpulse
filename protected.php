<?php
require_once __DIR__ . '/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Protected Page</title>
</head>
<body>
    <h2>Protected Page</h2>
    <p>Logged-in user: <?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user_email'] ?? 'User') ?></p>
</body>
</html>
