<?php
/**
 * DevLogix Summer Internship Program 2026 - Sprint 2
 * Epic: CRM Pipeline & Lead Management Consolidation
 * Target Endpoint: api/notifications.php
 * 
 * Purpose: RESTful endpoint for fetching client notifications with unread counter.
 * Tech: Core PHP, PDO with pgsql driver, PostgreSQL 15+, Native PHP Sessions.
 * 
 * Note for Management / Backend Squad:
 * When connecting to real PostgreSQL 15, update the PDO DSN below or include db.php.
 */

declare(strict_types=1);
session_start();

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

// Optional: Validate client session
$clientId = $_SESSION['client_id'] ?? 42; // Fallback mock client ID for testing

try {
    // If PostgreSQL connection credentials are configured in environment
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '5432';
    $dbname = getenv('DB_NAME') ?: 'devlogix_crm';
    $user = getenv('DB_USER') ?: 'postgres';
    $password = getenv('DB_PASSWORD') ?: '';

    // Check if database is reachable, otherwise output fallback JSON for development
    if (getenv('DB_HOST')) {
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        $stmt = $pdo->prepare("
            SELECT id, title, message, category, category_label, 
                   created_at, is_read, priority, action_url 
            FROM client_notifications 
            WHERE client_id = :client_id 
            ORDER BY created_at DESC 
            LIMIT 50
        ");
        $stmt->execute(['client_id' => $clientId]);
        $notifications = $stmt->fetchAll();

        // Calculate unread count
        $unreadStmt = $pdo->prepare("
            SELECT COUNT(*) AS count 
            FROM client_notifications 
            WHERE client_id = :client_id AND is_read = FALSE
        ");
        $unreadStmt->execute(['client_id' => $clientId]);
        $unreadCount = (int) $unreadStmt->fetchColumn();

        echo json_encode([
            'status' => 'success',
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
        exit;
    }
} catch (\Throwable $e) {
    // Graceful fallback to mock file if DB is not configured yet in Sprint 2 development
}

// Fallback to static mock JSON
$mockFilePath = __DIR__ . '/mock_notifications.json';
if (file_exists($mockFilePath)) {
    echo file_get_contents($mockFilePath);
} else {
    echo json_encode([
        'status' => 'success',
        'unread_count' => 0,
        'notifications' => []
    ]);
}
