<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    json_error(405, 'METHOD_NOT_ALLOWED', 'Use POST for privilege escalation.');
}

$user = require_internal(['admin']);
verify_csrf_request();

$input = json_decode(file_get_contents('php://input'), true);
$newRole = is_array($input) ? trim((string)($input['new_role'] ?? '')) : '';

$allowedEscalationRoles = ['manager'];

if (!in_array($newRole, $allowedEscalationRoles, true)) {
    json_error(422, 'INVALID_ROLE', 'The requested privilege level is not allowed.');
}

$db = Database::connection();

try {
    $db->beginTransaction();

    $stmt = $db->prepare(
        'UPDATE users SET role = :role, updated_at = NOW()
         WHERE id = :id AND is_active = TRUE'
    );
    $stmt->execute([
        'role' => $newRole,
        'id' => (int)$user['id'],
    ]);

    if ($stmt->rowCount() !== 1) {
        $db->rollBack();
        json_error(409, 'ROLE_UPDATE_FAILED', 'Privilege escalation could not be completed.');
    }

    $audit = $db->prepare(
        'INSERT INTO auth_audit_log
         (user_id, action, old_role, new_role, ip_address, user_agent)
         VALUES (:user_id, :action, :old_role, :new_role, :ip, :agent)'
    );
    $audit->execute([
        'user_id' => (int)$user['id'],
        'action' => 'PRIVILEGE_ESCALATION',
        'old_role' => (string)$user['role'],
        'new_role' => $newRole,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        'agent' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
    ]);

    $db->commit();
    rotate_session_after_privilege_escalation($newRole);

    json_success([
        'message' => 'Privilege escalation completed and session rotated.',
        'role' => $newRole,
        'privilege_version' => $_SESSION['auth']['privilege_version'],
        'csrf_token' => csrf_token(),
    ]);
} catch (Throwable) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    json_error(500, 'PRIVILEGE_ESCALATION_FAILED', 'Privilege escalation failed safely.');
}
