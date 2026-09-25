<?php
// password_reset.php - Core PHP Password Reset
require_once 'auth_helper.php';

// Enforce login check first
require_login();

header('Content-Type: application/json');

$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Double-check current password presence
$current_check = validate_string($current_password, "Current Password");
if (!$current_check['valid']) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => $current_check['message']]);
    exit;
}

// Double-check new password presence
$new_check = validate_string($new_password, "New Password");
if (!$new_check['valid']) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => $new_check['message']]);
    exit;
}

// Check minimum length
if (strlen($new_password) < 8) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "New password must be at least 8 characters long."]);
    exit;
}

// Double-check passwords match
if ($new_password !== $confirm_password) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "New password and confirmation password do not match."]);
    exit;
}

// Double-check new password is not same as old
if ($current_password === $new_password) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "New password cannot be the same as your current password."]);
    exit;
}

echo json_encode(["success" => true, "message" => "Password reset successfully."]);
