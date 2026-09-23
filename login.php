<?php
/**
 * login.php
 * BE-W7D1-3 — Traditional Login Checker (Core PHP only)
 *
 * Expects a POST request with:
 *   - email
 *   - password
 *
 * Checks credentials against the `users` table, verifies the password
 * with password_verify(), and on success stores the user's info and
 * role in a PHP session.
 */

session_start();

require __DIR__ . '/db.php';

// Only allow POST requests (normal form submission or Postman POST).
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'login failed';
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Basic input validation.
if ($email === '' || $password === '') {
    echo 'login failed';
    exit;
}

// Look up the user by email using a prepared statement.
$stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

// Verify the user exists and the password matches the stored hash.
if ($user && password_verify($password, $user['password'])) {

    // Regenerate session ID to prevent session fixation.
    session_regenerate_id(true);

    // Store login info and role in the session.
    $_SESSION['user_id']       = $user['id'];
    $_SESSION['user_name']     = $user['name'] ?? null;
    $_SESSION['user_email']    = $user['email'];
    $_SESSION['user_role']     = $user['role'];
    $_SESSION['logged_in']     = true;

    echo 'login successful';
} else {
    echo 'login failed';
}
