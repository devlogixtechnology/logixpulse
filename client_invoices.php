<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_client();

$clientId = (int)$_SESSION['user']['id'];

$stmt = db()->prepare(
    'SELECT id, original_filename, amount, created_at
     FROM invoices
     WHERE client_id = ?
     ORDER BY created_at DESC'
);
$stmt->execute([$clientId]);
$invoices = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>My Invoices</title><link rel="stylesheet" href="../assets/style.css"></head>
<body>
<nav class="nav"><b>Client Portal</b><span><a href="../logout.php">Logout</a></span></nav>
<div class="container">
    <div class="card">
        <h1>My Invoices</h1>
        <p class="muted">Only invoices linked to your logged-in client account are shown.</p>
        <?php if (!$invoices): ?>
            <p>No invoices found for your account.</p>
        <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead><tr><th>Invoice</th><th>Amount</th><th>Uploaded</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($invoices as $invoice): ?>
                <tr>
                    <td><?= h($invoice['original_filename']) ?></td>
                    <td><?= number_format((float)$invoice['amount'], 2) ?></td>
                    <td><?= h($invoice['created_at']) ?></td>
                    <td><a class="btn" href="../download.php?id=<?= (int)$invoice['id'] ?>">Download PDF</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
