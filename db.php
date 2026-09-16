<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "crm_leads";

$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    http_response_code(500);
    die("Database connection failed.");
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$database`");
$conn->select_db($database);

$conn->query("
    CREATE TABLE IF NOT EXISTS leads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        source VARCHAR(255) NOT NULL,
        status VARCHAR(50) NOT NULL DEFAULT 'New'
    )
");

$conn->set_charset("utf8mb4");
?>
