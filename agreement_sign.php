<?php
declare(strict_types=1);

/**
 * BE-04: Digital Signature Verification & Storage Pipeline
 *
 * POST JSON:
 * {
 *   "contract_token": "...",
 *   "signer_legal_name": "John Doe",
 *   "signature_image": "data:image/png;base64,..."
 * }
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    jsonResponse([
        'success' => false,
        'message' => 'Only POST requests are allowed.'
    ], 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '', true);

if (!is_array($data)) {
    jsonResponse([
        'success' => false,
        'message' => 'Request body must be valid JSON.'
    ], 400);
}

$contractToken = trim((string)($data['contract_token'] ?? ''));
$signerLegalName = trim((string)($data['signer_legal_name'] ?? ''));
$signatureImage = trim((string)($data['signature_image'] ?? ''));

if ($contractToken === '' || $signerLegalName === '' || $signatureImage === '') {
    jsonResponse([
        'success' => false,
        'message' => 'contract_token, signer_legal_name and signature_image are required.'
    ], 422);
}

if (!preg_match('/^[A-Za-z0-9._~:-]{16,512}$/', $contractToken)) {
    jsonResponse([
        'success' => false,
        'message' => 'Invalid contract token format.'
    ], 422);
}

if (mb_strlen($signerLegalName) < 2 || mb_strlen($signerLegalName) > 200) {
    jsonResponse([
        'success' => false,
        'message' => 'Signer legal name must be between 2 and 200 characters.'
    ], 422);
}

// Prevent HTML/script-like data from being stored as the legal name.
if (preg_match('/[<>]/', $signerLegalName)) {
    jsonResponse([
        'success' => false,
        'message' => 'Invalid signer legal name.'
    ], 422);
}

/**
 * Accept either:
 *   data:image/png;base64,AAAA...
 *   data:image/jpeg;base64,AAAA...
 *   data:image/webp;base64,AAAA...
 */
if (!preg_match(
    '#^data:image/(png|jpeg|jpg|webp);base64,([A-Za-z0-9+/=\r\n]+)$#i',
    $signatureImage,
    $matches
)) {
    jsonResponse([
        'success' => false,
        'message' => 'Signature must be a PNG, JPEG or WEBP base64 data URI.'
    ], 422);
}

$extension = strtolower($matches[1]);
if ($extension === 'jpeg') {
    $extension = 'jpg';
}

$base64Payload = preg_replace('/\s+/', '', $matches[2]);
$binary = base64_decode($base64Payload, true);

if ($binary === false || $binary === '') {
    jsonResponse([
        'success' => false,
        'message' => 'Invalid base64 signature payload.'
    ], 422);
}

// Keep signature files reasonably small.
$maxBytes = 2 * 1024 * 1024;
if (strlen($binary) > $maxBytes) {
    jsonResponse([
        'success' => false,
        'message' => 'Signature image is too large. Maximum size is 2 MB.'
    ], 422);
}

// Validate actual image bytes, not only the supplied MIME type.
$imageInfo = @getimagesizefromstring($binary);
if ($imageInfo === false) {
    jsonResponse([
        'success' => false,
        'message' => 'Signature payload is not a valid image.'
    ], 422);
}

$allowedMime = [
    'image/png' => 'png',
    'image/jpeg' => 'jpg',
    'image/webp' => 'webp',
];

$detectedMime = $imageInfo['mime'] ?? '';
if (!isset($allowedMime[$detectedMime])) {
    jsonResponse([
        'success' => false,
        'message' => 'Unsupported signature image type.'
    ], 422);
}

$extension = $allowedMime[$detectedMime];

$ip = getClientIp();
$userAgent = getUserAgent();
$executedAt = gmdate('Y-m-d H:i:s');

try {
    $pdo->beginTransaction();

    // Lock the contract row so two signing requests cannot execute it simultaneously.
    $stmt = $pdo->prepare(
        'SELECT id, client_id, status, token_hash
         FROM contracts
         WHERE token_hash = :token_hash
         LIMIT 1
         FOR UPDATE'
    );

    $tokenHash = hash('sha256', $contractToken);
    $stmt->execute(['token_hash' => $tokenHash]);
    $contract = $stmt->fetch();

    if (!$contract) {
        $pdo->rollBack();
        jsonResponse([
            'success' => false,
            'message' => 'Contract not found or token is invalid.'
        ], 404);
    }

    if ($contract['status'] === 'Executed') {
        $pdo->rollBack();
        jsonResponse([
            'success' => false,
            'message' => 'This contract has already been executed.'
        ], 409);
    }

    $clientId = (string)$contract['client_id'];
    $clientDirectory = safeClientDirectory($clientId);

    // Keep client signatures isolated from other clients.
    $storageRoot = dirname(__DIR__) . '/storage/signatures';
    $clientDir = $storageRoot . '/' . $clientDirectory;

    if (!is_dir($clientDir) && !mkdir($clientDir, 0750, true) && !is_dir($clientDir)) {
        throw new RuntimeException('Unable to create secure signature directory.');
    }

    $signatureSha256 = hash('sha256', $binary);
    $fileName = 'contract_' . (int)$contract['id'] . '_' . bin2hex(random_bytes(16)) . '.' . $extension;
    $filePath = $clientDir . '/' . $fileName;

    // Exclusive create/write to avoid overwriting an existing signature file.
    $bytesWritten = file_put_contents($filePath, $binary, LOCK_EX);
    if ($bytesWritten === false) {
        throw new RuntimeException('Unable to save signature graphic.');
    }

    @chmod($filePath, 0640);

    $relativePath = 'storage/signatures/' . $clientDirectory . '/' . $fileName;

    // Audit certificate: includes the signature hash and execution metadata.
    $certificate = generateAuditCertificate(
        (int)$contract['id'],
        $tokenHash,
        $signerLegalName,
        $signatureSha256,
        $ip,
        $userAgent,
        $executedAt
    );

    $auditStmt = $pdo->prepare(
        'INSERT INTO contract_audits
            (contract_id, action, signer_legal_name, signature_path, signature_sha256,
             signer_ip, user_agent, executed_at, certificate_hash, certificate_json)
         VALUES
            (:contract_id, :action, :signer_legal_name, :signature_path, :signature_sha256,
             :signer_ip, :user_agent, :executed_at, :certificate_hash, :certificate_json)'
    );

    $auditStmt->execute([
        'contract_id' => (int)$contract['id'],
        'action' => 'Executed',
        'signer_legal_name' => $signerLegalName,
        'signature_path' => $relativePath,
        'signature_sha256' => $signatureSha256,
        'signer_ip' => $ip,
        'user_agent' => $userAgent,
        'executed_at' => $executedAt,
        'certificate_hash' => $certificate['certificate_sha256'],
        'certificate_json' => json_encode($certificate, JSON_UNESCAPED_SLASHES),
    ]);

    // Lock the executed state on the contract.
    $updateStmt = $pdo->prepare(
        'UPDATE contracts
         SET status = :status, signer_legal_name = :signer_legal_name,
             executed_at = :executed_at
         WHERE id = :id AND status <> :already_executed'
    );

    $updateStmt->execute([
        'status' => 'Executed',
        'signer_legal_name' => $signerLegalName,
        'executed_at' => $executedAt,
        'id' => (int)$contract['id'],
        'already_executed' => 'Executed',
    ]);

    if ($updateStmt->rowCount() !== 1) {
        throw new RuntimeException('Contract status could not be updated.');
    }

    // Trigger a client welcome notification after successful execution.
    $notificationStmt = $pdo->prepare(
        'INSERT INTO notifications
            (client_id, contract_id, type, title, message, created_at)
         VALUES
            (:client_id, :contract_id, :type, :title, :message, :created_at)'
    );

    $notificationStmt->execute([
        'client_id' => $clientId,
        'contract_id' => (int)$contract['id'],
        'type' => 'client_welcome',
        'title' => 'Welcome — Agreement Executed',
        'message' => 'Your digital agreement has been successfully executed and archived.',
        'created_at' => $executedAt,
    ]);

    $pdo->commit();

    jsonResponse([
        'success' => true,
        'message' => 'Agreement signed, verified, archived and marked as Executed.',
        'data' => [
            'contract_id' => (int)$contract['id'],
            'status' => 'Executed',
            'executed_at' => $executedAt,
            'audit_certificate_hash' => $certificate['certificate_sha256'],
        ]
    ], 200);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Remove a file if it was written but the database transaction failed.
    if (isset($filePath) && is_file($filePath)) {
        @unlink($filePath);
    }

    error_log(
        '[' . gmdate('c') . '] agreement_sign.php: ' . $e->getMessage(),
        3,
        dirname(__DIR__) . '/logs/app.log'
    );

    jsonResponse([
        'success' => false,
        'message' => 'Unable to execute the agreement.'
    ], 500);
}
