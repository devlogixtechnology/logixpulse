<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

$user = require_internal();

json_success([
    'message' => 'Internal CRM access granted.',
    'user' => [
        'id' => (int)$user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
        'user_type' => $user['user_type'],
    ],
]);
