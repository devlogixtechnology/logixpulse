# LogixPulse Backend Documentation

## BE-W7D2-1 — Connect Traditional Login to the Real Form

Core PHP + MySQL implementation for the task.

### What is included

- Real HTML internal/team login form using POST.
- Real HTML client login form using POST.
- Prepared SQL statements for database lookup.
- Password verification using `password_verify()`.
- PHP session login state.
- Internal/team users redirect to `main_dashboard.php`.
- Clients redirect to `client_dashboard.php`.
- "Who Is Logged In" endpoint connected to the same session.
- Logout functionality.
- Login error handling.
- SQL schema with demo accounts.

### XAMPP setup

1. Put the extracted folder inside:
   `C:\xampp\htdocs\`
2. Start Apache and MySQL from XAMPP.
3. Open phpMyAdmin.
4. Import:
   `database/schema.sql`
5. Open:
   `http://localhost/BE-W7D2-1_Traditional_Login_Real_Form/`

### Demo credentials

Internal/team:
- Email: `admin@logixpulse.test`
- Password: `Admin@123`

Client:
- Email: `client@logixpulse.test`
- Password: `Client@123`

### Expected result

- Internal credentials -> Main Dashboard.
- Client credentials -> Client Dashboard.
- Wrong credentials -> login page with error.
- Who Is Logged In Check -> JSON showing the current session user.
- Logout -> returns to the login selection page.

### Database

Default MySQL settings in `db.php`:
- Host: 127.0.0.1
- Database: logixpulse_auth
- User: root
- Password: empty

---

## BE-W7D2-2 — Build "Who Is Logged In" Check

Core PHP only.

### Added
- `auth_check.php` — reusable session authentication check.
- `protected.php` — protected-page example using the check.

### Existing login session
The existing `login.php` already sets:
- `$_SESSION['user_id']`
- `$_SESSION['user_name']`
- `$_SESSION['user_email']`
- `$_SESSION['user_role']`
- `$_SESSION['logged_in'] = true`

The new check uses the existing `$_SESSION['logged_in']` value.

### Acceptance
Without a valid login session, opening `protected.php` redirects to `login.html`.
With a valid login session, `protected.php` opens normally.

