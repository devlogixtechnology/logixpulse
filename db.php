<?php
declare(strict_types=1);

/**
 * db.php
 * Core PHP database connection using PDO.
 */

$host = getenv('DB_HOST') ?: '127.0.0.1';
$db   = getenv('DB_NAME') ?: 'logixpulse_auth';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed: ' . $e->getMessage());
}

