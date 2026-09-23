<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(404);
    exit('Invoice not found.');
}

$stmt = db()->prepare(
    'SELECT i.id, i.client_id, i.original_filename, i.stored_filename, u.role AS client_role
     FROM invoices i
     INNER JOIN users u ON u.id = i.client_id
     WHERE i.id = ?
     LIMIT 1'
);
$stmt->execute([$id]);
$invoice = $stmt->fetch();

if (!$invoice || $invoice['client_role'] !== 'client') {
    http_response_code(404);
    exit('Invoice not found.');
}

/*
 * Authorization is enforced here, not only in the UI:
 * - Admin may open every invoice.
 * - Client may open only invoices whose client_id equals their own user ID.
 */
if (($_SESSION['user']['role'] ?? '') === 'client' &&
    (int)$invoice['client_id'] !== (int)$_SESSION['user']['id']) {
    http_response_code(403);
    exit('Forbidden: this invoice belongs to a different client.');
}

$storageDir = realpath(__DIR__ . '/../storage/invoices');
$file = $storageDir ? realpath($storageDir . DIRECTORY_SEPARATOR . $invoice['stored_filename']) : false;

if (!$file || !$storageDir || dirname($file) !== $storageDir || !is_file($file)) {
    http_response_code(404);
    exit('Invoice file not found.');
}

header('Content-Type: application/pdf');
header('Content-Length: ' . (string)filesize($file));
header('Content-Disposition: inline; filename="' . str_replace(['"', "\r", "\n"], '', $invoice['original_filename']) . '"');
header('X-Content-Type-Options: nosniff');
readfile($file);
exit;
