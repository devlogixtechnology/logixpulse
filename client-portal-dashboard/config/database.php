<?php
/**
 * config/database.php
 * Task: FEB-W7D3-1 - Connect the Timeline to Real Data
 *
 * Central PDO connection helper for the Client Portal.
 * Reads credentials from environment variables (see .env.example).
 *
 * Returns null instead of throwing when a connection can't be made,
 * so calling code (includes/timeline.php) can fall back to the mock
 * dataset - useful for local/demo work where MySQL isn't set up yet.
 * Set DB_MOCK_MODE=true in .env to force mock data even if a DB exists.
 */

function getDbConnection(): ?PDO
{
    static $pdo = null;
    static $attempted = false;

    if ($attempted) {
        return $pdo;
    }
    $attempted = true;

    if (getenv('DB_MOCK_MODE') === 'true') {
        return null;
    }

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $name = getenv('DB_NAME') ?: 'logixpulse';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';

    try {
        $pdo = new PDO(
            "mysql:host={$host};dbname={$name};charset=utf8mb4",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        error_log('[LogixPulse] DB connection failed, falling back to mock data: ' . $e->getMessage());
        $pdo = null;
    }

    return $pdo;
}
