<?php
require_once __DIR__ . "/config/db.php";

$stmt = $pdo->query("SELECT id, name, email, phone FROM leads ORDER BY id ASC");
$leads = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lead Notes</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f4f6f8;margin:0;color:#20252b}
        .container{max-width:850px;margin:40px auto;padding:0 18px}
        .card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 4px 18px rgba(0,0,0,.08)}
        .lead{border:1px solid #dde2e7;border-radius:10px;padding:16px;margin:12px 0}
        .lead h3{margin:0 0 7px}.button{display:inline-block;margin-top:10px;margin-right:7px;padding:9px 13px;border-radius:8px;background:#222;color:#fff;text-decoration:none;font-weight:700}
        .muted{color:#68717b}
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Leads</h1>
        <p class="muted">Choose a lead to add a manual note or view its activity history.</p>

        <?php foreach ($leads as $lead): ?>
            <div class="lead">
                <h3><?= htmlspecialchars($lead['name']) ?></h3>
                <div class="muted">
                    <?= htmlspecialchars($lead['email'] ?? '') ?>
                    <?php if ($lead['phone']): ?> &middot; <?= htmlspecialchars($lead['phone']) ?><?php endif; ?>
                </div>
                <a class="button" href="add_note.php?lead_id=<?= (int)$lead['id'] ?>">Add Note</a>
                <a class="button" href="activity_history.php?lead_id=<?= (int)$lead['id'] ?>">Activity History</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
