<?php
declare(strict_types=1);

/**
 * Security helpers for the agreement-signing pipeline.
 */

function jsonResponse(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function getClientIp(): string
{
    // Do not blindly trust X-Forwarded-For because it can be spoofed.
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function getUserAgent(): string
{
    return substr((string)($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'), 0, 1000);
}

function generateAuditCertificate(
    int $contractId,
    string $contractTokenHash,
    string $signerName,
    string $signatureSha256,
    string $ip,
    string $userAgent,
    string $timestamp
): array {
    $certificate = [
        'certificate_version' => '1.0',
        'contract_id' => $contractId,
        'contract_token_hash' => $contractTokenHash,
        'signer_legal_name' => $signerName,
        'signature_sha256' => $signatureSha256,
        'signer_ip' => $ip,
        'user_agent' => $userAgent,
        'executed_at' => $timestamp,
    ];

    $certificate['certificate_sha256'] = hash(
        'sha256',
        json_encode($certificate, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );

    return $certificate;
}

function safeClientDirectory(string $clientId): string
{
    // Only allow a predictable safe directory name.
    $safe = preg_replace('/[^A-Za-z0-9_-]/', '_', $clientId);
    $safe = trim((string)$safe, '_-');

    if ($safe === '') {
        $safe = 'client_unknown';
    }

    return substr($safe, 0, 100);
}
