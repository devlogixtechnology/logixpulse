<?php
declare(strict_types=1);

final class Database
{
    private static ?PDO $connection = null;

    private function __construct() {}

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            try {
                self::$connection->query('SELECT 1');
                return self::$connection;
            } catch (PDOException) {
                self::$connection = null;
            }
        }

        $host = (string)env_value('DB_HOST', '127.0.0.1');
        $port = (string)env_value('DB_PORT', '5432');
        $name = (string)env_value('DB_NAME', 'crm');
        $user = (string)env_value('DB_USER', 'postgres');
        $password = (string)env_value('DB_PASSWORD', '');

        $dsn = "pgsql:host={$host};port={$port};dbname={$name}";

        try {
            self::$connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5,
            ]);
        } catch (PDOException $e) {
            json_error(
                503,
                'DATABASE_UNAVAILABLE',
                'Database service is temporarily unavailable.'
            );
        }

        return self::$connection;
    }
}
