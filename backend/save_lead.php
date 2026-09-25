<?php
declare(strict_types=1);

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Only POST requests are allowed.'
    ]);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?? [];
if (!is_array($data)) {
    $data = [];
}

$name   = trim((string)($data['name'] ?? $_POST['name'] ?? ''));
$email  = trim((string)($data['email'] ?? $_POST['email'] ?? ''));
$phone  = trim((string)($data['phone'] ?? $_POST['phone'] ?? ''));
$source = trim((string)($data['source'] ?? $_POST['source'] ?? 'website'));

// If name wasn't provided directly, try first_name and last_name
if ($name === '' && (!empty($data['first_name']) || !empty($_POST['first_name']))) {
    $fn = trim((string)($data['first_name'] ?? $_POST['first_name'] ?? ''));
    $ln = trim((string)($data['last_name'] ?? $_POST['last_name'] ?? ''));
    $name = trim($fn . ' ' . $ln);
}

if ($name === '' || $email === '' || $phone === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Name, email, and phone are required.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email address format.'
    ]);
    exit;
}

$nameParts = explode(' ', $name, 2);
$firstName = $nameParts[0];
$lastName  = $nameParts[1] ?? '';

try {
    $pdo = getDatabaseConnection();

    $stmt = $pdo->prepare(
        "INSERT INTO leads (name, first_name, last_name, email, phone, source, status, stage)
         VALUES (:name, :first_name, :last_name, :email, :phone, :source, 'new', 'New')"
    );
    $stmt->execute([
        ':name'       => $name,
        ':first_name' => $firstName,
        ':last_name'  => $lastName,
        ':email'      => $email,
        ':phone'      => $phone,
        ':source'     => $source,
    ]);

    $leadId = (int)$pdo->lastInsertId();

    // Log the lead creation in activity_logs
    try {
        $logStmt = $pdo->prepare(
            "INSERT INTO activity_logs (lead_id, activity_type, description, note, created_by, created_at)
             VALUES (?, 'Lead Created', 'Lead was added to the CRM.', 'Lead was added to the CRM.', 'System', NOW())"
        );
        $logStmt->execute([$leadId]);
    } catch (Throwable $e) {
        error_log('[save_lead] activity log creation warning: ' . $e->getMessage());
    }

    echo json_encode([
        'success' => true,
        'message' => 'Lead saved successfully.',
        'lead_id' => $leadId,
        'lead'    => [
            'id'     => $leadId,
            'name'   => $name,
            'email'  => $email,
            'phone'  => $phone,
            'source' => $source,
            'status' => 'new'
        ]
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save lead: ' . $e->getMessage()
    ]);
}
