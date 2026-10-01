<?php
declare(strict_types=1);

/**
 * Local verification helper.
 * It is intentionally simple so it can be opened directly in XAMPP.
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../services/LeadStageService.php';

$pdo = db();

if (isset($_POST['create_demo'])) {
    $stmt = $pdo->prepare(
        "INSERT INTO leads (name, email, company, stage)
         VALUES ('Test Lead', 'test@example.com', 'Test Company', 'New Lead')"
    );
    $stmt->execute();

    $leadId = (int)$pdo->lastInsertId();

    $log = $pdo->prepare(
        "INSERT INTO lead_activity_log
            (lead_id, user_id, old_stage, new_stage, transition_metadata)
         VALUES
            (:lead_id, 1, NULL, 'New Lead', :metadata)"
    );
    $log->execute([
        ':lead_id' => $leadId,
        ':metadata' => json_encode([
            'event' => 'lead_created',
            'source' => 'manual_test'
        ])
    ]);

    $_SESSION['test_lead_id'] = $leadId;
}

$leadId = (int)($_SESSION['test_lead_id'] ?? 0);
$message = '';

if ($leadId > 0 && isset($_POST['transition'])) {
    $newStage = (string)$_POST['transition'];

    try {
        $service = new LeadStageService($pdo);

        $result = $service->transition(
            $leadId,
            (int)$_SESSION['user_id'],
            $newStage,
            [
                'source' => 'manual_test',
                'button' => 'transition'
            ]
        );

        $message = 'SUCCESS: ' . $result['old_stage'] . ' -> ' . $result['new_stage'];
    } catch (Throwable $e) {
        $message = 'REJECTED/ERROR: ' . $e->getMessage();
    }
}

$logs = [];

if ($leadId > 0) {
    $stmt = $pdo->prepare(
        'SELECT id, user_id, old_stage, new_stage, created_at, transition_metadata
         FROM lead_activity_log
         WHERE lead_id = :lead_id
         ORDER BY created_at ASC, id ASC'
    );
    $stmt->execute([':lead_id' => $leadId]);
    $logs = $stmt->fetchAll();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BE-02 Manual Verification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; line-height: 1.5; }
        button { margin: 4px; padding: 8px 12px; cursor: pointer; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        code { background: #eee; padding: 2px 4px; }
    </style>
</head>
<body>

<h1>BE-02 — Manual Verification</h1>

<p>Session user ID: <strong><?= (int)$_SESSION['user_id'] ?></strong></p>

<form method="post">
    <button type="submit" name="create_demo" value="1">
        Create New Test Lead
    </button>
</form>

<?php if ($message !== ''): ?>
    <p><strong><?= htmlspecialchars($message) ?></strong></p>
<?php endif; ?>

<?php if ($leadId > 0): ?>
    <p>Current test lead ID: <strong><?= $leadId ?></strong></p>

    <h2>Valid progression</h2>
    <form method="post">
        <button name="transition" value="Contacted">New Lead → Contacted</button>
        <button name="transition" value="Qualified">Contacted → Qualified</button>
        <button name="transition" value="Proposal Sent">Qualified → Proposal Sent</button>
        <button name="transition" value="Won">Proposal Sent → Won</button>
        <button name="transition" value="Lost">Proposal Sent → Lost</button>
    </form>

    <h2>Illegal transition test</h2>
    <p>
        Create a fresh lead and click <code>Qualified</code> before
        <code>Contacted</code>. It must be rejected with an invalid transition error.
    </p>

    <form method="post">
        <button name="transition" value="Qualified">
            Attempt New Lead → Qualified (should reject)
        </button>
    </form>

    <h2>Activity log</h2>

    <?php if (!$logs): ?>
        <p>No activity records found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Old Stage</th>
                    <th>New Stage</th>
                    <th>Created At</th>
                    <th>Metadata</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= (int)$log['id'] ?></td>
                    <td><?= (int)$log['user_id'] ?></td>
                    <td><?= htmlspecialchars((string)($log['old_stage'] ?? 'NULL')) ?></td>
                    <td><?= htmlspecialchars($log['new_stage']) ?></td>
                    <td><?= htmlspecialchars($log['created_at']) ?></td>
                    <td><?= htmlspecialchars($log['transition_metadata']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p>
        API log endpoint:
        <code>/BE-02_lead_stage_progression_audit/api/lead_activities.php?lead_id=<?= $leadId ?></code>
    </p>
<?php endif; ?>

</body>
</html>
