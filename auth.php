<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';

function loginUser(array $user, string $userType): void
{
    session_regenerate_id(true);

    $_SESSION['auth'] = [
        'id'         => (int)$user['id'],
        'name'       => $user['name'],
        'email'      => $user['email'],
        'role'       => $user['role'],
        'user_type'  => $userType,
        'logged_in'  => true,
        'logged_at'  => date('Y-m-d H:i:s'),
    ];
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['auth']['logged_in']);
}

function currentUser(): ?array
{
    return isLoggedIn() ? $_SESSION['auth'] : null;
}

function requireLogin(?string $userType = null): void
{
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }

    if ($userType !== null && ($_SESSION['auth']['user_type'] ?? '') !== $userType) {
        http_response_code(403);
        exit('Access denied.');
    }
}

function logoutUser(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            (bool)$params['secure'],
            (bool)$params['httponly']
        );
    }

    session_destroy();
}
