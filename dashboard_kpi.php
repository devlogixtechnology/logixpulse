<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

function tableExists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM information_schema.tables
         WHERE table_schema = DATABASE() AND table_name = ?"
    );
    $stmt->execute([$table]);
    return (bool)$stmt->fetchColumn();
}

function columns(PDO $pdo, string $table): array
{
    $stmt = $pdo->prepare(
        "SELECT COLUMN_NAME FROM information_schema.columns
         WHERE table_schema = DATABASE() AND table_name = ?"
    );
    $stmt->execute([$table]);
    return array_map('strtolower', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

function firstColumn(array $available, array $wanted): ?string
{
    foreach ($wanted as $name) {
        if (in_array(strtolower($name), $available, true)) {
            return $name;
        }
    }
    return null;
}

function percentageChange(int $current, int $previous): float
{
    if ($previous === 0) {
        return $current === 0 ? 0.0 : 100.0;
    }
    return round((($current - $previous) / $previous) * 100, 2);
}

function kpi(string $label, int $current, int $previous): array
{
    return [
        "label" => $label,
        "value" => $current,
        "previous_value" => $previous,
        "percentage_change" => percentageChange($current, $previous)
    ];
}

/*
 * Periods:
 * - Total Leads: current month-to-date vs the equivalent number of days
 *   in the previous month.
 * - Active Deals: current month-to-date vs equivalent previous-month period.
 * - Meetings This Week: current week vs the previous week.
 * - Closed Deals: current month-to-date vs equivalent previous-month period.
 *
 * Counts are read from the database; no KPI number is hardcoded.
 */

try {
    $now = new DateTimeImmutable('now');
    $monthStart = $now->modify('first day of this month')->setTime(0, 0, 0);
    $todayEnd = $now->setTime(23, 59, 59);

    $previousMonthStart = $monthStart->modify('-1 month');
    $elapsedDays = (int)$now->format('j');
    $previousMonthEnd = $previousMonthStart->modify('+' . ($elapsedDays - 1) . ' days')->setTime(23, 59, 59);

    // ---------- TOTAL LEADS ----------
    if (!tableExists($pdo, 'leads')) {
        throw new RuntimeException("Required table 'leads' was not found.");
    }

    $leadColumns = columns($pdo, 'leads');
    $leadDate = firstColumn($leadColumns, [
        'created_at', 'createdAt', 'date_created', 'created_date'
    ]);

    if (!$leadDate) {
        throw new RuntimeException("The leads table needs a created_at/createdAt/date_created/created_date column.");
    }

    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM leads
         WHERE `$leadDate` BETWEEN ? AND ?"
    );
    $stmt->execute([
        $monthStart->format('Y-m-d H:i:s'),
        $todayEnd->format('Y-m-d H:i:s')
    ]);
    $totalLeads = (int)$stmt->fetchColumn();

    $stmt->execute([
        $previousMonthStart->format('Y-m-d H:i:s'),
        $previousMonthEnd->format('Y-m-d H:i:s')
    ]);
    $previousLeads = (int)$stmt->fetchColumn();

    // ---------- ACTIVE DEALS ----------
    $activeDeals = 0;
    $previousActiveDeals = 0;
    $activeDealsSource = null;

    $dealTable = null;
    if (tableExists($pdo, 'deals')) {
        $dealTable = 'deals';
    } elseif (tableExists($pdo, 'projects')) {
        $dealTable = 'projects';
    }

    if (!$dealTable) {
        throw new RuntimeException("Required deals/projects table was not found.");
    }

    $dealColumns = columns($pdo, $dealTable);
    $dealDate = firstColumn($dealColumns, [
        'created_at', 'createdAt', 'start_date', 'start_at', 'date_created'
    ]);
    $dealStatus = firstColumn($dealColumns, ['status', 'deal_status', 'project_status']);

    if (!$dealDate) {
        throw new RuntimeException("$dealTable needs a date column for KPI period comparison.");
    }

    if ($dealStatus) {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM `$dealTable`
             WHERE `$dealDate` BETWEEN ? AND ?
             AND LOWER(`$dealStatus`) IN
             ('active','open','in progress','in_progress','negotiation','qualified')"
        );
    } else {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM `$dealTable`
             WHERE `$dealDate` BETWEEN ? AND ?"
        );
    }

    $stmt->execute([
        $monthStart->format('Y-m-d H:i:s'),
        $todayEnd->format('Y-m-d H:i:s')
    ]);
    $activeDeals = (int)$stmt->fetchColumn();

    $stmt->execute([
        $previousMonthStart->format('Y-m-d H:i:s'),
        $previousMonthEnd->format('Y-m-d H:i:s')
    ]);
    $previousActiveDeals = (int)$stmt->fetchColumn();

    $activeDealsSource = $dealTable;

    // ---------- MEETINGS THIS WEEK ----------
    $weekStart = $now->modify('monday this week')->setTime(0, 0, 0);
    $weekEnd = $weekStart->modify('+6 days')->setTime(23, 59, 59);
    $previousWeekStart = $weekStart->modify('-7 days');
    $previousWeekEnd = $weekStart->modify('-1 second');

    $meetings = 0;
    $previousMeetings = 0;
    $meetingsSource = null;

    if (tableExists($pdo, 'meetings')) {
        $meetingTable = 'meetings';
        $meetingColumns = columns($pdo, $meetingTable);
        $meetingDate = firstColumn($meetingColumns, [
            'meeting_date', 'scheduled_at', 'start_at', 'created_at', 'date'
        ]);

        if (!$meetingDate) {
            throw new RuntimeException("The meetings table needs a meeting date/time column.");
        }

        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM `$meetingTable`
             WHERE `$meetingDate` BETWEEN ? AND ?"
        );
        $stmt->execute([
            $weekStart->format('Y-m-d H:i:s'),
            $weekEnd->format('Y-m-d H:i:s')
        ]);
        $meetings = (int)$stmt->fetchColumn();

        $stmt->execute([
            $previousWeekStart->format('Y-m-d H:i:s'),
            $previousWeekEnd->format('Y-m-d H:i:s')
        ]);
        $previousMeetings = (int)$stmt->fetchColumn();
        $meetingsSource = $meetingTable;
    } elseif (tableExists($pdo, 'projects')) {
        // Some versions of the project store meetings against projects.
        $projectColumns = columns($pdo, 'projects');
        $meetingDate = firstColumn($projectColumns, [
            'meeting_date', 'next_meeting_at', 'scheduled_meeting_at'
        ]);

        if (!$meetingDate) {
            throw new RuntimeException(
                "Meetings KPI needs a meetings table or a meeting_date/next_meeting_at/scheduled_meeting_at column in projects."
            );
        }

        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM projects
             WHERE `$meetingDate` BETWEEN ? AND ?"
        );
        $stmt->execute([
            $weekStart->format('Y-m-d H:i:s'),
            $weekEnd->format('Y-m-d H:i:s')
        ]);
        $meetings = (int)$stmt->fetchColumn();

        $stmt->execute([
            $previousWeekStart->format('Y-m-d H:i:s'),
            $previousWeekEnd->format('Y-m-d H:i:s')
        ]);
        $previousMeetings = (int)$stmt->fetchColumn();
        $meetingsSource = 'projects';
    } else {
        throw new RuntimeException("Required meetings/projects data source was not found.");
    }

    // ---------- CLOSED DEALS (MTD) ----------
    $closedDeals = 0;
    $previousClosedDeals = 0;
    $closedDealsSource = null;

    $closedTable = null;
    if (tableExists($pdo, 'deals')) {
        $closedTable = 'deals';
    } elseif (tableExists($pdo, 'invoices')) {
        $closedTable = 'invoices';
    } elseif (tableExists($pdo, 'projects')) {
        $closedTable = 'projects';
    }

    if (!$closedTable) {
        throw new RuntimeException("Required invoices/deals/projects table was not found.");
    }

    $closedColumns = columns($pdo, $closedTable);
    $closedDate = firstColumn($closedColumns, [
        'closed_at', 'closed_date', 'paid_at', 'completed_at',
        'invoice_date', 'created_at', 'createdAt', 'date_created'
    ]);
    $closedStatus = firstColumn($closedColumns, [
        'status', 'deal_status', 'project_status', 'payment_status'
    ]);

    if (!$closedDate) {
        throw new RuntimeException("$closedTable needs a date column for closed-deals KPI.");
    }

    if ($closedStatus) {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM `$closedTable`
             WHERE `$closedDate` BETWEEN ? AND ?
             AND LOWER(`$closedStatus`) IN
             ('closed','won','completed','paid','settled')"
        );
    } else {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM `$closedTable`
             WHERE `$closedDate` BETWEEN ? AND ?"
        );
    }

    $stmt->execute([
        $monthStart->format('Y-m-d H:i:s'),
        $todayEnd->format('Y-m-d H:i:s')
    ]);
    $closedDeals = (int)$stmt->fetchColumn();

    $stmt->execute([
        $previousMonthStart->format('Y-m-d H:i:s'),
        $previousMonthEnd->format('Y-m-d H:i:s')
    ]);
    $previousClosedDeals = (int)$stmt->fetchColumn();

    $closedDealsSource = $closedTable;

    echo json_encode([
        "success" => true,
        "periods" => [
            "month_to_date" => [
                "start" => $monthStart->format('Y-m-d'),
                "end" => $now->format('Y-m-d')
            ],
            "week_to_date" => [
                "start" => $weekStart->format('Y-m-d'),
                "end" => $now->format('Y-m-d')
            ]
        ],
        "kpis" => [
            "total_leads" => kpi("Total Leads", $totalLeads, $previousLeads),
            "active_deals" => kpi("Active Deals", $activeDeals, $previousActiveDeals),
            "meetings_this_week" => kpi("Meetings This Week", $meetings, $previousMeetings),
            "closed_deals_mtd" => kpi("Closed Deals (Month-to-Date)", $closedDeals, $previousClosedDeals)
        ],
        "data_sources" => [
            "leads" => "leads",
            "active_deals" => $activeDealsSource,
            "meetings" => $meetingsSource,
            "closed_deals" => $closedDealsSource
        ]
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
