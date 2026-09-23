# BE-W7D2-2 — Build "Who Is Logged In" Check

Core PHP only.

## Added
- `auth_check.php` — reusable session authentication check.
- `protected.php` — protected-page example using the check.

## Existing login session
The existing `login.php` already sets:
- `$_SESSION['user_id']`
- `$_SESSION['user_name']`
- `$_SESSION['user_email']`
- `$_SESSION['user_role']`
- `$_SESSION['logged_in'] = true`

The new check uses the existing `$_SESSION['logged_in']` value.

## Acceptance
Without a valid login session, opening `protected.php` redirects to `login.html`.
With a valid login session, `protected.php` opens normally.
