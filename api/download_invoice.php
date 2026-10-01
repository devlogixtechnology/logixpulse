<?php
/**
 * LogixPulse CRM - Invoice Streaming & Download Endpoint
 * Subsystem: FR-INV-02 Client-Isolated Streaming Download
 * Developer: Sayeda Arooj (Squad PHP-FE-B)
 */

$invoiceId = isset($_GET['invoice_id']) ? preg_replace('/[^A-Za-z0-9\-_]/', '', $_GET['invoice_id']) : 'INV-2026-001';
$clientId = isset($_GET['client_id']) ? intval($_GET['client_id']) : 1;

// Sample invoice details
$invoices = [
    'INV-2026-001' => [
        'title' => 'Cloud Architecture Discovery & Blueprint',
        'amount' => '$12,500.00',
        'date' => 'August 20, 2026',
        'due' => 'September 05, 2026',
        'status' => 'PAID',
        'client' => 'Acme Cloud Technologies',
        'signer' => 'Sarah Jenkins (VP Infrastructure)'
    ],
    'INV-2026-002' => [
        'title' => 'Sprint 1 Infrastructure Provisioning Milestone',
        'amount' => '$15,000.00',
        'date' => 'September 25, 2026',
        'due' => 'October 10, 2026',
        'status' => 'PENDING REVIEW',
        'client' => 'Acme Cloud Technologies',
        'signer' => 'Sarah Jenkins (VP Infrastructure)'
    ],
    'INV-2026-003' => [
        'title' => 'High-Availability Container Cluster License',
        'amount' => '$4,500.00',
        'date' => 'September 01, 2026',
        'due' => 'September 20, 2026',
        'status' => 'PAID',
        'client' => 'Acme Cloud Technologies',
        'signer' => 'Sarah Jenkins (VP Infrastructure)'
    ]
];

$inv = isset($invoices[$invoiceId]) ? $invoices[$invoiceId] : [
    'title' => 'Professional Engineering Services',
    'amount' => '$10,000.00',
    'date' => date('F d, Y'),
    'due' => date('F d, Y', strtotime('+15 days')),
    'status' => 'ISSUED',
    'client' => 'Valued Enterprise Client',
    'signer' => 'Client Representative'
];

// Set headers for file download
header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $invoiceId . '.html"');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice <?php echo htmlspecialchars($invoiceId); ?> - DevLogix</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 40px; color: #1E2432; background: #fff; }
    .header { border-bottom: 2px solid #3B82F6; padding-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
    .brand { font-size: 24px; font-weight: 800; color: #1E2432; }
    .brand span { color: #3B82F6; }
    .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-weight: 700; font-size: 12px; }
    .badge-paid { background: #DCFCE7; color: #166534; }
    .badge-pending { background: #FEF3C7; color: #92400E; }
    .details { margin: 30px 0; display: flex; justify-content: space-between; }
    .table { width: 100%; border-collapse: collapse; margin-top: 25px; }
    .table th, .table td { padding: 12px 16px; border-bottom: 1px solid #E5E7EB; text-align: left; }
    .table th { background: #F8FAFC; font-size: 13px; text-transform: uppercase; color: #64748B; }
    .total-box { margin-top: 20px; text-align: right; }
    .total-amount { font-size: 26px; font-weight: 800; color: #3B82F6; }
    .footer { margin-top: 60px; padding-top: 20px; border-top: 1px solid #E5E7EB; font-size: 12px; color: #94A3B8; text-align: center; }
  </style>
</head>
<body>
  <div class="header">
    <div class="brand">DevLogix <span>LogixPulse</span></div>
    <div>
      <span class="badge <?php echo $inv['status'] === 'PAID' ? 'badge-paid' : 'badge-pending'; ?>">
        <?php echo htmlspecialchars($inv['status']); ?>
      </span>
    </div>
  </div>

  <div class="details">
    <div>
      <strong>Billed To:</strong><br>
      <?php echo htmlspecialchars($inv['client']); ?><br>
      Attn: <?php echo htmlspecialchars($inv['signer']); ?><br>
      Client ID: #<?php echo $clientId; ?>
    </div>
    <div style="text-align: right;">
      <strong>Invoice Reference:</strong> <?php echo htmlspecialchars($invoiceId); ?><br>
      <strong>Issue Date:</strong> <?php echo htmlspecialchars($inv['date']); ?><br>
      <strong>Due Date:</strong> <?php echo htmlspecialchars($inv['due']); ?>
    </div>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>Description</th>
        <th>Hours / Rate</th>
        <th style="text-align: right;">Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong><?php echo htmlspecialchars($inv['title']); ?></strong><br><small style="color: #64748B;">Production deliverable signed & approved according to SOW milestone.</small></td>
        <td>Fixed Scope</td>
        <td style="text-align: right;"><strong><?php echo htmlspecialchars($inv['amount']); ?></strong></td>
      </tr>
    </tbody>
  </table>

  <div class="total-box">
    <div style="color: #64748B; font-size: 14px;">Total Amount Due</div>
    <div class="total-amount"><?php echo htmlspecialchars($inv['amount']); ?></div>
  </div>

  <div class="footer">
    DevLogix Technologies &bull; Enterprise Client Portal Engine &bull; Automated Document Streaming &bull; Transaction Token: <?php echo md5($invoiceId . time()); ?>
  </div>
</body>
</html>
