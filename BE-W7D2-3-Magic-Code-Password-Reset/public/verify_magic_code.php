<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

$body = requestBody();
$email = strtolower(requiredString($body, 'email'));
$code = requiredString($body, 'code');

if (!validEmail($email) || !preg_match('/^\d{6}$/', $code)) {
    jsonResponse(false, 'Invalid email or code format.', [], 422);
}

$user = findUserByEmail($email);

if (!$user || (int)$user['is_active'] !== 1) {
    jsonResponse(false, 'Invalid or expired code.', [], 401);
}

$stmt = db()->prepare(
    'SELECT * FROM magic_codes
     WHERE user_id = ? AND email = ? AND used_at IS NULL
     ORDER BY id DESC LIMIT 1'
);
$stmt->execute([$user['id'], $email]);
$record = $stmt->fetch();

if (!$record || isExpired($record['expires_at'])) {
    jsonResponse(false, 'Invalid or expired code.', [], 401);
}

if ((int)$record['attempts'] >= MAX_MAGIC_ATTEMPTS) {
    jsonResponse(false, 'Too many attempts. Request a new code.', [], 429);
}

if (!password_verify($code, $record['code_hash'])) {
    $update = db()->prepare('UPDATE magic_codes SET attempts = attempts + 1 WHERE id = ?');
    $update->execute([$record['id']]);

    jsonResponse(false, 'Invalid or expired code.', [], 401);
}

// One-time use: mark code as consumed.
$consume = db()->prepare('UPDATE magic_codes SET used_at = UTC_TIMESTAMP() WHERE id = ?');
$consume->execute([$record['id']]);

jsonResponse(true, 'Magic code verified. Login successful.', [
    'user' => [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
    ],
]);
