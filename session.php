<?php
declare(strict_types=1);

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $sameSite = (string)env_value('SESSION_SAME_SITE', 'Lax');
    $secure = env_bool('SESSION_COOKIE_SECURE', true);
    $cookiePath = (string)env_value('SESSION_COOKIE_PATH', '/');
    $sessionName = (string)env_value('SESSION_NAME', 'CRM_SESSION');

    if (!in_array($sameSite, ['Lax', 'Strict', 'None'], true)) {
        $sameSite = 'Lax';
    }

    if ($sameSite === 'None') {
        $secure = true;
    }

    session_name($sessionName);

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => $cookiePath,
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => $sameSite,
    ]);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', $secure ? '1' : '0');
    ini_set('session.gc_maxlifetime', (string)env_int('SESSION_IDLE_TIMEOUT', 1800));

    session_start();

    enforce_session_expiration();
}

function enforce_session_expiration(): void
{
    if (empty($_SESSION['auth']['authenticated'])) {
        return;
    }

    $now = time();
    $idleTimeout = env_int('SESSION_IDLE_TIMEOUT', 1800);
    $absoluteTimeout = env_int('SESSION_ABSOLUTE_TIMEOUT', 28800);

    $lastActivity = (int)($_SESSION['auth']['last_activity'] ?? $now);
    $createdAt = (int)($_SESSION['auth']['created_at'] ?? $now);

    if (($now - $lastActivity) > $idleTimeout || ($now - $createdAt) > $absoluteTimeout) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool)$params['secure'], (bool)$params['httponly']);
        }
        session_destroy();
        return;
    }

    $_SESSION['auth']['last_activity'] = $now;
}
