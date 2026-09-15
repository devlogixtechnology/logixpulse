
<?php
/**
 * BE-W7D1-2 — Central Database Connection
 *
 * All PHP files should include this file instead of duplicating
 * database connection code.
 *
 * IMPORTANT:
 * Replace the five values below with the VPS database credentials
 * provided by the backend/deployment team. Do not commit real
 * credentials to a public repository.
 */

declare(strict_types=1);

$DB_HOST = 'YOUR_VPS_DB_HOST';
$DB_PORT = 3306;
$DB_NAME = 'YOUR_DATABASE_NAME';
$DB_USER = 'YOUR_DATABASE_USER';
$DB_PASS = 'YOUR_DATABASE_PASSWORD';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(
        $DB_HOST,
        $DB_USER,
        $DB_PASS,
        $DB_NAME,
        (int) $DB_PORT
    );

    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('Database connection failed: ' . $e->getMessage());

    http_response_code(500);
    exit('Database connection failed.');
}
?>
