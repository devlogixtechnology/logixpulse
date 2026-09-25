<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDatabaseConnection();

    $statement = $pdo->query('SELECT * FROM users LIMIT 1');
    $user = $statement->fetch();

    if ($user === false) {
        echo "Database connection successful, but the users table has no rows." . PHP_EOL;
        exit(0);
    }

    echo "Database connection successful." . PHP_EOL;
    echo "A row was successfully read from the users table:" . PHP_EOL;
    print_r($user);
} catch (Throwable $e) {
    http_response_code(500);
    echo "Database connection/test failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
