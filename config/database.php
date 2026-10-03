<?php
declare(strict_types=1);

/**
 * LogixPulse — Unified Database Configuration & Connection Helper
 *
 * Supports PostgreSQL (Supabase / AWS / Cloud) and MySQL (Local XAMPP / MariaDB).
 * Supports DATABASE_URL connection string as well as individual DB_* environment variables.
 * Provides singleton PDO instances, fallback handling, and global $pdo.
 */

// Load .env if present
(function () {
    $envPath = dirname(__DIR__) . '/.env';
    if (!file_exists($envPath)) {
        return;
    }
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (getenv($key) === false) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
            }
        }
    }
})();

if (!function_exists('getDatabaseConnection')) {
    /**
     * Get primary PDO connection (throws on error).
     */
    function getDatabaseConnection(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $connection = strtolower((string)(getenv('DB_CONNECTION') ?: ''));
        $dbUrl = getenv('DATABASE_URL') ?: getenv('DIRECT_URL') ?: '';

        // If DATABASE_URL is provided, parse it
        if (!empty($dbUrl)) {
            $parsed = parse_url($dbUrl);
            if ($parsed && isset($parsed['scheme'])) {
                $scheme = strtolower($parsed['scheme']);
                if (in_array($scheme, ['postgres', 'postgresql', 'pgsql'], true)) {
                    $connection = 'pgsql';
                } elseif ($scheme === 'mysql') {
                    $connection = 'mysql';
                }

                $host = $parsed['host'] ?? '127.0.0.1';
                $port = isset($parsed['port']) ? (string)$parsed['port'] : ($connection === 'pgsql' ? '5432' : '3306');
                $dbname = isset($parsed['path']) ? ltrim($parsed['path'], '/') : ($connection === 'pgsql' ? 'postgres' : 'logix_pulse');
                $username = isset($parsed['user']) ? urldecode($parsed['user']) : '';
                $password = isset($parsed['pass']) ? urldecode($parsed['pass']) : '';
            }
        }

        // Fall back to discrete DB_* environment variables
        if (!isset($host)) {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $port = getenv('DB_PORT') ?: ($connection === 'pgsql' ? '6543' : '3306');
            $dbname = getenv('DB_NAME') ?: ($connection === 'pgsql' ? 'postgres' : 'logix_pulse');
            $username = getenv('DB_USER') ?: ($connection === 'pgsql' ? 'postgres' : 'root');
            $password = getenv('DB_PASS') !== false ? (string)getenv('DB_PASS') : '';
        }

        // Auto-detect pgsql if host contains supabase or port is 5432/6543
        if ($connection === '' && (str_contains($host, 'supabase') || $port === '6543' || $port === '5432')) {
            $connection = 'pgsql';
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // 1. PostgreSQL (Supabase / Postgres)
        if ($connection === 'pgsql' || $connection === 'postgres') {
            $sslmode = getenv('DB_SSLMODE') ?: 'require';
            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$sslmode}";

            try {
                $pdo = new PDO($dsn, $username, $password, $options);
                return $pdo;
            } catch (PDOException $e) {
                // If pooler port 6543 failed, try session pooler port 5432
                if ($port === '6543') {
                    try {
                        $altDsn = "pgsql:host={$host};port=5432;dbname={$dbname};sslmode={$sslmode}";
                        $pdo = new PDO($altDsn, $username, $password, $options);
                        return $pdo;
                    } catch (PDOException $altEx) {
                        // ignore and throw original
                    }
                }
                error_log('[LogixPulse] Supabase/PostgreSQL connection failed: ' . $e->getMessage());
                throw $e;
            }
        }

        // 2. MySQL / MariaDB (Local XAMPP / MariaDB)
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $username, $password, $options);
            return $pdo;
        } catch (PDOException $e) {
            // Fallback attempt: if logix_pulse failed, try alternative database name logixpulse
            if ($dbname === 'logix_pulse') {
                try {
                    $altDsn = "mysql:host={$host};port={$port};dbname=logixpulse;charset=utf8mb4";
                    $pdo = new PDO($altDsn, $username, $password, $options);
                    return $pdo;
                } catch (PDOException $altEx) {
                    // Ignore fallback failure and throw original
                }
            }
            error_log('[LogixPulse] MySQL connection failed: ' . $e->getMessage());
            throw $e;
        }
    }
}

if (!function_exists('getDbConnection')) {
    /**
     * Safe PDO connection helper: returns null on failure so mock mode can activate.
     */
    function getDbConnection(): ?PDO
    {
        static $cached = null;
        static $tested = false;

        if ($tested) {
            return $cached;
        }
        $tested = true;

        if (getenv('DB_MOCK_MODE') === 'true') {
            return null;
        }

        try {
            $cached = getDatabaseConnection();
        } catch (Throwable $e) {
            $cached = null;
        }

        return $cached;
    }
}

if (!function_exists('db')) {
    /**
     * Alias for getDatabaseConnection().
     */
    function db(): PDO
    {
        return getDatabaseConnection();
    }
}

// Global PDO instance for scripts expecting global $pdo
global $pdo;
if (!isset($pdo) || !$pdo instanceof PDO) {
    $pdo = getDbConnection();
}

// Optional global MySQLi connection for legacy scripts expecting $conn
global $conn;
if (!isset($conn) && class_exists('mysqli')) {
    $connType = strtolower((string)(getenv('DB_CONNECTION') ?: ''));
    if ($connType !== 'pgsql' && $connType !== 'postgres') {
        $mHost = getenv('DB_HOST') ?: '127.0.0.1';
        $mUser = getenv('DB_USER') ?: 'root';
        $mPass = getenv('DB_PASS') !== false ? (string)getenv('DB_PASS') : '';
        $mDb   = getenv('DB_NAME') ?: 'logix_pulse';

        try {
            $conn = @new mysqli($mHost, $mUser, $mPass, $mDb);
            if ($conn->connect_error) {
                $conn = null;
            } else {
                $conn->set_charset('utf8mb4');
            }
        } catch (Throwable $e) {
            $conn = null;
        }
    }
}