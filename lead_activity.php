<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

$leadId = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);

if (!$leadId || $leadId < 1) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'A valid lead_id is required.'
    ]);
    exit;
}

try {
    // Fetch every activity for the selected lead, newest first.
    $stmt = $pdo->prepare(
        'SELECT
            id,
            lead_id,
            activity_type,
            description,
            created_by,
            created_at
         FROM activity_logs
         WHERE lead_id = :lead_id
         ORDER BY created_at DESC, id DESC'
    );

    $stmt->execute(['lead_id' => $leadId]);
    $activities = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'lead_id' => $leadId,
        'count' => count($activities),
        'activities' => $activities
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to fetch activity history.'
    ]);
}
