<?php
/**
 * LogixPulse — Login API Endpoint
 *
 * Accepts POST with email + password.
 * Validates against the users table.
 * Only allows 'client' role users.
 * Sets session on success, returns JSON.
 */

session_start();

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Read input
 $email    = trim($_POST['email'] ?? '');
 $password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required']);
    exit();
}

try {
    $pdo = getDatabaseConnection();

    // Fetch user by email
    $stmt = $pdo->prepare('
        SELECT id, email, password_hash, first_name, last_name, role, status
        FROM users
        WHERE email = :email
    ');

    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // User not found
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid email or password']);
        exit();
    }

    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid email or password']);
        exit();
    }

    // Check active status
    if ($user['status'] !== 'active') {
        http_response_code(403);
        echo json_encode(['error' => 'Account is not active. Please contact support.']);
        exit();
    }

    // Restrict to client role only
    if ($user['role'] !== 'client') {
        http_response_code(403);
        echo json_encode(['error' => 'This portal is for clients only.']);
        exit();
    }

    // Set session
    $_SESSION['user_id']      = (int) $user['id'];
    $_SESSION['user_email']   = $user['email'];
    $_SESSION['user_name']    = trim($user['first_name'] . ' ' . $user['last_name']);
    $_SESSION['user_role']    = $user['role'];
    $_SESSION['logged_in']    = true;
    $_SESSION['login_time']   = time();

    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);

    // Success
    echo json_encode([
        'success'  => true,
        'redirect' => 'client-portal-dashboard/index.php'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to process login. Please try again later.']);
}