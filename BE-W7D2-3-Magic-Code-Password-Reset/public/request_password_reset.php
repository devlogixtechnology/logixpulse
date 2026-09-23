<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

$body = requestBody();
$email = strtolower(requiredString($body, 'email'));

if (!validEmail($email)) {
    jsonResponse(false, 'Please provide a valid email address.', [], 422);
}

$user = findUserByEmail($email);
$responseData = [];

if ($user && (int)$user['is_active'] === 1) {
    $rawToken = generateSecureToken();
    $tokenHash = hash('sha256', $rawToken);

    // Invalidate older active reset tokens.
    $invalidate = db()->prepare(
        'UPDATE password_resets SET used_at = UTC_TIMESTAMP()
         WHERE user_id = ? AND used_at IS NULL'
    );
    $invalidate->execute([$user['id']]);

    $stmt = db()->prepare(
        'INSERT INTO password_resets
         (user_id, email, token_hash, expires_at)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([
        $user['id'],
        $email,
        $tokenHash,
        futureUtc(PASSWORD_RESET_EXPIRY_MINUTES),
    ]);

    // Replace this with a real email link in production.
    $responseData['dev_token'] = $rawToken;
}

jsonResponse(
    true,
    'If the account exists, a password reset token has been generated.',
    $responseData
);
