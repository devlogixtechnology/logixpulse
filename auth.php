<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/session.php')) {
    require_once __DIR__ . '/session.php';
} elseif (file_exists(__DIR__ . '/../config/session.php')) {
    require_once __DIR__ . '/../config/session.php';
} elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function loginUser(array $user, string $userType): void
{
    session_regenerate_id(true);

    $_SESSION['auth'] = [
        'id'         => (int)$user['id'],
        'name'       => $user['name'] ?? '',
        'email'      => $user['email'] ?? '',
        'role'       => $user['role'] ?? '',
        'user_type'  => $userType,
        'logged_in'  => true,
        'logged_at'  => date('Y-m-d H:i:s'),
    ];
    $_SESSION['user'] = $_SESSION['auth'];
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['auth']['logged_in']) || !empty($_SESSION['user']) || !empty($_SESSION['logged_in']);
}

function currentUser(): ?array
{
    return isLoggedIn() ? ($_SESSION['auth'] ?? $_SESSION['user'] ?? null) : null;
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

function require_login(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void
{
    require_login();
    $role = $_SESSION['auth']['role'] ?? $_SESSION['user']['role'] ?? $_SESSION['user_role'] ?? '';
    if ($role !== 'admin') {
        http_response_code(403);
        exit('Forbidden: admin access required.');
    }
}

function require_client(): void
{
    require_login();
    $role = $_SESSION['auth']['role'] ?? $_SESSION['user']['role'] ?? $_SESSION['user_role'] ?? '';
    if ($role !== 'client') {
        http_response_code(403);
        exit('Forbidden: client access required.');
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

