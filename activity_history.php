<?php
require_once __DIR__ . "/config/db.php";

$leadId = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);
if (!$leadId) {
    $leadId = 1;
}

$stmt = $pdo->prepare("SELECT id, name, email, phone FROM leads WHERE id = ?");
$stmt->execute([$leadId]);
$lead = $stmt->fetch();

if (!$lead) {
    http_response_code(404);
    exit("Lead not found.");
}

$stmt = $pdo->prepare(
    "SELECT id, activity_type, note, created_at
     FROM activity_logs
     WHERE lead_id = ?
     ORDER BY created_at DESC, id DESC"
);
$stmt->execute([$leadId]);
$activities = $stmt->fetchAll();

$saved = isset($_GET['saved']) && $_GET['saved'] === '1';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lead Activity History - <?= htmlspecialchars($lead['name']) ?></title>
    <style>
        body{font-family:Arial,sans-serif;background:#f4f6f8;margin:0;color:#20252b}
        .container{max-width:800px;margin:40px auto;padding:0 18px}
        .card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 4px 18px rgba(0,0,0,.08)}
        .lead{background:#f0f3f6;padding:14px;border-radius:8px;margin:16px 0}
        .success{background:#e7f8ec;color:#146b2e;padding:11px;border-radius:8px;margin-bottom:15px}
        .activity{border:1px solid #dde2e7;border-radius:10px;padding:15px;margin-top:12px}
        .meta{font-size:13px;color:#626b75;margin-bottom:8px}
        .note{white-space:pre-wrap;line-height:1.5}
        .empty{color:#626b75;padding:18px 0}
        .button{display:inline-block;padding:10px 14px;border-radius:8px;background:#222;color:#fff;text-decoration:none;font-weight:700}
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Lead Activity History</h1>

        <div class="lead">
            <strong><?= htmlspecialchars($lead['name']) ?></strong><br>
            <?php if ($lead['email']): ?><?= htmlspecialchars($lead['email']) ?><br><?php endif; ?>
            <?php if ($lead['phone']): ?><?= htmlspecialchars($lead['phone']) ?><?php endif; ?>
        </div>

        <?php if ($saved): ?>
            <div class="success">Note saved successfully.</div>
        <?php endif; ?>

        <a class="button" href="add_note.php?lead_id=<?= (int)$lead['id'] ?>">Add Manual Note</a>

        <?php if (!$activities): ?>
            <div class="empty">No activity has been recorded for this lead yet.</div>
        <?php else: ?>
            <?php foreach ($activities as $activity): ?>
                <div class="activity">
                    <div class="meta">
                        <?= htmlspecialchars(ucfirst($activity['activity_type'])) ?>
                        &middot;
                        <?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($activity['created_at']))) ?>
                    </div>
                    <div class="note"><?= htmlspecialchars($activity['note']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
