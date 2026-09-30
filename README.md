# REC-BE-02 — Harden Session-Based Authentication & Role Enforcement

Core PHP implementation for:
- Secure standardized PHP sessions
- Session-based authentication guard
- Internal staff vs external client session separation
- Role-based access control
- Session expiration
- Session ID rotation on privilege escalation
- CSRF protection for state-changing requests
- Standard JSON 401/403 responses
- Protected endpoint verification

## Requirements
- PHP 8.1+
- PostgreSQL 13+
- PDO PostgreSQL extension (`pdo_pgsql`)
- Apache/XAMPP or PHP built-in server

## Setup
1. Create a PostgreSQL database.
2. Run `database/schema.sql`.
3. Copy `.env.example` to `.env` and update database credentials.
4. If testing over plain HTTP on localhost, set:
   `SESSION_COOKIE_SECURE=false`
   In production HTTPS, set it to `true`.
5. Point Apache's document root to the project folder or place it under XAMPP `htdocs`.
6. Open `public/login.php` in a browser.

Demo accounts created by the SQL:
- staff@example.com / Password123!
- client@example.com / Password123!

The SQL stores password hashes generated with PHP `password_hash()`.

## API behavior
401 JSON:
{"success":false,"error":{"code":"UNAUTHORIZED","message":"Authentication required."}}

403 JSON:
{"success":false,"error":{"code":"FORBIDDEN","message":"You do not have permission to access this resource."}}

## Session boundaries
- `staff` sessions can access internal CRM endpoints.
- `client` sessions can access client portal endpoints.
- A client session cannot access internal endpoints, even if it has a role value that would otherwise match.
- A staff session cannot use client-only session boundaries.

## CSRF
For POST/PUT/PATCH/DELETE requests, send:
`X-CSRF-Token: <token>`

Get the token from `api/csrf_token.php` after authentication.

## Expiration and rotation
- Idle timeout: configurable through `SESSION_IDLE_TIMEOUT`.
- Absolute timeout: configurable through `SESSION_ABSOLUTE_TIMEOUT`.
- Session ID is regenerated after successful authentication.
- Session ID is regenerated again when a privilege escalation is explicitly performed.

## Verification
Use the included `tests/verify.php` for a checklist and manual API tests. The protected endpoints are:
- `api/internal_dashboard.php`
- `api/client_portal.php`
- `api/csrf_token.php`
- `api/escalate_privilege.php`

For a real deployment, use HTTPS, secure secrets, a production PostgreSQL account, and a reverse proxy/web server configuration that prevents direct access to internal files.
