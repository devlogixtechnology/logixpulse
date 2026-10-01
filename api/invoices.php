<?php
/**
 * LogixPulse CRM - Client Invoices API
 * Squad: PHP-FE-B (Client Communication & Invoices)
 * Subtask: Client Invoices Repository & Billing History
 * Developer: Sayeda Arooj
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$clientId = isset($_GET['client_id']) ? intval($_GET['client_id']) : 1;
$statusFilter = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'all';

$invoicesDatabase = [
    [
        'id' => 'INV-2026-001',
        'client_id' => 1,
        'title' => 'Cloud Architecture Discovery & Blueprint',
        'issue_date' => '2026-08-20',
        'due_date' => '2026-09-05',
        'amount' => 12500.00,
        'formatted_amount' => '$12,500.00',
        'status' => 'paid',
        'status_label' => 'Paid',
        'status_badge_class' => 'bg-success-subtle text-success border border-success-subtle',
        'payment_method' => 'Wire Transfer (ACH)',
        'download_url' => '/api/download_invoice.php?invoice_id=INV-2026-001&client_id=1'
    ],
    [
        'id' => 'INV-2026-002',
        'client_id' => 1,
        'title' => 'Sprint 1 Infrastructure Provisioning Milestone',
        'issue_date' => '2026-09-25',
        'due_date' => '2026-10-10',
        'amount' => 15000.00,
        'formatted_amount' => '$15,000.00',
        'status' => 'pending',
        'status_label' => 'Pending Review',
        'status_badge_class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        'payment_method' => 'Pending Authorization',
        'download_url' => '/api/download_invoice.php?invoice_id=INV-2026-002&client_id=1'
    ],
    [
        'id' => 'INV-2026-003',
        'client_id' => 1,
        'title' => 'High-Availability Container Cluster License',
        'issue_date' => '2026-09-01',
        'due_date' => '2026-09-20',
        'amount' => 4500.00,
        'formatted_amount' => '$4,500.00',
        'status' => 'paid',
        'status_label' => 'Paid',
        'status_badge_class' => 'bg-success-subtle text-success border border-success-subtle',
        'payment_method' => 'Corporate Credit Card',
        'download_url' => '/api/download_invoice.php?invoice_id=INV-2026-003&client_id=1'
    ],
    [
        'id' => 'INV-2026-004',
        'client_id' => 1,
        'title' => 'Security Hardening & Penetration Testing Retainer',
        'issue_date' => '2026-08-01',
        'due_date' => '2026-08-15',
        'amount' => 6200.00,
        'formatted_amount' => '$6,200.00',
        'status' => 'paid',
        'status_label' => 'Paid',
        'status_badge_class' => 'bg-success-subtle text-success border border-success-subtle',
        'payment_method' => 'Wire Transfer (ACH)',
        'download_url' => '/api/download_invoice.php?invoice_id=INV-2026-004&client_id=1'
    ],
    // Client 2 (Nexus Global Logistics)
    [
        'id' => 'INV-2026-005',
        'client_id' => 2,
        'title' => 'Telematics Sensor Feasibility Audit',
        'issue_date' => '2026-09-15',
        'due_date' => '2026-09-30',
        'amount' => 18500.00,
        'formatted_amount' => '$18,500.00',
        'status' => 'overdue',
        'status_label' => 'Overdue',
        'status_badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle',
        'payment_method' => 'Unpaid',
        'download_url' => '/api/download_invoice.php?invoice_id=INV-2026-005&client_id=2'
    ],
    [
        'id' => 'INV-2026-006',
        'client_id' => 2,
        'title' => 'Edge Gateway Protocol Architecture Specification',
        'issue_date' => '2026-10-01',
        'due_date' => '2026-10-15',
        'amount' => 24000.00,
        'formatted_amount' => '$24,000.00',
        'status' => 'pending',
        'status_label' => 'Pending Review',
        'status_badge_class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        'payment_method' => 'Pending Approval',
        'download_url' => '/api/download_invoice.php?invoice_id=INV-2026-006&client_id=2'
    ]
];

// Enforce strict tenant isolation: only return invoices belonging to the requested client
$clientInvoices = array_values(array_filter($invoicesDatabase, function($inv) use ($clientId) {
    return $inv['client_id'] === $clientId;
}));

// Apply status filter if not 'all'
if ($statusFilter !== 'all') {
    $clientInvoices = array_values(array_filter($clientInvoices, function($inv) use ($statusFilter) {
        return strtolower($inv['status']) === $statusFilter;
    }));
}

// Compute invoice aggregate metrics
$totalBilled = 0;
$totalPaid = 0;
$totalOutstanding = 0;

foreach ($clientInvoices as $inv) {
    $totalBilled += $inv['amount'];
    if ($inv['status'] === 'paid') {
        $totalPaid += $inv['amount'];
    } else {
        $totalOutstanding += $inv['amount'];
    }
}

echo json_encode([
    'status' => 'success',
    'client_id' => $clientId,
    'total_count' => count($clientInvoices),
    'summary' => [
        'total_billed' => '$' . number_format($totalBilled, 2),
        'total_paid' => '$' . number_format($totalPaid, 2),
        'total_outstanding' => '$' . number_format($totalOutstanding, 2)
    ],
    'data' => $clientInvoices
], JSON_PRETTY_PRINT);
