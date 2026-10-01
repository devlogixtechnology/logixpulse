<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');

    jsonResponse([
        'success' => false,
        'message' => 'Only GET is allowed for activity logs.'
    ], 405);
}

requireUserId();

$leadId = filter_var($_GET['lead_id'] ?? null, FILTER_VALIDATE_INT);

if (!$leadId || $leadId <= 0) {
    jsonResponse([
        'success' => false,
        'message' => 'lead_id is required and must be a positive integer.'
    ], 422);
}

try {
    $pdo = db();

    $leadStmt = $pdo->prepare(
        'SELECT id, stage, created_at, updated_at
         FROM leads
         WHERE id = :id'
    );
    $leadStmt->execute([':id' => $leadId]);
    $lead = $leadStmt->fetch();

    if (!$lead) {
        jsonResponse([
            'success' => false,
            'message' => 'Lead not found.'
        ], 404);
    }

    $stmt = $pdo->prepare(
        'SELECT
            id,
            lead_id,
            user_id,
            old_stage,
            new_stage,
            transition_metadata,
            created_at
         FROM lead_activity_log
         WHERE lead_id = :lead_id
         ORDER BY created_at ASC, id ASC'
    );
    $stmt->execute([':lead_id' => $leadId]);

    $rows = $stmt->fetchAll();

    $logs = [];
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

    foreach ($rows as $index => $row) {
        $started = new DateTimeImmutable(
            $row['created_at'],
            new DateTimeZone('UTC')
        );

        $nextRow = $rows[$index + 1] ?? null;

        if ($nextRow) {
            $ended = new DateTimeImmutable(
                $nextRow['created_at'],
                new DateTimeZone('UTC')
            );
        } else {
            $ended = $now;
        }

        $seconds = max(0, $ended->getTimestamp() - $started->getTimestamp());

        $metadata = json_decode($row['transition_metadata'], true);

        $logs[] = [
            'id' => (int)$row['id'],
            'lead_id' => (int)$row['lead_id'],
            'user_id' => (int)$row['user_id'],
            'old_stage' => $row['old_stage'],
            'new_stage' => $row['new_stage'],
            'transition_metadata' => is_array($metadata) ? $metadata : [],
            'created_at' => $row['created_at'],
            'time_spent_seconds' => $seconds,
            'time_spent_human' => formatDuration($seconds),
        ];
    }

    jsonResponse([
        'success' => true,
        'data' => [
            'lead' => [
                'id' => (int)$lead['id'],
                'current_stage' => $lead['stage'],
                'created_at' => $lead['created_at'],
                'updated_at' => $lead['updated_at'],
            ],
            'logs' => $logs,
        ]
    ]);
} catch (Throwable $e) {
    error_log($e->getMessage());

    jsonResponse([
        'success' => false,
        'error' => 'SERVER_ERROR',
        'message' => 'Unable to retrieve lead activity logs.'
    ], 500);
}

function formatDuration(int $seconds): string
{
    $days = intdiv($seconds, 86400);
    $seconds %= 86400;

    $hours = intdiv($seconds, 3600);
    $seconds %= 3600;

    $minutes = intdiv($seconds, 60);
    $seconds %= 60;

    $parts = [];

    if ($days > 0) {
        $parts[] = $days . 'd';
    }
    if ($hours > 0 || $days > 0) {
        $parts[] = $hours . 'h';
    }
    if ($minutes > 0 || $hours > 0 || $days > 0) {
        $parts[] = $minutes . 'm';
    }

    $parts[] = $seconds . 's';

    return implode(' ', $parts);
}
