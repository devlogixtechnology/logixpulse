<?php
declare(strict_types=1);

/**
 * db.php
 * Core PHP database connection using PDO and MySQLi.
 */

$host = getenv('DB_HOST') ?: '127.0.0.1';
$db   = getenv('DB_NAME') ?: 'logixpulse';
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
    $pdo = null;
}

// MySQLi connection for scripts requiring $conn
$conn = @new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    $conn = @new mysqli($host, $user, $pass);
    if ($conn && !$conn->connect_error) {
        $conn->query("CREATE DATABASE IF NOT EXISTS `$db`");
        $conn->select_db($db);
    }
}
if ($conn && !$conn->connect_error) {
    $conn->set_charset("utf8mb4");
}

