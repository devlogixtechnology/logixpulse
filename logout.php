<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    json_error(405, 'METHOD_NOT_ALLOWED', 'Use POST for logout.');
}

require_auth();
verify_csrf_request();
logout_session();

json_success(['message' => 'Logout successful.']);
