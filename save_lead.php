<?php

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Only POST requests are allowed.'
    ]);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$source = trim($_POST['source'] ?? '');

if ($name === '' || $email === '' || $phone === '' || $source === '') {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Name, email, phone and source are required.'
    ]);
    exit;
}

$status = 'New';

$stmt = $conn->prepare(
    "INSERT INTO leads (name, email, phone, source, status)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "sssss",
    $name,
    $email,
    $phone,
    $source,
    $status
);

header('Content-Type: application/json');

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lead could not be saved.'
    ]);
    $stmt->close();
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Lead saved successfully.'
]);

$stmt->close();
$conn->close();
?>
