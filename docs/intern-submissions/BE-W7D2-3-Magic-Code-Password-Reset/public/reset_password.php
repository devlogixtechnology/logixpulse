<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

$body = requestBody();
$token = requiredString($body, 'token');
$newPassword = requiredString($body, 'new_password');

if (strlen($newPassword) < 8) {
    jsonResponse(false, 'Password must contain at least 8 characters.', [], 422);
}

$tokenHash = hash('sha256', $token);

$stmt = db()->prepare(
    'SELECT * FROM password_resets
     WHERE token_hash = ? AND used_at IS NULL
     LIMIT 1'
);
$stmt->execute([$tokenHash]);
$record = $stmt->fetch();

if (!$record || isExpired($record['expires_at'])) {
    jsonResponse(false, 'Invalid or expired reset token.', [], 401);
}

if ((int)$record['attempts'] >= MAX_RESET_ATTEMPTS) {
    jsonResponse(false, 'Too many attempts. Request a new reset token.', [], 429);
}

$user = findUserByEmail($record['email']);

if (!$user || (int)$user['is_active'] !== 1) {
    jsonResponse(false, 'Invalid or expired reset token.', [], 401);
}

$newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

$pdo = db();
$pdo->beginTransaction();

try {
    $updateUser = $pdo->prepare(
        'UPDATE users SET password_hash = ?, updated_at = UTC_TIMESTAMP()
         WHERE id = ?'
    );
    $updateUser->execute([$newPasswordHash, $user['id']]);

    // One-time use: invalidate the reset token after successful reset.
    $consume = $pdo->prepare(
        'UPDATE password_resets SET used_at = UTC_TIMESTAMP() WHERE id = ?'
    );
    $consume->execute([$record['id']]);

    // Invalidate any other active reset tokens for this user.
    $invalidateOthers = $pdo->prepare(
        'UPDATE password_resets
         SET used_at = UTC_TIMESTAMP()
         WHERE user_id = ? AND used_at IS NULL'
    );
    $invalidateOthers->execute([$user['id']]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    jsonResponse(false, 'Password reset failed. Please try again.', [], 500);
}

jsonResponse(true, 'Password updated successfully. You can now log in.');
