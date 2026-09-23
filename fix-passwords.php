<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDatabaseConnection();
    
    // 'Password@123' ka sahi aur secure hash banayein
    $newHash = password_hash('Password@123', PASSWORD_DEFAULT);
    
    // Sare users ka password update kar dein
    $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash");
    $stmt->execute([':hash' => $newHash]);
    
    echo "<h1>✅ Success!</h1>";
    echo "<p>All user passwords have been updated to: <b>Password@123</b></p>";
    echo "<p>Ab aap wapas <a href='index.html'>Login Page</a> par ja kar login kar sakte hain.</p>";

} catch (PDOException $e) {
    die("Error updating passwords: " . $e->getMessage());
}
?>