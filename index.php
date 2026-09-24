<?php
declare(strict_types=1);

/**
 * LogixPulse — Application Entry Point & Authentication Router
 *
 * Resolves root URL (http://localhost/) based on active user session:
 * - Authenticated Client: redirects to client-portal-dashboard/index.php
 * - Authenticated Staff/Admin: redirects to main_dashboard.php
 * - Unauthenticated: redirects to index.html (Sign In page)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['logged_in']) 
    || !empty($_SESSION['auth']['logged_in']) 
    || !empty($_SESSION['user']['logged_in']);

$role = $_SESSION['user_role'] 
    ?? $_SESSION['auth']['role'] 
    ?? $_SESSION['user']['role'] 
    ?? '';

if ($isLoggedIn) {
    if ($role === 'client') {
        header('Location: client-portal-dashboard/index.php');
        exit;
    }

    // Team, staff, executive, or admin roles
    if (file_exists(__DIR__ . '/main_dashboard.php')) {
        header('Location: main_dashboard.php');
        exit;
    }

    header('Location: client-portal-dashboard/index.php');
    exit;
}

// Unauthenticated user -> redirect to the login screen
header('Location: index.html');
exit;
