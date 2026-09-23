# BE-W7D2-3 — Magic Code + Password Reset Scripts

## 1. What this project does

This project implements two flows using Core PHP and MySQL:

### Magic-code login
1. User submits an email to `request_magic_code.php`.
2. The system generates a 6-digit code.
3. Only a hash of the code is saved in `magic_codes`.
4. The code expires after 10 minutes.
5. `verify_magic_code.php` checks the code, expiry, attempt limit, and one-time usage.
6. A successful verification returns basic user information.

### Password reset
1. User submits an email to `request_password_reset.php`.
2. The system generates a secure random reset token.
3. Only a SHA-256 hash of the token is saved in `password_resets`.
4. The token expires after 30 minutes.
5. `reset_password.php` validates the token and updates the password using `password_hash()`.
6. The token is marked as used and cannot be reused.

> Email delivery is represented by returning `dev_code` and `dev_token` for local development. Replace this with a real email provider before production.

## 2. Folder explanation

```text
BE-W7D2-3-Magic-Code-Password-Reset/
├── config/
│   ├── config.php       # Database settings and expiry/attempt constants
│   ├── db.php           # PDO database connection
│   └── helpers.php      # JSON responses, validation, common functions
├── database/
│   └── setup.sql        # Database, tables, and demo user
├── public/
│   ├── request_magic_code.php
│   ├── verify_magic_code.php
│   ├── request_password_reset.php
│   └── reset_password.php
└── README.md            # Setup, testing, and API documentation
```

## 3. XAMPP setup

1. Start **Apache** and **MySQL** in XAMPP.
2. Copy this project folder into:

```text
C:/xampp/htdocs/
```

3. Open phpMyAdmin:

```text
http://localhost/phpmyadmin
```

4. Import:

```text
database/setup.sql
```

5. Open `config/config.php` and confirm:

```php
const DB_NAME = 'magic_auth_demo';
const DB_USER = 'root';
const DB_PASS = '';
```

6. The project endpoints will be available at:

```text
http://localhost/BE-W7D2-3-Magic-Code-Password-Reset/public/request_magic_code.php
http://localhost/BE-W7D2-3-Magic-Code-Password-Reset/public/verify_magic_code.php
http://localhost/BE-W7D2-3-Magic-Code-Password-Reset/public/request_password_reset.php
http://localhost/BE-W7D2-3-Magic-Code-Password-Reset/public/reset_password.php
```

## 4. Test requests

Use Postman, Thunder Client, or JavaScript `fetch()`.

### Request magic code

**POST** `public/request_magic_code.php`

```json
{
  "email": "demo@example.com"
}
```

Local response contains a development-only `dev_code`.

### Verify magic code

**POST** `public/verify_magic_code.php`

```json
{
  "email": "demo@example.com",
  "code": "123456"
}
```

Replace `123456` with the actual `dev_code`.

### Request password reset

**POST** `public/request_password_reset.php`

```json
{
  "email": "demo@example.com"
}
```

Local response contains a development-only `dev_token`.

### Reset password

**POST** `public/reset_password.php`

```json
{
  "token": "PASTE_DEV_TOKEN_HERE",
  "new_password": "NewPassword@123"
}
```

## 5. Security decisions included

- PDO prepared statements to reduce SQL injection risk.
- `password_hash()` for passwords and magic-code hashes.
- `random_int()` for six-digit codes.
- `random_bytes()` for reset tokens.
- Reset tokens are stored as SHA-256 hashes, not plaintext.
- Expiry checks are enforced.
- Codes and tokens are one-time use.
- Attempt limits reduce brute-force guessing.
- Generic request responses reduce account enumeration.
- Old active codes/tokens are invalidated when a new one is requested.
- Password reset updates happen inside a database transaction.

## 6. Important production improvements

Before deploying publicly:

- Send codes/tokens through a trusted email provider.
- Do not return `dev_code` or `dev_token` in API responses.
- Move database credentials to environment variables.
- Add HTTPS, authentication sessions/JWT, rate limiting by IP/email, audit logs, and CSRF protection where applicable.
- Add stronger password policy and email verification.
- Use a queue or background worker for email delivery.
