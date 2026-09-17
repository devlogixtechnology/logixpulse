<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function require_login(): void
{
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void
{
    require_login();

    if (($_SESSION['user']['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Forbidden: admin access required.');
    }
}

function require_client(): void
{
    require_login();

    if (($_SESSION['user']['role'] ?? '') !== 'client') {
        http_response_code(403);
        exit('Forbidden: client access required.');
    }
}
