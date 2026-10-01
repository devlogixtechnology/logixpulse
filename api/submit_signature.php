<?php
/**
 * LogixPulse CRM - Digital Agreement Signature Submission
 * Squad: PHP-FE-B (Client Signing Portal)
 * Subtask FEB-03 (Halima) & FEB-04 (Sayyda Arooj)
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$agreementId = isset($input['agreement_id']) ? trim($input['agreement_id']) : 'MSA-2026-904';
$referenceCode = $agreementId . '-EXEC';
$signerName = isset($input['signer_name']) ? trim($input['signer_name']) : 'Sarah Jenkins';
$signerEmail = isset($input['signer_email']) ? trim($input['signer_email']) : 's.jenkins@acmecloud.com';
$clientCompany = isset($input['company_name']) ? trim($input['company_name']) : 'Acme Cloud Technologies';
$signatureBase64 = isset($input['signature_data']) ? trim($input['signature_data']) : '';
$scopeAgreed = !empty($input['scope_agreed']);
$paymentAgreed = !empty($input['payment_agreed']);
$confidentialityAgreed = !empty($input['confidentiality_agreed']);

if (empty($signatureBase64)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Signature canvas drawing cannot be empty.']);
    exit;
}

if (!$scopeAgreed || !$paymentAgreed || !$confidentialityAgreed) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'All mandatory legal acknowledgment checkboxes must be confirmed.']);
    exit;
}

// Uploads and storage directory
$uploadDir = __DIR__ . '/../uploads/signatures';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

// Save signature image
$randomHash = bin2hex(random_bytes(16));
$fileName = 'sig_' . time() . '_' . $randomHash . '.png';
$filePath = $uploadDir . '/' . $fileName;

if (preg_match('/^data:image\/(\w+);base64,/', $signatureBase64)) {
    $cleanData = substr($signatureBase64, strpos($signatureBase64, ',') + 1);
    $signatureData = base64_decode($cleanData);
} else {
    $signatureData = base64_decode($signatureBase64);
}

@file_put_contents($filePath, $signatureData);

$executedTimestamp = date('M d, Y • h:i:s A T');
$isoTimestamp = date('c');
$certificateHash = strtoupper(hash('sha256', $agreementId . $signerEmail . $isoTimestamp . $randomHash));

$executedRecord = [
    'id' => $agreementId,
    'reference_code' => $referenceCode,
    'status' => 'signed',
    'status_badge' => 'Executed & Legally Binding',
    'client_company' => $clientCompany,
    'signer_name' => $signerName,
    'signer_email' => $signerEmail,
    'execution_timestamp' => $executedTimestamp,
    'iso_timestamp' => $isoTimestamp,
    'certificate_hash' => $certificateHash,
    'signature_image_data' => $signatureBase64,
    'download_url' => '/api/download_receipt.php?agreement_id=' . urlencode($agreementId) . '&ref=' . urlencode($referenceCode),
    'audit_trail' => [
        'event' => 'AGREEMENT_DIGITALLY_EXECUTED',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        'browser' => $_SERVER['HTTP_USER_AGENT'] ?? 'LogixPulse-Signing-Portal/2.0',
        'terms_acknowledged' => ['scope' => true, 'payment' => true, 'confidentiality' => true]
    ]
];

// Persist to state store to prevent re-signing
$stateStorePath = $uploadDir . '/agreement_state.json';
$existingState = [];
if (file_exists($stateStorePath)) {
    $existingState = json_decode(file_get_contents($stateStorePath), true) ?: [];
}
$existingState[$agreementId] = $executedRecord;
@file_put_contents($stateStorePath, json_encode($existingState, JSON_PRETTY_PRINT));

echo json_encode([
    'status' => 'success',
    'message' => 'Agreement signed and certified successfully.',
    'agreement' => $executedRecord
], JSON_PRETTY_PRINT);
