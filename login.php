<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

if (!empty($_SESSION['user'])) {
    redirect($_SESSION['user']['role'] === 'admin' ? 'admin/index.php' : 'client/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email and password.';
    } else {
        $stmt = db()->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            unset($user['password_hash']);
            $_SESSION['user'] = $user;
            redirect($user['role'] === 'admin' ? 'admin/index.php' : 'client/index.php');
        }

        $error = 'Invalid login details.';
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Invoice Portal Login</title><link rel="stylesheet" href="assets/style.css"></head>
<body>
<div class="card login">
    <h1>Invoice Portal</h1>
    <p class="muted">Login as admin or client.</p>
    <?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>
    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required autocomplete="username">
        <label>Password</label>
        <input type="password" name="password" required autocomplete="current-password">
        <button type="submit">Login</button>
    </form>
    <hr>
    <p class="small"><b>Demo admin:</b> admin@example.com / admin123</p>
    <p class="small"><b>Client A:</b> clienta@example.com / client123</p>
    <p class="small"><b>Client B:</b> clientb@example.com / client456</p>
</div>
</body>
</html>
