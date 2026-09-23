<?php
header("Content-Type: application/json");
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success"=>false,"message"=>"Only POST method is allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$lead_id = isset($data["lead_id"]) ? (int)$data["lead_id"] : 0;
$new_stage = isset($data["new_stage"]) ? trim($data["new_stage"]) : "";

if ($lead_id <= 0 || $new_stage === "") {
    http_response_code(400);
    echo json_encode(["success"=>false,"message"=>"lead_id and new_stage are required"]);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT id, stage FROM leads WHERE id = ? FOR UPDATE");
    $stmt->execute([$lead_id]);
    $lead = $stmt->fetch();

    if (!$lead) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(["success"=>false,"message"=>"Lead not found"]);
        exit;
    }

    $old_stage = $lead["stage"];

    if ($old_stage === $new_stage) {
        $pdo->commit();
        echo json_encode([
            "success"=>true,
            "message"=>"Lead is already in this stage",
            "activity_logged"=>false
        ]);
        exit;
    }

    $update = $pdo->prepare("UPDATE leads SET stage = ? WHERE id = ?");
    $update->execute([$new_stage, $lead_id]);

    $note = "Moved from " . $old_stage . " to " . $new_stage . ".";

    $log = $pdo->prepare(
        "INSERT INTO activity_logs (lead_id, note) VALUES (?, ?)"
    );
    $log->execute([$lead_id, $note]);

    $pdo->commit();

    echo json_encode([
        "success"=>true,
        "message"=>"Lead stage updated successfully",
        "lead_id"=>$lead_id,
        "old_stage"=>$old_stage,
        "new_stage"=>$new_stage,
        "activity"=>$note,
        "activity_logged"=>true
    ]);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        "success"=>false,
        "message"=>"Unable to update lead stage"
    ]);
}
?>