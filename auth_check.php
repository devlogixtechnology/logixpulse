<?php
/**
 * BE-W7D2-2 — Who Is Logged In Check
 * Reusable Core PHP session check.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.html');
    exit;
}
