<?php
declare(strict_types=1);

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: internal_login.php');
    exit;
}

$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    header('Location: internal_login.php?error=' . urlencode('Please enter a valid email and password.'));
    exit;
}

$stmt = $pdo->prepare(
    'SELECT id, name, email, password, role FROM internal_users WHERE email = :email AND status = "active" LIMIT 1'
);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    header('Location: internal_login.php?error=' . urlencode('Invalid email or password.'));
    exit;
}

loginUser($user, 'internal');
header('Location: main_dashboard.php');
exit;
