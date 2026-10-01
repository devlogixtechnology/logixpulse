<?php
/**
 * LogixPulse — Client Timeline / Milestone Feed Endpoint
 * Task: REC-FEB-03 - Connect Real-Time Client Timeline & Milestone Feed
 *
 * GET only. Returns the logged-in client's milestones as JSON,
 * in chronological order (oldest first), each with its attachments.
 *
 * Security: the client is identified ONLY by the session
 * ($_SESSION['user_id']). Any client_id sent in the request is ignored,
 * so a client can never see another client's milestones.
 */

session_start();

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');            // always return fresh status values
header('X-Content-Type-Options: nosniff');

/** Send a JSON error and stop. */
function timelineError(int $code, string $message): void
{
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message]);
    exit();
}

/**
 * Turn whatever is stored in the status column into one of:
 * 'completed', 'in_progress', 'upcoming'.
 * Unknown values are treated as 'upcoming' so they are never shown as done.
 */
function normalizeMilestoneStatus($raw): string
{
    $value = strtolower(trim((string) $raw));
    $value = preg_replace('/[\s\-]+/', '_', $value);

    $completed  = ['completed', 'complete', 'done', 'finished', 'delivered'];
    $inProgress = ['in_progress', 'inprogress', 'active', 'current', 'ongoing', 'started'];

    if (in_array($value, $completed, true)) {
        return 'completed';
    }
    if (in_array($value, $inProgress, true)) {
        return 'in_progress';
    }
    return 'upcoming'; // 'upcoming', 'pending', 'planned', 'not_started', unknown...
}

/** Date + time, e.g. "Sep 8, 2026, 11:05 AM" (null when empty/invalid). */
function formatTimelineDateTime($value): ?string
{
    if (empty($value)) {
        return null;
    }
    try {
        return (new DateTime($value))->format('M j, Y, g:i A');
    } catch (Exception $e) {
        return null;
    }
}

/** Date only, e.g. "Oct 10, 2026" (same format as formatDate() in index.php). */
function formatTimelineDate($value): ?string
{
    if (empty($value)) {
        return null;
    }
    try {
        return (new DateTime($value))->format('M j, Y');
    } catch (Exception $e) {
        return null;
    }
}

/** Bytes -> "2.4 MB" (null when unknown). */
function formatTimelineFileSize($bytes): ?string
{
    if ($bytes === null || $bytes === '') {
        return null;
    }
    $bytes = (float) $bytes;
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 1) . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024) . ' KB';
    }
    return (int) $bytes . ' B';
}

// ---- Only GET ----
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    timelineError(405, 'Method not allowed');
}

// ---- Must be an authenticated client (same check as the dashboard) ----
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'client') {
    timelineError(401, 'Unauthorized');
}

$clientId = (int) $_SESSION['user_id'];
session_write_close(); // done with the session; don't block other requests

try {
    $pdo = getDatabaseConnection();

    // Milestones: ONLY this client's rows.
    // Chronological = completion time, else start time, else planned due date.
    $stmt = $pdo->prepare('
        SELECT m.id, m.title, m.status, m.notes,
               m.started_at, m.completed_at, m.due_date,
               p.name AS project_name
        FROM project_milestones m
        LEFT JOIN projects p ON p.id = m.project_id
        WHERE m.client_id = :client_id
        ORDER BY COALESCE(m.completed_at, m.started_at, CAST(m.due_date AS DATETIME), m.created_at) ASC,
                 m.id ASC
    ');
    $stmt->execute([':client_id' => $clientId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Attachments for this client's milestones (joined back to the client so
    // an attachment can never be returned for someone else's milestone).
    $attachmentsByMilestone = [];
    if (!empty($rows)) {
        $attStmt = $pdo->prepare('
            SELECT a.milestone_id, a.file_name, a.file_size
            FROM milestone_attachments a
            INNER JOIN project_milestones m ON m.id = a.milestone_id
            WHERE m.client_id = :client_id
            ORDER BY a.id ASC
        ');
        $attStmt->execute([':client_id' => $clientId]);

        foreach ($attStmt->fetchAll(PDO::FETCH_ASSOC) as $att) {
            $attachmentsByMilestone[(int) $att['milestone_id']][] = [
                'name'       => $att['file_name'],
                'size_label' => formatTimelineFileSize($att['file_size']),
            ];
        }
    }

    $statusLabels = [
        'completed'   => 'Completed',
        'in_progress' => 'In Progress',
        'upcoming'    => 'Upcoming',
    ];

    $milestones = [];
    foreach ($rows as $row) {
        $status = normalizeMilestoneStatus($row['status']);
        $notes  = trim((string) ($row['notes'] ?? ''));
        $id     = (int) $row['id'];

        $milestones[] = [
            'id'              => $id,
            'title'           => $row['title'],
            'status'          => $status,
            'status_label'    => $statusLabels[$status],
            'project_name'    => $row['project_name'],
            // A completed date is only sent for completed milestones, so an
            // upcoming/in-progress item can never look "completed".
            'completed_label' => $status === 'completed' ? formatTimelineDateTime($row['completed_at']) : null,
            'started_label'   => formatTimelineDate($row['started_at']),
            'due_label'       => formatTimelineDate($row['due_date']),
            'notes'           => $notes !== '' ? $notes : null,
            'attachments'     => $attachmentsByMilestone[$id] ?? [],
        ];
    }

    echo json_encode([
        'success'    => true,
        'count'      => count($milestones),
        'milestones' => $milestones,
    ], JSON_INVALID_UTF8_SUBSTITUTE);

} catch (Throwable $e) {
    error_log('client_timeline.php failed: ' . $e->getMessage());
    timelineError(500, 'Unable to load your project timeline. Please try again later.');
}
