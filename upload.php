<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/helpers.php';
require_admin();

$clients = db()->query('SELECT id, name, email FROM users WHERE role = "client" ORDER BY name')->fetchAll();
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
        $clientStmt = db()->prepare('SELECT id FROM users WHERE id = ? AND role = "client" LIMIT 1');
        $clientStmt->execute([$clientId]);
        if (!$clientStmt->fetch()) {
            $errors[] = 'Selected client account does not exist.';
        }
    }

    if (!$errors) {
        if ((int)$file['size'] > 5 * 1024 * 1024) {
            $errors[] = 'PDF must be 5 MB or smaller.';
        }

        $original = basename((string)$file['name']);
        $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        $header = file_get_contents($file['tmp_name'], false, null, 0, 5);

        if ($extension !== 'pdf' || $mime !== 'application/pdf' || $header !== '%PDF-') {
            $errors[] = 'Only valid PDF files are allowed.';
        }
    }

    if (!$errors) {
        $storageDir = __DIR__ . '/../../storage/invoices';
        if (!is_dir($storageDir) && !mkdir($storageDir, 0750, true)) {
            $errors[] = 'Could not create invoice storage directory.';
        }
    }

    if (!$errors) {
        $storedFilename = bin2hex(random_bytes(16)) . '.pdf';
        $destination = $storageDir . DIRECTORY_SEPARATOR . $storedFilename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $errors[] = 'Could not safely save the uploaded PDF.';
        } else {
            chmod($destination, 0640);

            try {
                $stmt = db()->prepare(
                    'INSERT INTO invoices (client_id, original_filename, stored_filename, amount)
                     VALUES (?, ?, ?, ?)'
                );
                $stmt->execute([
                    $clientId,
                    $original,
                    $storedFilename,
                    number_format((float)$amountRaw, 2, '.', '')
                ]);
                flash('success', 'Invoice uploaded and linked to the selected client.');
                redirect('index.php');
            } catch (Throwable $e) {
                @unlink($destination);
                $errors[] = 'Invoice could not be recorded in the database.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Upload Invoice</title><link rel="stylesheet" href="../assets/style.css"></head>
<body>
<nav class="nav"><b>Admin Invoice Portal</b><span><a href="index.php">All Invoices</a><a href="../logout.php">Logout</a></span></nav>
<div class="container">
    <div class="card">
        <h1>Upload Invoice</h1>
        <p class="muted">Client selection is required. Every saved invoice is linked to exactly one client account.</p>
        <?php if ($errors): ?><div class="alert error"><ul><?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label>Client account *</label>
            <select name="client_id" required>
                <option value="">-- Select client --</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= (int)$client['id'] ?>" <?= ((string)($_POST['client_id'] ?? '') === (string)$client['id']) ? 'selected' : '' ?>>
                        <?= h($client['name']) ?> (<?= h($client['email']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Invoice amount *</label>
            <input type="number" name="amount" min="0" step="0.01" required value="<?= h((string)($_POST['amount'] ?? '')) ?>">

            <label>Invoice PDF *</label>
            <input type="file" name="invoice" accept="application/pdf,.pdf" required>
            <p class="small muted">PDF only, maximum 5 MB.</p>

            <button type="submit">Upload & Save Invoice</button>
        </form>
    </div>
</div>
</body>
</html>
