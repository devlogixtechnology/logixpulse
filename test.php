<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=logixpulse_kanban;charset=utf8mb4", "root", "");
    echo "Connection successful!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}