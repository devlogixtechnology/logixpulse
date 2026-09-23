<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

$body = requestBody();
$email = strtolower(requiredString($body, 'email'));

if (!validEmail($email)) {
    jsonResponse(false, 'Please provide a valid email address.', [], 422);
}

$user = findUserByEmail($email);

/*
 * Generic response prevents revealing whether an email exists.
 * In local development, the generated code is returned in data.dev_code.
 */
$responseData = [];

if ($user && (int)$user['is_active'] === 1) {
    $code = generateSixDigitCode();
    $codeHash = password_hash($code, PASSWORD_DEFAULT);

    // Invalidate older active codes for this user.
    $invalidate = db()->prepare(
        'UPDATE magic_codes SET used_at = UTC_TIMESTAMP()
         WHERE user_id = ? AND used_at IS NULL'
    );
    $invalidate->execute([$user['id']]);

    $stmt = db()->prepare(
        'INSERT INTO magic_codes
         (user_id, email, code_hash, expires_at)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([
        $user['id'],
        $email,
        $codeHash,
        futureUtc(MAGIC_CODE_EXPIRY_MINUTES),
    ]);

    // Replace this with an email/SMS provider in production.
    $responseData['dev_code'] = $code;
}

jsonResponse(
    true,
    'If the account exists, a magic code has been generated.',
    $responseData
);
