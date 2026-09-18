<?php
// Simple browser test. Change lead_id and new_stage if needed.
$url = "http://localhost/BE-W7D4-1-Save-Lead-Stage-Changes/api/update_lead_stage.php";

$payload = [
    "lead_id" => 1,
    "new_stage" => "Meeting Booked"
];

$options = [
    "http" => [
        "method" => "POST",
        "header" => "Content-Type: application/json\r\n",
        "content" => json_encode($payload)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

header("Content-Type: application/json");
echo $response;
?>