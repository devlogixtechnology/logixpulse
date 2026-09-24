<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../backend/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST requests allowed.']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?? [];
$email = trim((string)($data['email'] ?? $_POST['email'] ?? ''));
$code  = trim((string)($data['code'] ?? $_POST['code'] ?? ''));

if ($email === '' || $code === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and code are required.']);
    exit;
}

try {
    $pdo = getDatabaseConnection();

    $stmt = $pdo->prepare(
        'SELECT id, user_id, code, code_hash, expires_at, used_at, attempts
         FROM magic_codes
         WHERE email = :email AND used_at IS NULL AND expires_at > NOW()
         ORDER BY id DESC LIMIT 1'
    );
    $stmt->execute([':email' => $email]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired magic code.']);
        exit;
    }

    $valid = false;
    if (!empty($record['code_hash'])) {
        $valid = password_verify($code, $record['code_hash']);
    } elseif (!empty($record['code'])) {
        $valid = hash_equals($record['code'], $code);
    }

    if (!$valid) {
        $pdo->prepare('UPDATE magic_codes SET attempts = attempts + 1 WHERE id = ?')->execute([$record['id']]);
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Incorrect magic code.']);
        exit;
    }

    // Mark as used
    $pdo->prepare('UPDATE magic_codes SET used_at = NOW() WHERE id = ?')->execute([$record['id']]);

    // Fetch user and log in
    $uStmt = $pdo->prepare('SELECT id, name, email, role, status FROM users WHERE id = ?');
    $uStmt->execute([$record['user_id']]);
    $user = $uStmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        loginUser($user, $user['role'] ?? 'client');
    }

    echo json_encode([
        'success'  => true,
        'message'  => 'Verification successful.',
        'redirect' => 'client-portal-dashboard/index.php',
        'user'     => $user
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Verification error: ' . $e->getMessage()]);
}
