<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$leadId = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);
if (!$leadId) {
    $leadId = 1;
}

try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare(
        "SELECT id, 
                COALESCE(NULLIF(name, ''), CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) AS name,
                email, phone, status, stage
         FROM leads WHERE id = ?"
    );
    $stmt->execute([$leadId]);
    $lead = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lead) {
        http_response_code(404);
        exit('Lead not found.');
    }

    $stmt = $pdo->prepare(
        "SELECT id, 
                COALESCE(activity_type, action, 'Note') AS activity_type,
                COALESCE(note, description, '') AS note,
                created_by, created_at
         FROM activity_logs
         WHERE lead_id = ?
         ORDER BY created_at DESC, id DESC"
    );
    $stmt->execute([$leadId]);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    http_response_code(500);
    exit('Database error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$saved = isset($_GET['saved']) && $_GET['saved'] === '1';

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lead Activity History - <?= e($lead['name']) ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 32px 16px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; }
        h1, h2 { margin: 0; font-size: 20px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .timeline { list-style: none; padding: 0; margin: 20px 0 0; }
        .timeline-item { padding-bottom: 20px; border-left: 2px solid #e2e8f0; margin-left: 10px; padding-left: 20px; position: relative; }
        .timeline-item:last-child { border-left-color: transparent; }
        .timeline-dot { position: absolute; left: -7px; top: 4px; width: 12px; height: 12px; border-radius: 50%; background: #4f46e5; border: 2px solid #fff; }
        .meta { font-size: 12px; color: #64748b; margin-bottom: 6px; }
        .text { font-size: 14px; color: #334155; line-height: 1.5; }
        .btn { display: inline-block; background: #4f46e5; color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; }
        .btn-link { color: #4f46e5; text-decoration: none; font-size: 14px; margin-right: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div style="margin-bottom: 16px;">
        <a href="lead_details.php?lead_id=<?= (int)$leadId ?>" class="btn-link">← Lead Details</a>
    </div>

    <?php if ($saved): ?>
        <div class="alert-success">✓ Note has been added successfully to the activity timeline.</div>
    <?php endif; ?>

    <div class="card">
        <div class="header">
            <div>
                <h1><?= e($lead['name']) ?></h1>
                <div style="font-size: 13px; color: #64748b; margin-top: 4px;"><?= e($lead['email']) ?> · <?= e($lead['phone']) ?></div>
            </div>
            <a href="add_note.php?lead_id=<?= (int)$leadId ?>" class="btn">+ Add Note</a>
        </div>
    </div>

    <div class="card">
        <h2>Activity Timeline</h2>
        <?php if (empty($activities)): ?>
            <p style="color: #64748b; font-style: italic; margin-top: 16px;">No activity logged yet.</p>
        <?php else: ?>
            <ul class="timeline">
                <?php foreach ($activities as $item): ?>
                    <li class="timeline-item">
                        <span class="timeline-dot"></span>
                        <div class="meta">
                            <strong><?= e($item['activity_type']) ?></strong> · <?= e($item['created_at']) ?>
                            <?php if (!empty($item['created_by'])): ?> · by <?= e($item['created_by']) ?><?php endif; ?>
                        </div>
                        <div class="text"><?= nl2br(e($item['note'])) ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
