<?php
/**
 * LogixPulse CRM - Agreement Status Verification Endpoint
 * Squad: PHP-FE-B (Client Signing Portal)
 * Subtask: FEB-04 Implement Signed Agreement Confirmation & Receipt Download
 * Developer: Sayyda Arooj
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$token = isset($_GET['token']) ? trim($_GET['token']) : 'lp_token_demo';
$agreementId = isset($_GET['agreement_id']) ? trim($_GET['agreement_id']) : 'MSA-2026-904';

// Check if signature record exists in state file or session
$signatureStore = __DIR__ . '/../uploads/signatures/agreement_state.json';
$signedRecord = null;

if (file_exists($signatureStore)) {
    $data = json_decode(file_get_contents($signatureStore), true);
    if (isset($data[$agreementId])) {
        $signedRecord = $data[$agreementId];
    }
}

if ($signedRecord && $signedRecord['status'] === 'signed') {
    echo json_encode([
        'status' => 'success',
        'is_already_executed' => true,
        'message' => 'This agreement has already been legally executed. Re-signing is prevented.',
        'agreement' => $signedRecord
    ], JSON_PRETTY_PRINT);
    exit;
}

// Default pending state
echo json_encode([
    'status' => 'success',
    'is_already_executed' => false,
    'message' => 'Agreement is active and pending authorized client signature.',
    'agreement' => [
        'id' => $agreementId,
        'reference_code' => $agreementId . '-EXEC',
        'status' => 'pending_signature',
        'title' => 'Master Services Agreement & Statement of Work',
        'token' => $token,
        'effective_date' => 'October 01, 2026',
        'client_name' => 'Acme Cloud Technologies',
        'signer_name' => 'Sarah Jenkins',
        'signer_email' => 's.jenkins@acmecloud.com',
        'deal_value' => '$45,000 ARR'
    ]
], JSON_PRETTY_PRINT);
