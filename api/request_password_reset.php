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
$email = trim((string)($data['email'] ?? $_POST['email'] ?? ''));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare('SELECT id, email FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No account associated with that email.']);
        exit;
    }

    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expiresAt = date('Y-m-d H:i:s', time() + 1800); // 30 minutes

    $insert = $pdo->prepare(
        'INSERT INTO password_resets (user_id, email, token, token_hash, expires_at, created_at)
         VALUES (:uid, :email, :token, :hash, :exp, NOW())'
    );
    $insert->execute([
        ':uid'   => (int)$user['id'],
        ':email' => $email,
        ':token' => $token,
        ':hash'  => $tokenHash,
        ':exp'   => $expiresAt,
    ]);

    echo json_encode([
        'success'    => true,
        'message'    => 'Password reset token generated.',
        'dev_token'  => $token,
        'expires_at' => $expiresAt
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error requesting reset: ' . $e->getMessage()]);
}
