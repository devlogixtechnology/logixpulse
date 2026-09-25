<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

require_client();

$user = currentUser();
$clientId = (int)($user['id'] ?? 0);

$pdo = getDatabaseConnection();
$stmt = $pdo->prepare(
    "SELECT id, 
            COALESCE(original_filename, invoice_number, CONCAT('Invoice #', id)) AS original_filename,
            amount, status, created_at
     FROM invoices
     WHERE client_id = ?
     ORDER BY created_at DESC"
);
$stmt->execute([$clientId]);
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>My Invoices - LogixPulse</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 24px; }
        .container { max-width: 800px; margin: 20px auto; }
        .card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .top-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 14px; }
        .top-nav a { color: #4f46e5; text-decoration: none; }
        h1 { margin-top: 0; font-size: 20px; }
        .muted { color: #64748b; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px; }
        th, td { text-align: left; padding: 12px 10px; border-bottom: 1px solid #e2e8f0; }
        th { color: #64748b; font-weight: 600; font-size: 12px; text-transform: uppercase; }
        .btn { display: inline-block; padding: 6px 12px; border-radius: 6px; background: #4f46e5; color: #fff; text-decoration: none; font-size: 12px; font-weight: 500; }
        .btn:hover { background: #4338ca; }
    </style>
</head>
<body>
<div class="container">
    <div class="top-nav">
        <strong>LogixPulse Client Portal</strong>
        <div>
            <a href="../client-portal-dashboard/index.php">Dashboard</a> · 
            <a href="../auth/logout.php">Logout</a>
        </div>
    </div>

    <div class="card">
        <h1>My Invoices</h1>
        <p class="muted">Showing invoices associated with your client account.</p>

        <?php if (empty($invoices)): ?>
            <p style="margin-top: 20px; color: #64748b; font-style: italic;">No invoices found for your account.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $inv): ?>
                        <tr>
                            <td><?= h($inv['original_filename']) ?></td>
                            <td>$<?= number_format((float)$inv['amount'], 2) ?></td>
                            <td><?= h(substr((string)$inv['created_at'], 0, 10)) ?></td>
                            <td>
                                <a class="btn" href="download.php?id=<?= (int)$inv['id'] ?>">Download PDF</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
