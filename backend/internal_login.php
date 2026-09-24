<?php
declare(strict_types=1);

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/auth.php';

if (isLoggedIn()) {
    $user = currentUser();
    $role = $user['role'] ?? $user['user_type'] ?? '';
    header('Location: ' . ($role === 'client' ? 'client_dashboard.php' : 'main_dashboard.php'));
    exit;
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Login - LogixPulse</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 40px 16px; }
        .container { max-width: 440px; margin: 0 auto; }
        .card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        h1 { margin-top: 0; font-size: 22px; margin-bottom: 6px; }
        .subtitle { color: #64748b; font-size: 14px; margin-bottom: 24px; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        label { display: block; font-weight: 500; font-size: 14px; margin-bottom: 6px; }
        input { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; margin-bottom: 16px; }
        input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
        .btn { width: 100%; padding: 11px; background: #4f46e5; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .btn:hover { background: #4338ca; }
        .switch { font-size: 13px; color: #64748b; text-align: center; margin-top: 20px; }
        .switch a { color: #4f46e5; text-decoration: none; }
        .back { display: inline-block; font-size: 13px; color: #64748b; text-decoration: none; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <a class="back" href="../index.html">← Back to Main</a>
        <h1>Internal Staff Login</h1>
        <p class="subtitle">Sign in with your team or admin account</p>

        <?php if ($error): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="process_internal_login.php" method="POST" autocomplete="on">
            <label for="email">Work Email</label>
            <input id="email" name="email" type="email" required autocomplete="username" placeholder="admin@logixpulse.com">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••">

            <button class="btn" type="submit">Sign In</button>
        </form>

        <p class="switch">Client account? <a href="client_login.php">Go to Client Sign In</a></p>
    </div>
</div>
</body>
</html>
