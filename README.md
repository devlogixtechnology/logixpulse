# BE-W7D1-2 — Build the Database Connection File

This package implements the LogixPulse database connection task.

## Files

- `config/database.php` — the single reusable PDO database connection file.
- `tests/database_connection_test.php` — verifies the connection and attempts to read one row from `users`.
- `.env.example` — names the environment variables required by the connection file.

## How other PHP files use it

```php
require_once __DIR__ . '/config/database.php';

$pdo = getDatabaseConnection();
```

Then use `$pdo` for queries.

## Server configuration

Set these environment variables on the VPS/server:

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`

Real credentials are intentionally not included in this package.

## Test

From the project root:

```bash
php tests/database_connection_test.php
```

The `users` table must already exist and contain at least one row to demonstrate the final acceptance condition exactly. If the table exists but is empty, the test reports that the connection succeeded but no row was available.

## Security

Do not put production passwords or other real database credentials in Git or in a public ZIP.
