<?php
declare(strict_types=1);

function require_auth(?array $allowedRoles = null, ?string $requiredUserType = null): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        start_secure_session();
    }

    $auth = $_SESSION['auth'] ?? null;

    if (!is_array($auth) || empty($auth['authenticated']) || empty($auth['user_id'])) {
        json_error(401, 'UNAUTHORIZED', 'Authentication required.');
    }

    $userId = (int)$auth['user_id'];

    try {
        $stmt = Database::connection()->prepare(
            'SELECT id, email, role, user_type, is_active FROM users WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();
    } catch (PDOException) {
        json_error(503, 'DATABASE_UNAVAILABLE', 'Unable to validate the active session.');
    }

    if (!$user || !(bool)$user['is_active']) {
        logout_session();
        json_error(401, 'UNAUTHORIZED', 'The user session is no longer active.');
    }

    if (
        $requiredUserType !== null &&
        !hash_equals($requiredUserType, (string)$user['user_type'])
    ) {
        json_error(403, 'FORBIDDEN', 'You do not have permission to access this resource.');
    }

    if (
        $allowedRoles !== null &&
        !in_array((string)$user['role'], $allowedRoles, true)
    ) {
        json_error(403, 'FORBIDDEN', 'You do not have permission to access this resource.');
    }

    $_SESSION['auth']['role'] = (string)$user['role'];
    $_SESSION['auth']['user_type'] = (string)$user['user_type'];
    $_SESSION['auth']['last_activity'] = time();

    return $user;
}

function require_internal(array $allowedRoles = ['admin', 'manager', 'staff']): array
{
    return require_auth($allowedRoles, 'staff');
}

function require_client(array $allowedRoles = ['client']): array
{
    return require_auth($allowedRoles, 'client');
}

function login_session(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['auth'] = [
        'authenticated' => true,
        'user_id' => (int)$user['id'],
        'email' => (string)$user['email'],
        'role' => (string)$user['role'],
        'user_type' => (string)$user['user_type'],
        'created_at' => time(),
        'last_activity' => time(),
        'privilege_version' => 1,
    ];

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function rotate_session_after_privilege_escalation(string $newRole): void
{
    if (empty($_SESSION['auth']['authenticated'])) {
        json_error(401, 'UNAUTHORIZED', 'Authentication required.');
    }

    session_regenerate_id(true);
    $_SESSION['auth']['role'] = $newRole;
    $_SESSION['auth']['privilege_version'] =
        (int)($_SESSION['auth']['privilege_version'] ?? 1) + 1;
    $_SESSION['auth']['last_activity'] = time();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function logout_session(): void
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
