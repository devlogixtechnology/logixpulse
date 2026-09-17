<?php
// Apni database details yahan daalein (XAMPP/local server ke hisab se)
 $host = "127.0.0.1";
$dbname = "logixpulse_kanban";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "DB connection fail: " . $e->getMessage()]);
    exit;
}
