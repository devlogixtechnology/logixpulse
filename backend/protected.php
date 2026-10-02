<?php
declare(strict_types=1);

/**
 * BE-W7D2-2 — Example Protected Page
 */

require_once __DIR__ . '/auth_check.php';

$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Protected Page - LogixPulse</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; padding: 40px; }
        .card { background: #fff; max-width: 500px; margin: 0 auto; padding: 32px; border-radius: 12px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Protected Area</h1>
        <p>Welcome, <strong><?= htmlspecialchars($user['name'] ?? 'User') ?></strong>!</p>
        <p style="color: #64748b; font-size: 14px;">Your session is authenticated (Role: <?= htmlspecialchars($user['role'] ?? '') ?>).</p>
        <p><a href="../auth/logout.php">Log Out</a></p>
    </div>
</body>
</html>
