<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function jsonResponse(bool $success, string $message, array $data = [], int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
    ], JSON_UNESCAPED_SLASHES);

    exit;
}

function requestBody(): array
{
    $raw = file_get_contents('php://input');
    $body = json_decode($raw ?: '{}', true);

    return is_array($body) ? $body : [];
}

function requiredString(array $body, string $key): string
{
    $value = trim((string)($body[$key] ?? ''));

    if ($value === '') {
        jsonResponse(false, "The field '{$key}' is required.", [], 422);
    }

    return $value;
}

function validEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function findUserByEmail(string $email): ?array
{
    $stmt = db()->prepare('SELECT id, name, email, password_hash, is_active FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);

    $user = $stmt->fetch();
    return $user ?: null;
}

function generateSixDigitCode(): string
{
    return (string) random_int(100000, 999999);
}

function generateSecureToken(): string
{
    return bin2hex(random_bytes(32));
}

function utcNow(): string
{
    return gmdate('Y-m-d H:i:s');
}

function futureUtc(int $minutes): string
{
    return gmdate('Y-m-d H:i:s', time() + ($minutes * 60));
}

function isExpired(string $expiresAt): bool
{
    return strtotime($expiresAt) <= time();
}