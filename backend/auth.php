<?php
declare(strict_types=1);

/**
 * LogixPulse — Authentication & Access Control Helper
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Log a user in and initialize session variables across all conventions.
 */
function loginUser(array $user, string $userType = 'client'): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }

    $id = (int)($user['id'] ?? 0);
    $email = (string)($user['email'] ?? '');
    $role = (string)($user['role'] ?? $userType);
    $name = trim((string)($user['name'] ?? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))));

    $_SESSION['auth'] = [
        'id'        => $id,
        'name'      => $name,
        'email'     => $email,
        'role'      => $role,
        'user_type' => $userType,
        'logged_in' => true,
        'logged_at' => date('Y-m-d H:i:s'),
    ];

    $_SESSION['user'] = $_SESSION['auth'];

    // Flat session variables for older scripts
    $_SESSION['user_id']    = $id;
    $_SESSION['client_id']  = $id;
    $_SESSION['user_name']  = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role']  = $role;
    $_SESSION['logged_in']  = true;
    $_SESSION['login_time'] = time();
}

/**
 * Check if any user is currently authenticated.
 */
function isLoggedIn(): bool
{
    return !empty($_SESSION['logged_in']) 
        || !empty($_SESSION['auth']['logged_in']) 
        || !empty($_SESSION['user']['logged_in']);
}

/**
 * Return current user information or null.
 */
function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    if (!empty($_SESSION['auth'])) {
        return $_SESSION['auth'];
    }

    if (!empty($_SESSION['user'])) {
        return $_SESSION['user'];
    }

    return [
        'id'        => $_SESSION['user_id'] ?? 0,
        'name'      => $_SESSION['user_name'] ?? '',
        'email'     => $_SESSION['user_email'] ?? '',
        'role'      => $_SESSION['user_role'] ?? 'client',
        'user_type' => $_SESSION['user_role'] ?? 'client',
        'logged_at' => isset($_SESSION['login_time']) ? date('Y-m-d H:i:s', $_SESSION['login_time']) : date('Y-m-d H:i:s'),
    ];
}

/**
 * Enforce authentication or redirect.
 */
function requireLogin(?string $userType = null): void
{
    if (!isLoggedIn()) {
        header('Location: ../index.html');
        exit;
    }

    if ($userType !== null) {
        $user = currentUser();
        $role = $user['role'] ?? $user['user_type'] ?? '';
        if ($role !== $userType && $role !== 'admin') {
            http_response_code(403);
            exit('Access denied.');
        }
    }
}

function require_login(): void
{
    requireLogin();
}

function require_admin(): void
{
    if (!isLoggedIn()) {
        header('Location: ../index.html');
        exit;
    }

    $user = currentUser();
    $role = $user['role'] ?? '';
    if ($role !== 'admin' && $role !== 'administrator' && $role !== 'executive') {
        http_response_code(403);
        exit('Forbidden: admin access required.');
    }
}

function require_client(): void
{
    if (!isLoggedIn()) {
        header('Location: ../index.html');
        exit;
    }

    $user = currentUser();
    $role = $user['role'] ?? '';
    if ($role !== 'client' && $role !== 'admin') {
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
