<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$leadId = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);

if (!$leadId || $leadId < 1) {
    http_response_code(400);
    exit('A valid lead_id is required.');
}

try {
    $pdo = getDatabaseConnection();

    $leadStmt = $pdo->prepare(
        "SELECT id, 
                COALESCE(NULLIF(name, ''), CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) AS name,
                email, phone, status, stage
         FROM leads
         WHERE id = :lead_id"
    );
    $leadStmt->execute(['lead_id' => $leadId]);
    $lead = $leadStmt->fetch(PDO::FETCH_ASSOC);

    if (!$lead) {
        http_response_code(404);
        exit('Lead not found.');
    }

    $activityStmt = $pdo->prepare(
        "SELECT id, 
                COALESCE(activity_type, action, 'Activity') AS activity_type,
                COALESCE(description, note, '') AS description,
                created_by, created_at
         FROM activity_logs
         WHERE lead_id = :lead_id
         ORDER BY created_at DESC, id DESC"
    );
    $activityStmt->execute(['lead_id' => $leadId]);
    $activities = $activityStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    http_response_code(500);
    exit('Database error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Details — <?= e($lead['name']) ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; background: #f8fafc; color: #1e293b; }
        .container { max-width: 860px; margin: 36px auto; padding: 0 20px; }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 16px; }
        h1, h2 { margin: 0; font-size: 20px; }
        .lead-meta { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; font-size: 14px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; text-transform: uppercase; background: #e0e7ff; color: #4338ca; }
        .activity-timeline { position: relative; padding-left: 20px; border-left: 2px solid #e2e8f0; margin-left: 8px; }
        .activity-item { position: relative; margin-bottom: 24px; }
        .activity-item:last-child { margin-bottom: 0; }
        .activity-dot { position: absolute; left: -26px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: #6366f1; border: 2px solid #fff; box-shadow: 0 0 0 2px #6366f1; }
        .activity-title { font-weight: 600; font-size: 14px; color: #0f172a; }
        .activity-time { font-size: 12px; color: #64748b; margin-top: 2px; }
        .activity-desc { font-size: 14px; color: #334155; margin-top: 6px; }
        .empty { color: #64748b; font-style: italic; font-size: 14px; }
        .btn-back { display: inline-block; margin-bottom: 16px; text-decoration: none; color: #4f46e5; font-size: 14px; font-weight: 500; }
    </style>
</head>
<body>
<div class="container">
    <a href="javascript:history.back()" class="btn-back">← Back</a>
    <div class="card">
        <div class="header">
            <h1><?= e($lead['name']) ?></h1>
            <span class="badge"><?= e($lead['status'] ?? $lead['stage'] ?? 'new') ?></span>
        </div>
        <div class="lead-meta">
            <div><strong>Email:</strong> <?= e($lead['email']) ?></div>
            <div><strong>Phone:</strong> <?= e($lead['phone']) ?></div>
            <div><strong>Lead ID:</strong> #<?= (int)$lead['id'] ?></div>
        </div>
    </div>

    <div class="card">
        <div class="header">
            <h2>Activity History</h2>
            <a href="add_note.php?lead_id=<?= (int)$lead['id'] ?>" style="font-size: 13px; color: #4f46e5; text-decoration: none; font-weight: 600;">+ Add Note</a>
        </div>

        <?php if (empty($activities)): ?>
            <p class="empty">No activity history found for this lead.</p>
        <?php else: ?>
            <div class="activity-timeline">
                <?php foreach ($activities as $act): ?>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div class="activity-title"><?= e($act['activity_type']) ?></div>
                        <div class="activity-time">
                            <?= e($act['created_at']) ?>
                            <?php if (!empty($act['created_by'])): ?>
                                · By <?= e($act['created_by']) ?>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($act['description'])): ?>
                            <div class="activity-desc"><?= nl2br(e($act['description'])) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
