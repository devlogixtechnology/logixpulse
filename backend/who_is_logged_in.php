<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/auth.php';

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
    'user'    => [
        'id'        => $user['id'] ?? null,
        'name'      => $user['name'] ?? '',
        'email'     => $user['email'] ?? '',
        'role'      => $user['role'] ?? '',
        'user_type' => $user['user_type'] ?? ($user['role'] ?? ''),
        'logged_at' => $user['logged_at'] ?? date('Y-m-d H:i:s'),
    ]
], JSON_PRETTY_PRINT);
