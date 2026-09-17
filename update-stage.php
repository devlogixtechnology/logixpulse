<?php
// Squad BE ka "stage-update" script
// Ye endpoint frontend se card_id aur new_column_id leta hai aur DB mein save karta hai

header('Content-Type: application/json');
require 'db_connect.php';

// Sirf POST requests allow karein
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Sirf POST allowed hai"]);
    exit;
}

// JSON body ko parse karna
$input = json_decode(file_get_contents('php://input'), true);
$taskId       = $input['task_id'] ?? null;
$newColumnId  = $input['new_column_id'] ?? null;
$newPosition  = $input['new_position'] ?? 0;

if (!$taskId || !$newColumnId) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "task_id aur new_column_id zaroori hain"]);
    exit;
}

try {
    // Real save: task ka column_id (stage) aur position update karna
    $stmt = $pdo->prepare(
        "UPDATE tasks SET column_id = :col, position = :pos WHERE id = :id"
    );
    $stmt->execute([
        ':col' => $newColumnId,
        ':pos' => $newPosition,
        ':id'  => $taskId
    ]);

    // Confirm karein ke row actually mili aur update hui
    if ($stmt->rowCount() === 0) {
        // ho sakta hai value pehle se same thi — check karein task exist karta hai ya nahi
        $check = $pdo->prepare("SELECT id FROM tasks WHERE id = :id");
        $check->execute([':id' => $taskId]);
        if (!$check->fetch()) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Task nahi mila"]);
            exit;
        }
    }

    echo json_encode(["success" => true, "message" => "Card DB mein save ho gaya"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Save fail: " . $e->getMessage()]);
}
