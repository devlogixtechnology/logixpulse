<?php
declare(strict_types=1);

/**
 * BE-W7D2-2 — Who Is Logged In Check
 * Reusable Core PHP session check.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth.php';

if (!isLoggedIn()) {
    header('Location: ../index.html');
    exit;
}
