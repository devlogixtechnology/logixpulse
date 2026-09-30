<?php
declare(strict_types=1);

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_request(): void
{
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

    if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
        return;
    }

    $provided = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if ($provided === '') {
        $input = json_decode(file_get_contents('php://input'), true);
        $provided = is_array($input) ? (string)($input['_csrf_token'] ?? '') : '';
    }

    $expected = (string)($_SESSION['csrf_token'] ?? '');

    if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
        json_error(403, 'CSRF_TOKEN_INVALID', 'Invalid or missing CSRF token.');
    }
}
