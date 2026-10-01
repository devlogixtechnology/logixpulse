<?php
/**
 * LogixPulse CRM - Invoices Helper Service
 * Subsystem: FR-INV-01, FR-INV-02, FR-INV-03
 * Developer: Sayeda Arooj (Squad PHP-FE-B)
 */

if (!function_exists('getClientInvoices')) {
    function getClientInvoices($clientId = 1) {
        $invoices = [
            1 => [
                ['id' => 'INV-2026-001', 'title' => 'Cloud Architecture Discovery & Blueprint', 'amount' => 12500.00, 'status' => 'paid', 'date' => '2026-08-20'],
                ['id' => 'INV-2026-002', 'title' => 'Sprint 1 Infrastructure Provisioning Milestone', 'amount' => 15000.00, 'status' => 'pending', 'date' => '2026-09-25'],
                ['id' => 'INV-2026-003', 'title' => 'High-Availability Container Cluster License', 'amount' => 4500.00, 'status' => 'paid', 'date' => '2026-09-01'],
                ['id' => 'INV-2026-004', 'title' => 'Security Hardening & Penetration Testing Retainer', 'amount' => 6200.00, 'status' => 'paid', 'date' => '2026-08-01']
            ],
            2 => [
                ['id' => 'INV-2026-005', 'title' => 'Telematics Sensor Feasibility Audit', 'amount' => 18500.00, 'status' => 'overdue', 'date' => '2026-09-15'],
                ['id' => 'INV-2026-006', 'title' => 'Edge Gateway Protocol Architecture Specification', 'amount' => 24000.00, 'status' => 'pending', 'date' => '2026-10-01']
            ]
        ];

        return isset($invoices[$clientId]) ? $invoices[$clientId] : [];
    }
}
