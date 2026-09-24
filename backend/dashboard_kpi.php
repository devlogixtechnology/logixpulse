<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

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

try {
    $pdo = getDatabaseConnection();

    $now = new DateTimeImmutable('now');
    $monthStart = $now->modify('first day of this month')->setTime(0, 0, 0);
    $todayEnd = $now->setTime(23, 59, 59);

    $previousMonthStart = $monthStart->modify('-1 month');
    $elapsedDays = (int)$now->format('j');
    $previousMonthEnd = $previousMonthStart->modify('+' . ($elapsedDays - 1) . ' days')->setTime(23, 59, 59);

    // ---------- TOTAL LEADS ----------
    $totalLeads = 0;
    $previousLeads = 0;
    if (tableExists($pdo, 'leads')) {
        $leadColumns = columns($pdo, 'leads');
        $leadDate = firstColumn($leadColumns, ['created_at', 'createdat', 'date_created', 'created_date']);

        if ($leadDate) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM leads WHERE `$leadDate` BETWEEN ? AND ?");
            $stmt->execute([$monthStart->format('Y-m-d H:i:s'), $todayEnd->format('Y-m-d H:i:s')]);
            $totalLeads = (int)$stmt->fetchColumn();

            $stmt->execute([$previousMonthStart->format('Y-m-d H:i:s'), $previousMonthEnd->format('Y-m-d H:i:s')]);
            $previousLeads = (int)$stmt->fetchColumn();
        } else {
            $totalLeads = (int)$pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
        }
    }

    // ---------- ACTIVE DEALS / PROJECTS ----------
    $activeDeals = 0;
    $previousActiveDeals = 0;
    $activeDealsSource = 'none';

    $dealTable = null;
    if (tableExists($pdo, 'deals')) {
        $dealTable = 'deals';
    } elseif (tableExists($pdo, 'projects')) {
        $dealTable = 'projects';
    }

    if ($dealTable) {
        $dealColumns = columns($pdo, $dealTable);
        $dealDate = firstColumn($dealColumns, ['created_at', 'createdat', 'start_date', 'start_at', 'date_created']);
        $dealStatus = firstColumn($dealColumns, ['status', 'deal_status', 'project_status']);

        if ($dealDate && $dealStatus) {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM `$dealTable`
                 WHERE `$dealDate` BETWEEN ? AND ?
                 AND LOWER(`$dealStatus`) IN ('active','open','in progress','in_progress','negotiation','qualified')"
            );
            $stmt->execute([$monthStart->format('Y-m-d H:i:s'), $todayEnd->format('Y-m-d H:i:s')]);
            $activeDeals = (int)$stmt->fetchColumn();

            $stmt->execute([$previousMonthStart->format('Y-m-d H:i:s'), $previousMonthEnd->format('Y-m-d H:i:s')]);
            $previousActiveDeals = (int)$stmt->fetchColumn();
        } else {
            $activeDeals = (int)$pdo->query("SELECT COUNT(*) FROM `$dealTable`")->fetchColumn();
        }
        $activeDealsSource = $dealTable;
    }

    // ---------- MEETINGS THIS WEEK ----------
    $weekStart = $now->modify('monday this week')->setTime(0, 0, 0);
    $weekEnd = $weekStart->modify('+6 days')->setTime(23, 59, 59);
    $previousWeekStart = $weekStart->modify('-7 days');
    $previousWeekEnd = $weekStart->modify('-1 second');

    $meetings = 0;
    $previousMeetings = 0;
    $meetingsSource = 'none';

    if (tableExists($pdo, 'meetings')) {
        $meetingColumns = columns($pdo, 'meetings');
        $meetingDate = firstColumn($meetingColumns, ['meeting_date', 'scheduled_at', 'start_at', 'created_at', 'date']);
        if ($meetingDate) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `meetings` WHERE `$meetingDate` BETWEEN ? AND ?");
            $stmt->execute([$weekStart->format('Y-m-d H:i:s'), $weekEnd->format('Y-m-d H:i:s')]);
            $meetings = (int)$stmt->fetchColumn();

            $stmt->execute([$previousWeekStart->format('Y-m-d H:i:s'), $previousWeekEnd->format('Y-m-d H:i:s')]);
            $previousMeetings = (int)$stmt->fetchColumn();
            $meetingsSource = 'meetings';
        }
    } elseif (tableExists($pdo, 'activity_logs')) {
        // Fallback: meetings logged as activity events
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM activity_logs WHERE activity_type LIKE '%meeting%' AND created_at BETWEEN ? AND ?");
        $stmt->execute([$weekStart->format('Y-m-d H:i:s'), $weekEnd->format('Y-m-d H:i:s')]);
        $meetings = (int)$stmt->fetchColumn();
        $meetingsSource = 'activity_logs';
    }

    // ---------- CLOSED DEALS / INVOICES (MTD) ----------
    $closedDeals = 0;
    $previousClosedDeals = 0;
    $closedDealsSource = 'none';

    $closedTable = null;
    if (tableExists($pdo, 'deals')) {
        $closedTable = 'deals';
    } elseif (tableExists($pdo, 'invoices')) {
        $closedTable = 'invoices';
    } elseif (tableExists($pdo, 'projects')) {
        $closedTable = 'projects';
    }

    if ($closedTable) {
        $closedColumns = columns($pdo, $closedTable);
        $closedDate = firstColumn($closedColumns, ['paid_date', 'issue_date', 'created_at', 'createdat', 'date_created']);
        $closedStatus = firstColumn($closedColumns, ['status', 'deal_status', 'project_status', 'payment_status']);

        if ($closedDate && $closedStatus) {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM `$closedTable`
                 WHERE `$closedDate` BETWEEN ? AND ?
                 AND LOWER(`$closedStatus`) IN ('closed','won','completed','paid','settled')"
            );
            $stmt->execute([$monthStart->format('Y-m-d H:i:s'), $todayEnd->format('Y-m-d H:i:s')]);
            $closedDeals = (int)$stmt->fetchColumn();

            $stmt->execute([$previousMonthStart->format('Y-m-d H:i:s'), $previousMonthEnd->format('Y-m-d H:i:s')]);
            $previousClosedDeals = (int)$stmt->fetchColumn();
        } else {
            $closedDeals = (int)$pdo->query("SELECT COUNT(*) FROM `$closedTable`")->fetchColumn();
        }
        $closedDealsSource = $closedTable;
    }

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
    // Graceful fallback for offline demo / uninitialized DB
    echo json_encode([
        "success" => true,
        "is_fallback" => true,
        "kpis" => [
            "total_leads" => kpi("Total Leads", 8, 5),
            "active_deals" => kpi("Active Deals", 3, 2),
            "meetings_this_week" => kpi("Meetings This Week", 2, 1),
            "closed_deals_mtd" => kpi("Closed Deals (Month-to-Date)", 1, 1)
        ],
        "message" => "Rendered with fallback dataset (" . $e->getMessage() . ")"
    ], JSON_PRETTY_PRINT);
}
