<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/LeadRepository.php';

try {
    $repository = new LeadRepository($pdo);
    $leads = $repository->getGroupedLeads();

    echo json_encode([
        'success' => true,
        'data' => $leads
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to load lead data.'
    ], JSON_PRETTY_PRINT);
}
