<?php
/**
 * db.php
 * Core PHP database connection using PDO.
 * Update the constants below to match your MySQL setup.
 */

$DB_HOST = 'localhost';
$DB_NAME = 'your_database_name';
$DB_USER = 'your_db_username';
$DB_PASS = 'your_db_password';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // In production, log this instead of exposing details.
    die('Database connection failed: ' . $e->getMessage());
}
