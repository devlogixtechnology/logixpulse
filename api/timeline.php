<?php
/**
 * LogixPulse CRM - Client Timeline API
 * Squad: PHP-FE-B (Client Communication & Onboarding)
 * Subtask: Client Onboarding Milestone Progression
 * Developer: Sayeda Arooj
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$clientId = isset($_GET['client_id']) ? intval($_GET['client_id']) : 1;

// Multi-tenant client timeline data
$clientsTimelineData = [
    1 => [
        'client_id' => 1,
        'company_name' => 'Acme Cloud Technologies',
        'project_name' => 'Cloud Infrastructure Architecture Consolidation',
        'deal_value' => '$45,000 ARR',
        'current_step_id' => 3,
        'completion_percentage' => 75,
        'lead_architect' => [
            'name' => 'Alex Lawson',
            'role' => 'Senior Lead Architect',
            'email' => 'alex.lawson@devlogix.io',
            'phone' => '+1 (555) 392-1084'
        ],
        'steps' => [
            [
                'step_id' => 1,
                'title' => 'Client Discovery & Scope Definition',
                'phase_name' => 'Discovery',
                'description' => 'Initial architectural review and stakeholder requirements intake.',
                'target_date' => '2026-08-15',
                'status' => 'completed',
                'badge' => 'Completed'
            ],
            [
                'step_id' => 2,
                'title' => 'Technical Architecture & SOW Proposal',
                'phase_name' => 'Proposal',
                'description' => 'Infrastructure topology, security blueprints, and budgetary allocation.',
                'target_date' => '2026-09-02',
                'status' => 'completed',
                'badge' => 'Completed'
            ],
            [
                'step_id' => 3,
                'title' => 'Contract Execution & Master Services Agreement',
                'phase_name' => 'Review',
                'description' => 'Mutual legal review and authorized digital e-signature on MSA-2026-904.',
                'target_date' => '2026-10-05',
                'status' => 'current',
                'badge' => 'Active Stage'
            ],
            [
                'step_id' => 4,
                'title' => 'Sprint 1 Onboarding & Environment Provisioning',
                'phase_name' => 'Execution',
                'description' => 'CI/CD pipeline rollout and staging cloud container orchestration.',
                'target_date' => '2026-10-18',
                'status' => 'upcoming',
                'badge' => 'Pending Sign'
            ],
            [
                'step_id' => 5,
                'title' => 'Final UAT Acceptance & Production Handover',
                'phase_name' => 'Handover',
                'description' => 'Executive signoff, invoice settlement, and production SLA activation.',
                'target_date' => '2026-11-15',
                'status' => 'upcoming',
                'badge' => 'Upcoming'
            ]
        ]
    ],
    2 => [
        'client_id' => 2,
        'company_name' => 'Nexus Global Logistics',
        'project_name' => 'Enterprise Fleet Telemetry & Dispatch Portal',
        'deal_value' => '$78,500 ARR',
        'current_step_id' => 1,
        'completion_percentage' => 25,
        'lead_architect' => [
            'name' => 'Marcus Vance',
            'role' => 'Enterprise Solutions Director',
            'email' => 'm.vance@devlogix.io',
            'phone' => '+1 (555) 782-9011'
        ],
        'steps' => [
            [
                'step_id' => 1,
                'title' => 'Stakeholder Telematics Discovery',
                'phase_name' => 'Discovery',
                'description' => 'Field sensor audit, API ingest analysis, and vehicle fleet profiling.',
                'target_date' => '2026-10-12',
                'status' => 'current',
                'badge' => 'Active Stage'
            ],
            [
                'step_id' => 2,
                'title' => 'System Integration Architecture Design',
                'phase_name' => 'Proposal',
                'description' => 'Real-time telemetry event streaming and cloud messaging spec.',
                'target_date' => '2026-10-25',
                'status' => 'upcoming',
                'badge' => 'Upcoming'
            ],
            [
                'step_id' => 3,
                'title' => 'Commercial Contract & SLA Agreement',
                'phase_name' => 'Review',
                'description' => 'Legal framework approval and multi-year support agreement.',
                'target_date' => '2026-11-08',
                'status' => 'upcoming',
                'badge' => 'Upcoming'
            ],
            [
                'step_id' => 4,
                'title' => 'Telemetry Ingestion Pipeline Deployment',
                'phase_name' => 'Execution',
                'description' => 'Edge hardware onboarding and automated gateway provisioning.',
                'target_date' => '2026-11-28',
                'status' => 'upcoming',
                'badge' => 'Upcoming'
            ],
            [
                'step_id' => 5,
                'title' => 'Fleetwide Go-Live & Operational Handoff',
                'phase_name' => 'Handover',
                'description' => 'Dispatch room training and high-availability cutover.',
                'target_date' => '2026-12-15',
                'status' => 'upcoming',
                'badge' => 'Upcoming'
            ]
        ]
    ]
];

$clientData = isset($clientsTimelineData[$clientId]) ? $clientsTimelineData[$clientId] : $clientsTimelineData[1];

echo json_encode([
    'status' => 'success',
    'timestamp' => date('c'),
    'data' => $clientData
], JSON_PRETTY_PRINT);
