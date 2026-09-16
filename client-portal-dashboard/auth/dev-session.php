<?php
/**
 * auth/dev-session.php
 * Task: FEB-W7D3-1 - Connect the Timeline to Real Data
 *
 * TEMP dev-only helper that simulates a logged-in client by setting
 * $_SESSION['client_id']. This is NOT the real auth flow (that's a
 * separate task) — it exists only so this feature, and anything else
 * that depends on "the logged-in client", can be tested/demoed with
 * different accounts before real login is wired up.
 *
 * Usage: auth/dev-session.php?client_id=1  or  ?client_id=2
 *
 * Safety: refuses to run when APP_ENV=production, so this can never
 * be used to hijack a session in a live environment. Delete this file
 * once the real login task sets $_SESSION['client_id'] on its own.
 */

session_start();

if (getenv('APP_ENV') === 'production') {
    http_response_code(403);
    echo 'Dev session switcher is disabled in production.';
    exit;
}

$clientId = isset($_GET['client_id']) ? (int) $_GET['client_id'] : null;

if ($clientId) {
    $_SESSION['client_id'] = $clientId;
}

header('Location: ../index.php');
exit;
