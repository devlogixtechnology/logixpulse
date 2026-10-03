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
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare('SELECT id, email FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No account found with this email.']);
        exit;
    }

    $code = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    $codeHash = password_hash($code, PASSWORD_DEFAULT);
    $expiresAt = date('Y-m-d H:i:s', time() + 600); // 10 minutes

    $insert = $pdo->prepare(
        'INSERT INTO magic_codes (user_id, email, code, code_hash, expires_at, created_at)
         VALUES (:uid, :email, :code, :hash, :exp, NOW())'
    );
    $insert->execute([
        ':uid'   => (int)$user['id'],
        ':email' => $email,
        ':code'  => $code,
        ':hash'  => $codeHash,
        ':exp'   => $expiresAt,
    ]);

    echo json_encode([
        'success'    => true,
        'message'    => 'Magic code sent to your email.',
        'dev_code'   => $code,
        'expires_at' => $expiresAt
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error requesting magic code: ' . $e->getMessage()]);
}
