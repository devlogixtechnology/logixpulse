<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Requires an authenticated CRM user.
 * The normal CRM login should set $_SESSION['user_id'].
 */
function requireUserId(): int
{
    $userId = $_SESSION['user_id'] ?? null;

    if (!is_numeric($userId) || (int)$userId <= 0) {
        require_once __DIR__ . '/../helpers/response.php';

        jsonResponse([
            'success' => false,
            'message' => 'Authentication required. Please log in first.'
        ], 401);
    }

    return (int)$userId;
}
