<?php
// magic_code.php - Core PHP Magic Code Generator & Verifier
require_once 'auth_helper.php';

// Enforce login check first
require_login();

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'generate') {
    $email_input = $_POST['email'] ?? '';
    
    // Double-check email format
    $check = validate_email($email_input);
    if (!$check['valid']) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => $check['message']]);
        exit;
    }

    $email = $check['value'];
    $magic_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Save to session (or DB in production)
    $_SESSION['magic_codes'][$email] = [
        'code' => $magic_code,
        'expires' => time() + 600 // 10 mins
    ];

    echo json_encode([
        "success" => true,
        "message" => "Magic code generated successfully for {$email}."
    ]);
    exit;
}

if ($action === 'verify') {
    $email_input = $_POST['email'] ?? '';
    $code_input = $_POST['code'] ?? '';

    // Double-check inputs
    $email_check = validate_email($email_input);
    if (!$email_check['valid']) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => $email_check['message']]);
        exit;
    }

    $code_check = validate_string($code_input, "Magic Code");
    if (!$code_check['valid']) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => $code_check['message']]);
        exit;
    }

    $email = $email_check['value'];
    $code = $code_check['value'];

    $record = $_SESSION['magic_codes'][$email] ?? null;

    if (!$record) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "No magic code found for this email."]);
        exit;
    }

    if (time() > $record['expires']) {
        unset($_SESSION['magic_codes'][$email]);
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Magic code has expired."]);
        exit;
    }

    if ($record['code'] !== $code) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Invalid magic code provided."]);
        exit;
    }

    unset($_SESSION['magic_codes'][$email]);
    echo json_encode(["success" => true, "message" => "Magic code verified successfully."]);
    exit;
}

http_response_code(400);
echo json_encode(["success" => false, "error" => "Invalid action requested."]);
