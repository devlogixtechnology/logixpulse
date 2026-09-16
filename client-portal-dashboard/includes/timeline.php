<?php
/**
 * includes/timeline.php
 * Task: FEB-W7D3-1 - Connect the Timeline to Real Data
 *
 * Replaces the hardcoded timeline steps on the dashboard with the
 * logged-in client's real current phase, pulled from the database.
 * Falls back to an in-memory mock dataset with the same shape if the
 * database isn't reachable, so the dashboard never breaks.
 */

require_once __DIR__ . '/../../config/database.php';

/**
 * Mock dataset used only when the database connection fails.
 * Same shape as the `timeline_steps` table, keyed by client (user) id.
 */
function getMockTimelineDataset(): array
{
    return [
        10 => [
            'current_step_order' => 3,
            'steps' => [
                ['step_order' => 1, 'title' => 'Project Started', 'date_label' => 'Jan 6'],
                ['step_order' => 2, 'title' => 'Requirements & Planning', 'date_label' => 'Jan 20'],
                ['step_order' => 3, 'title' => 'Development', 'date_label' => 'Est. Feb 28'],
                ['step_order' => 4, 'title' => 'Review & Approval', 'date_label' => 'Est. Mar 10'],
                ['step_order' => 5, 'title' => 'Delivery', 'date_label' => 'Est. Mar 20'],
            ],
        ],
        11 => [
            'current_step_order' => 2,
            'steps' => [
                ['step_order' => 1, 'title' => 'Project Started', 'date_label' => 'Feb 2'],
                ['step_order' => 2, 'title' => 'Requirements & Planning', 'date_label' => 'Est. Feb 24'],
                ['step_order' => 3, 'title' => 'Development', 'date_label' => 'Est. Mar 15'],
                ['step_order' => 4, 'title' => 'Review & Approval', 'date_label' => 'Est. Mar 28'],
                ['step_order' => 5, 'title' => 'Delivery', 'date_label' => 'Est. Apr 5'],
            ],
        ],
    ];
}

/**
 * Returns the logged-in client's timeline: each step with a computed
 * status (completed / current / upcoming) and the overall progress
 * percentage used for the track fill.
 *
 * @return array{progress:float, steps:array}|null
 */
function getClientTimeline(int $clientId): ?array
{
    $currentStepOrder = null;
    $steps = [];

    try {
        $pdo = getDatabaseConnection();

        $progressStmt = $pdo->prepare(
            'SELECT current_step_order FROM client_timeline_progress WHERE client_id = :client_id'
        );
        $progressStmt->execute([':client_id' => $clientId]);
        $progressRow = $progressStmt->fetch(PDO::FETCH_ASSOC);

        if ($progressRow) {
            $currentStepOrder = (int) $progressRow['current_step_order'];

            $stepsStmt = $pdo->prepare(
                'SELECT step_order, title, date_label FROM timeline_steps WHERE client_id = :client_id ORDER BY step_order ASC'
            );
            $stepsStmt->execute([':client_id' => $clientId]);
            $steps = $stepsStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $currentStepOrder = null;
        $steps = [];
    }

    // No row found in the DB (or DB unreachable) - fall back to mock data.
    if ($currentStepOrder === null) {
        $dataset = getMockTimelineDataset();

        if (!isset($dataset[$clientId])) {
            return null;
        }

        $currentStepOrder = $dataset[$clientId]['current_step_order'];
        $steps = $dataset[$clientId]['steps'];
    }

    if (empty($steps)) {
        return null;
    }

    $totalSteps = count($steps);
    $formattedSteps = [];

    foreach ($steps as $step) {
        if ($step['step_order'] < $currentStepOrder) {
            $status = 'completed';
            $statusLabel = 'Completed';
        } elseif ($step['step_order'] == $currentStepOrder) {
            $status = 'current';
            $statusLabel = 'In Progress';
        } else {
            $status = 'upcoming';
            $statusLabel = 'Upcoming';
        }

        $formattedSteps[] = [
            'title' => $step['title'],
            'date_label' => $step['date_label'],
            'status' => $status,
            'status_label' => $statusLabel,
        ];
    }

    // Progress fill: 0% at step 1, 100% at the last step.
    $completedCount = max(0, $currentStepOrder - 1);
    $progress = $totalSteps > 1 ? ($completedCount / ($totalSteps - 1)) * 100 : 0;

    return [
        'progress' => round($progress, 1),
        'steps' => $formattedSteps,
    ];
}
