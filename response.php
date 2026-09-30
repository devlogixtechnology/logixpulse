<?php
declare(strict_types=1);

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function json_error(int $status, string $code, string $message, array $extra = []): never
{
    json_response([
        'success' => false,
        'error' => array_merge([
            'code' => $code,
            'message' => $message,
        ], $extra),
    ], $status);
}

function json_success(array $data = [], int $status = 200): never
{
    json_response(array_merge(['success' => true], $data), $status);
}
