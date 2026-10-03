<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';

require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(404);
    exit('Invoice ID is required.');
}

$pdo = getDatabaseConnection();
$stmt = $pdo->prepare(
    'SELECT i.id, i.client_id, i.original_filename, i.stored_filename, u.role AS client_role
     FROM invoices i
     LEFT JOIN users u ON u.id = i.client_id
     WHERE i.id = ?
     LIMIT 1'
);
$stmt->execute([$id]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    http_response_code(404);
    exit('Invoice not found.');
}

$user = currentUser();
$userRole = $user['role'] ?? 'client';
$userId = (int)($user['id'] ?? 0);

// Isolation: clients can only download their own invoices
if ($userRole === 'client' && (int)$invoice['client_id'] !== $userId) {
    http_response_code(403);
    exit('Forbidden: you do not have permission to download this invoice.');
}

// Search storage locations
$candidates = [
    dirname(__DIR__) . '/uploads/invoices/' . $invoice['stored_filename'],
    dirname(__DIR__) . '/storage/invoices/' . $invoice['stored_filename'],
    dirname(__DIR__) . '/dummy-invoice.pdf',
];

$file = false;
foreach ($candidates as $candidate) {
    if (!empty($candidate) && is_file($candidate)) {
        $file = $candidate;
        break;
    }
}

if (!$file) {
    http_response_code(404);
    exit('Invoice file not found on disk.');
}

$filename = !empty($invoice['original_filename']) ? $invoice['original_filename'] : 'invoice-' . $id . '.pdf';
$safeFilename = str_replace(['"', "\r", "\n", '/', '\\'], '', $filename);

header('Content-Type: application/pdf');
header('Content-Length: ' . (string)filesize($file));
header('Content-Disposition: inline; filename="' . $safeFilename . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=0, must-revalidate');

readfile($file);
exit;
