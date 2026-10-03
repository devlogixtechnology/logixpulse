<?php
/**
 * LogixPulse — Invoice Download Endpoint
 *
 * Returns a downloadable file for a specific invoice.
 * Only allows access if the invoice belongs to the logged-in client.
 */

session_start();

require_once __DIR__ . '/../config/database.php';

// Must be authenticated as a client
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
    http_response_code(401);
    die('Unauthorized');
}

 $invoice_id = $_GET['id'] ?? null;

if (!$invoice_id || !is_numeric($invoice_id)) {
    http_response_code(400);
    die('Invalid invoice ID');
}

try {
    $pdo = getDatabaseConnection();

    // Fetch invoice — MUST belong to this client
    $stmt = $pdo->prepare('
        SELECT i.*, p.name AS project_name
        FROM invoices i
        LEFT JOIN projects p ON i.project_id = p.id
        WHERE i.id = :invoice_id
          AND i.client_id = :client_id
    ');

    $stmt->execute([
        ':invoice_id' => (int) $invoice_id,
        ':client_id'  => (int) $_SESSION['user_id']
    ]);

    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$invoice) {
        http_response_code(404);
        die('Invoice not found');
    }

    // Build a plain-text invoice (replace with PDF generation in production)
    $output  = "============================================\n";
    $output .= "  INVOICE\n";
    $output .= "============================================\n\n";
    $output .= "Invoice Number : " . $invoice['invoice_number'] . "\n";
    $output .= "Project        : " . ($invoice['project_name'] ?? 'N/A') . "\n";
    $output .= "Amount         : $" . number_format((float) $invoice['amount'], 2) . "\n";
    $output .= "Status         : " . ucfirst($invoice['status']) . "\n";
    $output .= "Issue Date     : " . ($invoice['issue_date'] ?? 'N/A') . "\n";
    $output .= "Due Date       : " . ($invoice['due_date'] ?? 'N/A') . "\n";
    if (!empty($invoice['paid_date'])) {
        $output .= "Paid Date      : " . $invoice['paid_date'] . "\n";
    }
    $output .= "\n============================================\n";
    $output .= "  LogixPulse Client Portal\n";
    $output .= "============================================\n";

    // Send as downloadable text file
    $filename = 'invoice-' . $invoice['invoice_number'] . '.txt';

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($output));
    echo $output;
    exit();

} catch (PDOException $e) {
    http_response_code(500);
    die('Error retrieving invoice');
}