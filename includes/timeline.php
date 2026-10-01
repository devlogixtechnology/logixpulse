<?php
/**
 * LogixPulse CRM - Timeline Helper Service
 * Subsystem: FR-CLT-01 Interactive Onboarding Milestone Timeline
 * Developer: Sayeda Arooj (Squad PHP-FE-B)
 */

if (!function_exists('getClientTimeline')) {
    function getClientTimeline($clientId = 1) {
        $mockData = [
            1 => [
                'current_phase' => 3,
                'steps' => [
                    ['id' => 1, 'name' => 'Discovery', 'title' => 'Client Discovery & Scope Definition', 'status' => 'completed', 'date' => '2026-08-15'],
                    ['id' => 2, 'name' => 'Proposal', 'title' => 'Technical Architecture & SOW Proposal', 'status' => 'completed', 'date' => '2026-09-02'],
                    ['id' => 3, 'name' => 'Review', 'title' => 'Contract Execution & Master Services Agreement', 'status' => 'current', 'date' => '2026-10-05'],
                    ['id' => 4, 'name' => 'Execution', 'title' => 'Sprint 1 Onboarding & Environment Provisioning', 'status' => 'upcoming', 'date' => '2026-10-18'],
                    ['id' => 5, 'name' => 'Handover', 'title' => 'Final UAT Acceptance & Production Handover', 'status' => 'upcoming', 'date' => '2026-11-15']
                ]
            ],
            2 => [
                'current_phase' => 1,
                'steps' => [
                    ['id' => 1, 'name' => 'Discovery', 'title' => 'Stakeholder Telematics Discovery', 'status' => 'current', 'date' => '2026-10-12'],
                    ['id' => 2, 'name' => 'Proposal', 'title' => 'System Integration Architecture Design', 'status' => 'upcoming', 'date' => '2026-10-25'],
                    ['id' => 3, 'name' => 'Review', 'title' => 'Commercial Contract & SLA Agreement', 'status' => 'upcoming', 'date' => '2026-11-08'],
                    ['id' => 4, 'name' => 'Execution', 'title' => 'Telemetry Ingestion Pipeline Deployment', 'status' => 'upcoming', 'date' => '2026-11-28'],
                    ['id' => 5, 'name' => 'Handover', 'title' => 'Fleetwide Go-Live & Operational Handoff', 'status' => 'upcoming', 'date' => '2026-12-15']
                ]
            ]
        ];

        return isset($mockData[$clientId]) ? $mockData[$clientId] : $mockData[1];
    }
}
