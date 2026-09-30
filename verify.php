<?php
declare(strict_types=1);

/*
 * This script is intentionally a readable verification checklist.
 * Run the application first, then test these cases with browser/cURL/Postman.
 */

$base = 'http://localhost/rec-be-02-session-auth-role-enforcement';

$checks = [
    '1. POST /api/login.php with staff credentials -> 200 + session cookie + CSRF token',
    '2. GET /api/internal_dashboard.php as staff -> 200',
    '3. GET /api/client_portal.php as staff -> 403 (cross-boundary protection)',
    '4. POST /api/logout.php with valid CSRF token -> 200',
    '5. GET /api/internal_dashboard.php after logout -> 401',
    '6. Login as client -> 200',
    '7. GET /api/client_portal.php as client -> 200',
    '8. GET /api/internal_dashboard.php as client -> 403',
    '9. POST state-changing endpoint without CSRF token -> 403',
    '10. Privilege escalation as admin -> session ID rotated and privilege_version incremented',
];

echo "REC-BE-02 Verification Checklist\n\n";
foreach ($checks as $check) {
    echo "[ ] {$check}\n";
}
echo "\nBase URL: {$base}\n";
