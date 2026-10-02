<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST method is allowed']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?? [];
if (!is_array($data)) {
    $data = [];
}

$leadId = isset($data['lead_id']) ? (int)$data['lead_id'] : (int)($_POST['lead_id'] ?? 0);
$newStage = trim((string)($data['new_stage'] ?? $data['stage'] ?? $data['status'] ?? $data['new_status'] ?? $_POST['new_stage'] ?? $_POST['stage'] ?? $_POST['status'] ?? ''));


if ($leadId <= 0 || $newStage === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'lead_id and new_stage/status are required']);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    $pdo->beginTransaction();

    // Check existing lead
    $stmt = $pdo->prepare('SELECT * FROM leads WHERE id = ? FOR UPDATE');
    $stmt->execute([$leadId]);
    $lead = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lead) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Lead not found']);
        exit;
    }

    $oldStage = $lead['status'] ?? $lead['stage'] ?? 'new';

    if (strcasecmp((string)$oldStage, $newStage) === 0) {
        $pdo->commit();
        echo json_encode([
            'success'         => true,
            'message'         => 'Lead is already in this stage',
            'lead_id'         => $leadId,
            'old_stage'       => $oldStage,
            'new_stage'       => $newStage,
            'activity_logged' => false
        ]);
        exit;
    }

    // Determine available columns in leads table
    $hasStatusCol = array_key_exists('status', $lead);
    $hasStageCol  = array_key_exists('stage', $lead);

    if ($hasStatusCol && $hasStageCol) {
        $update = $pdo->prepare('UPDATE leads SET status = ?, stage = ?, updated_at = NOW() WHERE id = ?');
        $update->execute([$newStage, $newStage, $leadId]);
    } elseif ($hasStatusCol) {
        $update = $pdo->prepare('UPDATE leads SET status = ?, updated_at = NOW() WHERE id = ?');
        $update->execute([$newStage, $leadId]);
    } else {
        $update = $pdo->prepare('UPDATE leads SET stage = ? WHERE id = ?');
        $update->execute([$newStage, $leadId]);
    }

    $note = "Moved from {$oldStage} to {$newStage}.";

    // Insert into activity_logs
    try {
        $log = $pdo->prepare(
            "INSERT INTO activity_logs (lead_id, activity_type, description, note, created_by, created_at)
             VALUES (?, 'Stage Changed', ?, ?, 'System', NOW())"
        );
        $log->execute([$leadId, $note, $note]);
    } catch (Throwable $logEx) {
        // Fallback for minimal activity_logs schema (lead_id, note)
        try {
            $log = $pdo->prepare('INSERT INTO activity_logs (lead_id, note) VALUES (?, ?)');
            $log->execute([$leadId, $note]);
        } catch (Throwable $e) {
            error_log('[update_lead_stage] activity_log insert fallback: ' . $e->getMessage());
        }
    }

    $pdo->commit();

    echo json_encode([
        'success'         => true,
        'message'         => 'Lead stage updated successfully',
        'lead_id'         => $leadId,
        'old_stage'       => $oldStage,
        'new_stage'       => $newStage,
        'activity'        => $note,
        'activity_logged' => true
    ]);

} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to update lead stage: ' . $e->getMessage()
    ]);
}