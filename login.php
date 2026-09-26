<?php
/**
 * LogixPulse — Staff Login Handler
 * Returns plain text "login successful" or "login failed" as expected by app.js.
 * Demo credentials: alex.lawson@devlogix.io / Demo@1234
 */
session_start();

header('Content-Type: text/plain; charset=utf-8');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('method not allowed');
}

$email    = trim($_POST['email']    ?? '');
$password =      $_POST['password'] ?? '';

// ── Demo credentials (replace with DB lookup in production) ──────────────────
$DEMO_USERS = [
    'alex.lawson@devlogix.io' => [
        'password' => 'Demo@1234',
        'name'     => 'Alex Lawson',
        'role'     => 'Super Admin',
    ],
];

$user = $DEMO_USERS[$email] ?? null;

if ($user && $user['password'] === $password) {
    $_SESSION['lp_user']  = $email;
    $_SESSION['lp_name']  = $user['name'];
    $_SESSION['lp_role']  = $user['role'];
    echo 'login successful';
} else {
    http_response_code(401);
    echo 'login failed';
}
