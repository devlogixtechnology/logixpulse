<?php
// auth_helper.php - Core PHP Auth Guard & Validation Helper

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if the user is logged in before allowing access.
 */
function require_login() {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "Unauthorized: You must be logged in to perform this action."
        ]);
        exit;
    }
}

/**
 * Double-check string input: Rejects empty or non-string inputs.
 */
function validate_string($input, $field_name) {
    if (!isset($input) || !is_string($input) || trim($input) === '') {
        return ["valid" => false, "message" => "{$field_name} is required and cannot be empty."];
    }
    return ["valid" => true, "value" => trim($input)];
}

/**
 * Double-check email input: Rejects invalid email formats.
 */
function validate_email($email) {
    if (!isset($email) || !filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        return ["valid" => false, "message" => "Invalid email address provided."];
    }
    return ["valid" => true, "value" => strtolower(trim($email))];
}
?>
