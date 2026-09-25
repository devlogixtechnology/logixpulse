<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$search = trim((string)($_GET['search'] ?? $_GET['q'] ?? ''));

try {
    $pdo = getDatabaseConnection();

    if ($search === '') {
        $stmt = $pdo->query(
            "SELECT id, 
                    COALESCE(NULLIF(name, ''), CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) AS name,
                    email, phone, company, status, stage, created_at
             FROM leads 
             ORDER BY id ASC"
        );
    } else {
        $stmt = $pdo->prepare(
            "SELECT id, 
                    COALESCE(NULLIF(name, ''), CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) AS name,
                    email, phone, company, status, stage, created_at
             FROM leads 
             WHERE name LIKE :q1 
                OR first_name LIKE :q2 
                OR last_name LIKE :q3 
                OR email LIKE :q4 
                OR phone LIKE :q5 
                OR company LIKE :q6
             ORDER BY id ASC"
        );
        $term = "%{$search}%";
        $stmt->execute([
            ':q1' => $term,
            ':q2' => $term,
            ':q3' => $term,
            ':q4' => $term,
            ':q5' => $term,
            ':q6' => $term,
        ]);
    }

    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'query'   => $search,
        'count'   => count($leads),
        'leads'   => $leads
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lead search failed: ' . $e->getMessage()
    ]);
}
