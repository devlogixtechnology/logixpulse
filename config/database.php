<?php
/**
 * LogixPulse — Database Configuration & Connection (MySQL/XAMPP Version)
 */

function getDatabaseConnection() {
    // ---- Database Credentials for XAMPP ----
    $host     = 'localhost';
    $port     = '3306'; // MySQL default port
    $dbname   = 'logix_pulse';
    $username = 'root'; // XAMPP default user
    $password = '';     // XAMPP default password is empty

    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return $pdo;

    } catch (PDOException $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        throw new PDOException('Unable to connect to database. Please try again later.');
    }
}