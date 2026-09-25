<?php
// lead_search_protected.php - Protected Search Endpoint
require_once 'auth_helper.php';

// Enforce login check first
require_login();

header('Content-Type: application/json');

$search = $_GET['search'] ?? '';

if (!is_string($search)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid search parameter."]);
    exit;
}

$leads = [
    ["id" => 1, "name" => "Ahmed Khan", "email" => "ahmed@example.com", "phone" => "03001234567"],
    ["id" => 2, "name" => "Ali Raza", "email" => "ali@example.com", "phone" => "03219876543"]
];

$searchLower = strtolower(trim($search));

if ($searchLower === '') {
    echo json_encode(["success" => true, "data" => $leads]);
    exit;
}

$results = array_values(array_filter($leads, function($lead) use ($searchLower) {
    return (strpos(strtolower($lead['name']), $searchLower) !== false) ||
           (strpos(strtolower($lead['email']), $searchLower) !== false) ||
           (strpos($lead['phone'], $searchLower) !== false);
}));

echo json_encode(["success" => true, "data" => $results]);
