<?php
declare(strict_types=1);

/**
 * LogixPulse — Lead Repository
 * Handles querying and grouping of CRM leads by status / stage.
 */
class LeadRepository
{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        if ($pdo instanceof PDO) {
            $this->pdo = $pdo;
        } elseif (function_exists('getDatabaseConnection')) {
            try {
                $this->pdo = getDatabaseConnection();
            } catch (Throwable $e) {
                $this->pdo = null;
            }
        } else {
            $this->pdo = null;
        }
    }

    /**
     * Get all leads grouped by status/stage.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function getGroupedLeads(): array
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->query("SELECT * FROM leads ORDER BY id ASC");
                $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($leads)) {
                    $grouped = [];
                    foreach ($leads as $lead) {
                        $rawStatus = $lead['status'] ?? $lead['stage'] ?? 'new';
                        $statusKey = strtolower(trim((string)$rawStatus));
                        if (!isset($grouped[$statusKey])) {
                            $grouped[$statusKey] = [];
                        }
                        // Ensure name field is present
                        if (empty($lead['name']) && (!empty($lead['first_name']) || !empty($lead['last_name']))) {
                            $lead['name'] = trim(($lead['first_name'] ?? '') . ' ' . ($lead['last_name'] ?? ''));
                        }
                        $grouped[$statusKey][] = $lead;
                    }
                    return $grouped;
                }
            } catch (Throwable $e) {
                error_log('[LeadRepository] DB query failed, falling back to mock: ' . $e->getMessage());
            }
        }

        return $this->getMockGroupedLeads();
    }

    /**
     * Fallback mock leads dataset for offline / development testing.
     */
    private function getMockGroupedLeads(): array
    {
        return [
            'new' => [
                ['id' => 1, 'name' => 'Emma Clark', 'email' => 'emma.clark@innohub.com', 'phone' => '+1-555-0101', 'company' => 'InnoHub', 'status' => 'new', 'stage' => 'New'],
                ['id' => 4, 'name' => 'Henry Carter', 'email' => 'henry.carter@vortex.com', 'phone' => '+1-555-0104', 'company' => 'Vortex Labs', 'status' => 'new', 'stage' => 'New'],
                ['id' => 7, 'name' => 'Karen Flynn', 'email' => 'karen.flynn@zenithco.com', 'phone' => '+1-555-0107', 'company' => 'Zenith Co', 'status' => 'new', 'stage' => 'New'],
            ],
            'contacted' => [
                ['id' => 2, 'name' => 'Frank Adams', 'email' => 'frank.adams@solartech.com', 'phone' => '+1-555-0102', 'company' => 'SolarTech', 'status' => 'contacted', 'stage' => 'Contacted'],
                ['id' => 5, 'name' => 'Ivy Dixon', 'email' => 'ivy.dixon@pulsedata.com', 'phone' => '+1-555-0105', 'company' => 'PulseData', 'status' => 'contacted', 'stage' => 'Contacted'],
                ['id' => 8, 'name' => 'Leo Grant', 'email' => 'leo.grant@orbitx.com', 'phone' => '+1-555-0108', 'company' => 'OrbitX', 'status' => 'contacted', 'stage' => 'Contacted'],
            ],
            'qualified' => [
                ['id' => 3, 'name' => 'Grace Baker', 'email' => 'grace.baker@nexgen.com', 'phone' => '+1-555-0103', 'company' => 'NexGen', 'status' => 'qualified', 'stage' => 'Qualified'],
                ['id' => 6, 'name' => 'Jack Ellis', 'email' => 'jack.ellis@arclight.com', 'phone' => '+1-555-0106', 'company' => 'ArcLight', 'status' => 'qualified', 'stage' => 'Qualified'],
            ]
        ];
    }
}
