<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

require_admin();

$pdo = getDatabaseConnection();
$clients = $pdo->query("SELECT id, COALESCE(NULLIF(name, ''), CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) AS name, email FROM users WHERE role = 'client' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientId = filter_input(INPUT_POST, 'client_id', FILTER_VALIDATE_INT);
    $amountRaw = trim((string)($_POST['amount'] ?? ''));
    $file = $_FILES['invoice'] ?? null;

    if (!$clientId) {
        $errors[] = 'You must select a client. The invoice cannot be unassigned.';
    }

    if ($amountRaw === '' || !is_numeric($amountRaw) || (float)$amountRaw < 0) {
        $errors[] = 'Enter a valid invoice amount.';
    }

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Please select a PDF invoice file.';
    }

    if (!$errors) {
        $clientStmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'client' LIMIT 1");
        $clientStmt->execute([$clientId]);
        if (!$clientStmt->fetch()) {
            $errors[] = 'Selected client account does not exist.';
        }
    }

    if (!$errors && $file) {
        if ((int)$file['size'] > 10 * 1024 * 1024) {
            $errors[] = 'PDF must be 10 MB or smaller.';
        }

        $original = basename((string)$file['name']);
        $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));

        $header = file_get_contents($file['tmp_name'], false, null, 0, 5);

        if ($extension !== 'pdf' || $header !== '%PDF-') {
            $errors[] = 'Only valid PDF files are allowed.';
        }
    }

    if (!$errors && $file) {
        $storageDir = dirname(__DIR__) . '/uploads/invoices';
        if (!is_dir($storageDir) && !mkdir($storageDir, 0755, true)) {
            $errors[] = 'Could not create invoice storage directory.';
        }
    }

    if (!$errors && $file) {
        $storedFilename = bin2hex(random_bytes(16)) . '.pdf';
        $destination = $storageDir . DIRECTORY_SEPARATOR . $storedFilename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $errors[] = 'Could not safely save the uploaded PDF.';
        } else {
            chmod($destination, 0644);

            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO invoices (client_id, original_filename, stored_filename, amount, invoice_number, status, issue_date)
                     VALUES (?, ?, ?, ?, ?, 'pending', CURRENT_DATE)"
                );
                $invNumber = 'INV-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
                $stmt->execute([
                    $clientId,
                    $original,
                    $storedFilename,
                    number_format((float)$amountRaw, 2, '.', ''),
                    $invNumber
                ]);
                flash('success', 'Invoice uploaded and linked to the selected client successfully.');
                redirect('upload.php');
            } catch (Throwable $e) {
                @unlink($destination);
                $errors[] = 'Invoice could not be recorded in database: ' . $e->getMessage();
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Upload Client Invoice - LogixPulse</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 24px; }
        .container { max-width: 600px; margin: 20px auto; }
        .card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        h1 { margin-top: 0; font-size: 20px; }
        .muted { color: #64748b; font-size: 13px; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert.error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        label { display: block; font-weight: 500; font-size: 14px; margin-top: 16px; margin-bottom: 6px; }
        select, input[type="number"], input[type="file"] { width: 100%; box-sizing: border-box; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; }
        button { margin-top: 24px; width: 100%; padding: 12px; background: #4f46e5; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        button:hover { background: #4338ca; }
        .top-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 14px; }
        .top-nav a { color: #4f46e5; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <div class="top-nav">
        <strong>Admin Invoice Portal</strong>
        <div>
            <a href="client_invoices.php">All Invoices</a> · 
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="card">
        <h1>Upload Client Invoice</h1>
        <p class="muted">Upload a PDF invoice and associate it securely with a client account.</p>

        <?php show_flash(); ?>

        <?php if (!empty($errors)): ?>
            <div class="alert error">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= h($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <label>Client Account *</label>
            <select name="client_id" required>
                <option value="">-- Select Client --</option>
                <?php foreach ($clients as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= ((string)($_POST['client_id'] ?? '') === (string)$c['id']) ? 'selected' : '' ?>>
                        <?= h($c['name']) ?> (<?= h($c['email']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Invoice Amount ($) *</label>
            <input type="number" name="amount" min="0" step="0.01" required value="<?= h((string)($_POST['amount'] ?? '')) ?>" placeholder="e.g. 2500.00">

            <label>Invoice PDF File *</label>
            <input type="file" name="invoice" accept="application/pdf,.pdf" required>
            <p class="muted" style="margin-top: 4px;">PDF format only, maximum 10 MB.</p>

            <button type="submit">Upload & Save Invoice</button>
        </form>
    </div>
</div>
</body>
</html>
