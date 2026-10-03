<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST requests allowed.']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?? [];
$token = trim((string)($data['token'] ?? $_POST['token'] ?? ''));
$newPassword = (string)($data['new_password'] ?? $_POST['new_password'] ?? '');

if ($token === '' || $newPassword === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Token and new_password are required.']);
    exit;
}

if (strlen($newPassword) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    $tokenHash = hash('sha256', $token);

    $stmt = $pdo->prepare(
        'SELECT id, user_id, expires_at, used_at, token, token_hash
         FROM password_resets
         WHERE (token_hash = :hash OR token = :tok)
           AND used_at IS NULL
           AND expires_at > NOW()
         ORDER BY id DESC LIMIT 1'
    );
    $stmt->execute([':hash' => $tokenHash, ':tok' => $token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired password reset token.']);
        exit;
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

    $pdo->beginTransaction();

    $updUser = $pdo->prepare('UPDATE users SET password_hash = :hash, updated_at = NOW() WHERE id = :uid');
    $updUser->execute([':hash' => $newHash, ':uid' => $reset['user_id']]);

    $updReset = $pdo->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = :rid');
    $updReset->execute([':rid' => $reset['id']]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully. You can now sign in with your new password.'
    ]);

} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error resetting password: ' . $e->getMessage()]);
}
