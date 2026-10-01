<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../services/LeadStageService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');

    jsonResponse([
        'success' => false,
        'message' => 'Only POST is allowed for stage transitions.'
    ], 405);
}

$userId = requireUserId();
$body = readJsonBody();

$leadId = filter_var($body['lead_id'] ?? null, FILTER_VALIDATE_INT);
$newStage = trim((string)($body['new_stage'] ?? ''));
$metadata = $body['metadata'] ?? [];

if (!$leadId || $leadId <= 0) {
    jsonResponse([
        'success' => false,
        'message' => 'lead_id is required and must be a positive integer.'
    ], 422);
}

if ($newStage === '') {
    jsonResponse([
        'success' => false,
        'message' => 'new_stage is required.'
    ], 422);
}

if (!is_array($metadata)) {
    jsonResponse([
        'success' => false,
        'message' => 'metadata must be a JSON object.'
    ], 422);
}

try {
    $service = new LeadStageService(db());

    $result = $service->transition(
        (int)$leadId,
        $userId,
        $newStage,
        $metadata
    );

    jsonResponse([
        'success' => true,
        'data' => $result
    ], 200);
} catch (DomainException $e) {
    jsonResponse([
        'success' => false,
        'error' => 'INVALID_STAGE_TRANSITION',
        'message' => $e->getMessage()
    ], 422);
} catch (InvalidArgumentException $e) {
    jsonResponse([
        'success' => false,
        'error' => 'VALIDATION_ERROR',
        'message' => $e->getMessage()
    ], 422);
} catch (RuntimeException $e) {
    jsonResponse([
        'success' => false,
        'error' => 'NOT_FOUND',
        'message' => $e->getMessage()
    ], 404);
} catch (Throwable $e) {
    error_log($e->getMessage());

    jsonResponse([
        'success' => false,
        'error' => 'SERVER_ERROR',
        'message' => 'Unable to process the stage transition.'
    ], 500);
}
