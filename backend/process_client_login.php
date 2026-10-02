<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: client_login.php');
    exit;
}

$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    header('Location: client_login.php?error=' . urlencode('Please enter a valid email and password.'));
    exit;
}

$pdo = getDatabaseConnection();

// Try users table first
$stmt = $pdo->prepare(
    "SELECT id, 
            COALESCE(NULLIF(name, ''), CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) AS name,
            email, password_hash, role, status
     FROM users 
     WHERE email = :email AND status = 'active' LIMIT 1"
);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {
    loginUser($user, 'client');
    header('Location: client_dashboard.php');
    exit;
}

// Fallback to clients table if present
try {
    $stmt2 = $pdo->prepare(
        "SELECT id, name, email, password, role FROM clients WHERE email = :email AND status = 'active' LIMIT 1"
    );
    $stmt2->execute(['email' => $email]);
    $client = $stmt2->fetch(PDO::FETCH_ASSOC);

    if ($client && password_verify($password, $client['password'])) {
        loginUser($client, 'client');
        header('Location: client_dashboard.php');
        exit;
    }
} catch (Throwable $e) {
    // Ignore table missing error
}

header('Location: client_login.php?error=' . urlencode('Invalid email or password.'));
exit;
