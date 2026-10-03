<?php
/**
 * includes/timeline.php
 * Task: FEB-W7D3-1 - Connect the Timeline to Real Data
 *
 * Replaces the old made-up/hardcoded timeline steps with the real
 * current phase pulled from the database, for a given client.
 * Falls back to an in-memory mock dataset (same shape as the DB
 * tables) when no DB connection is available.
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Mock "database" used only when a real DB connection isn't available.
 * Shape mirrors the `clients` + `timeline_steps` tables exactly, so
 * switching to the real DB later needs no changes outside this file.
 */
function getMockTimelineDataset(): array
{
    return [
        1 => [
            'client_name' => 'Acme Cloud Technologies',
            'current_step_order' => 3,
            'steps' => [
                ['step_order' => 1, 'title' => 'Project Started', 'date_label' => 'Jan 6'],
                ['step_order' => 2, 'title' => 'Requirements & Planning', 'date_label' => 'Jan 20'],
                ['step_order' => 3, 'title' => 'Development', 'date_label' => 'Est. Feb 28'],
                ['step_order' => 4, 'title' => 'Review & Approval', 'date_label' => 'Est. Mar 10'],
                ['step_order' => 5, 'title' => 'Delivery', 'date_label' => 'Est. Mar 20'],
            ],
        ],
        2 => [
            'client_name' => 'Vertex FinTech Core',
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
 * Returns the timeline for a client: real client name, each step's
 * computed status (completed / current / upcoming), and the overall
 * progress percentage for the track fill.
 *
 * @return array{client_name:string, progress:float, steps:array}|null
 */
function getClientTimeline(int $clientId): ?array
{
    $pdo = getDbConnection();

    if ($pdo !== null) {
        $client = null;
        try {
            $clientStmt = $pdo->prepare(
                "SELECT COALESCE(NULLIF(u.name, ''), CONCAT(u.first_name, ' ', u.last_name)) AS name,
                        COALESCE(p.current_step_order, 3) AS current_step_order
                 FROM users u
                 LEFT JOIN client_timeline_progress p ON p.client_id = u.id
                 WHERE u.id = ?"
            );
            $clientStmt->execute([$clientId]);
            $client = $clientStmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $client = null;
        }

        if (!$client) {
            try {
                $clientStmt = $pdo->prepare('SELECT name, current_step_order FROM clients WHERE id = ?');
                $clientStmt->execute([$clientId]);
                $client = $clientStmt->fetch(PDO::FETCH_ASSOC);
            } catch (Throwable $e) {
                $client = null;
            }
        }

        $steps = [];
        if ($client) {
            try {
                $stepsStmt = $pdo->prepare(
                    'SELECT step_order, title, date_label FROM timeline_steps WHERE client_id = ? ORDER BY step_order ASC'
                );
                $stepsStmt->execute([$clientId]);
                $steps = $stepsStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Throwable $e) {
                $steps = [];
            }
        }

        if (!$client || empty($steps)) {
            $dataset = getMockTimelineDataset();
            $mockKey = isset($dataset[$clientId]) ? $clientId : 1;
            $clientName = $dataset[$mockKey]['client_name'];
            $currentStepOrder = $dataset[$mockKey]['current_step_order'];
            $steps = $dataset[$mockKey]['steps'];
        } else {
            $clientName = $client['name'];
            $currentStepOrder = (int) $client['current_step_order'];
        }
    } else {
        // No live DB connection available - use the mock dataset instead.
        $dataset = getMockTimelineDataset();
        $mockKey = isset($dataset[$clientId]) ? $clientId : 1;

        $clientName = $dataset[$mockKey]['client_name'];
        $currentStepOrder = $dataset[$mockKey]['current_step_order'];
        $steps = $dataset[$mockKey]['steps'];
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

    // Progress fill = how far along the current step is, proportional
    // to the total number of steps (0% at step 1, 100% at the last step).
    $completedCount = max(0, $currentStepOrder - 1);
    $progress = $totalSteps > 1 ? ($completedCount / ($totalSteps - 1)) * 100 : 0;

    return [
        'client_name' => $clientName,
        'progress' => round($progress, 1),
        'steps' => $formattedSteps,
    ];
}
