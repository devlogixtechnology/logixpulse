<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'No user is currently logged in.'
    ], JSON_PRETTY_PRINT);
    exit;
}

$user = currentUser();

echo json_encode([
    'success' => true,
    'message' => 'A user is currently logged in.',
    'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'user_type' => $user['user_type'],
        'logged_at' => $user['logged_at']
    ]
], JSON_PRETTY_PRINT);
