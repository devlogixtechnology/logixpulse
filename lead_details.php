<?php
declare(strict_types=1);

require_once __DIR__ . '/config/db.php';

$leadId = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);

if (!$leadId || $leadId < 1) {
    http_response_code(400);
    exit('A valid lead_id is required.');
}

$leadStmt = $pdo->prepare(
    'SELECT id, name, email, phone, status
     FROM leads
     WHERE id = :lead_id'
);
$leadStmt->execute(['lead_id' => $leadId]);
$lead = $leadStmt->fetch();

if (!$lead) {
    http_response_code(404);
    exit('Lead not found.');
}

$activityStmt = $pdo->prepare(
    'SELECT id, activity_type, description, created_by, created_at
     FROM activity_logs
     WHERE lead_id = :lead_id
     ORDER BY created_at DESC, id DESC'
);
$activityStmt->execute(['lead_id' => $leadId]);
$activities = $activityStmt->fetchAll();

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Details - Activity History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f5f7fa;
            color: #202938;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .card {
            background: #fff;
            border: 1px solid #e2e6ec;
            border-radius: 10px;
            padding: 24px;
            margin-bottom: 20px;
        }
        h1, h2 { margin-top: 0; }
        .lead-meta {
            display: grid;
            gap: 8px;
        }
        .activity {
            border-left: 3px solid #6b7280;
            padding: 0 0 18px 16px;
            margin: 0 0 18px 5px;
        }
        .activity:last-child {
            margin-bottom: 0;
        }
        .activity-title {
            font-weight: 700;
            margin-bottom: 5px;
        }
        .activity-time {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 7px;
        }
        .empty {
            color: #6b7280;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1><?= e($lead['name']) ?></h1>
        <div class="lead-meta">
            <div><strong>Email:</strong> <?= e($lead['email']) ?></div>
            <div><strong>Phone:</strong> <?= e($lead['phone']) ?></div>
            <div><strong>Status:</strong> <?= e($lead['status']) ?></div>
        </div>
    </div>

    <div class="card">
        <h2>Activity History</h2>

        <?php if (!$activities): ?>
            <p class="empty">No activity history found for this lead.</p>
        <?php else: ?>
            <?php foreach ($activities as $activity): ?>
                <div class="activity">
                    <div class="activity-title">
                        <?= e($activity['activity_type']) ?>
                    </div>
                    <div class="activity-time">
                        <?= e($activity['created_at']) ?>
                        <?php if ($activity['created_by'] !== null && $activity['created_by'] !== ''): ?>
                            · By <?= e($activity['created_by']) ?>
                        <?php endif; ?>
                    </div>
                    <div><?= e($activity['description']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
