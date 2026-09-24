<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$leadId = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);
if (!$leadId) {
    $leadId = filter_input(INPUT_POST, 'lead_id', FILTER_VALIDATE_INT);
}

if (!$leadId) {
    $leadId = 1;
}

try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare(
        'SELECT id, 
                COALESCE(NULLIF(name, ""), CONCAT(COALESCE(first_name, ""), " ", COALESCE(last_name, ""))) AS name,
                email, phone 
         FROM leads WHERE id = ?'
    );
    $stmt->execute([$leadId]);
    $lead = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lead) {
        http_response_code(404);
        exit('Lead not found.');
    }
} catch (Throwable $e) {
    http_response_code(500);
    exit('Database error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true);

    $note = trim((string)($jsonData['note'] ?? $_POST['note'] ?? ''));

    if ($note === '') {
        $errors[] = 'Please enter a note.';
    } elseif (mb_strlen($note) > 5000) {
        $errors[] = 'Note must be 5000 characters or less.';
    }

    if (empty($errors)) {
        try {
            $insert = $pdo->prepare(
                'INSERT INTO activity_logs (lead_id, activity_type, note, description, created_by, created_at)
                 VALUES (:lead_id, "note", :note, :desc, "User", NOW())'
            );
            $insert->execute([
                ':lead_id' => $leadId,
                ':note'    => $note,
                ':desc'    => $note,
            ]);

            if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => true, 'message' => 'Note saved successfully.']);
                exit;
            }

            header('Location: activity_history.php?lead_id=' . $leadId . '&saved=1');
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Failed to save note: ' . $e->getMessage();
        }
    }
}

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Manual Note - <?= e($lead['name']) ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 32px 16px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        h1 { font-size: 18px; margin-top: 0; margin-bottom: 8px; }
        .subtitle { font-size: 13px; color: #64748b; margin-bottom: 20px; }
        textarea { width: 100%; min-height: 120px; box-sizing: border-box; border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px; font-family: inherit; font-size: 14px; }
        textarea:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
        .btn { display: inline-block; background: #4f46e5; color: #fff; padding: 10px 18px; border-radius: 8px; border: none; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #4338ca; }
        .btn-cancel { background: transparent; color: #64748b; margin-left: 12px; }
        .btn-cancel:hover { color: #1e293b; background: transparent; }
        .errors { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .actions { margin-top: 16px; display: flex; align-items: center; }
    </style>
</head>
<body>
<div class="container">
    <h1>Add Manual Note</h1>
    <div class="subtitle">Adding note for lead: <strong><?= e($lead['name']) ?></strong> (<?= e($lead['email']) ?>)</div>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $err): ?>
                <div><?= e($err) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="add_note.php?lead_id=<?= (int)$leadId ?>">
        <input type="hidden" name="lead_id" value="<?= (int)$leadId ?>">
        <label for="note" style="display: block; font-weight: 500; font-size: 14px; margin-bottom: 6px;">Note Content</label>
        <textarea id="note" name="note" placeholder="Write internal activity note..." required><?= e($_POST['note'] ?? '') ?></textarea>
        <div class="actions">
            <button type="submit" class="btn">Save Note</button>
            <a href="lead_details.php?lead_id=<?= (int)$leadId ?>" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
