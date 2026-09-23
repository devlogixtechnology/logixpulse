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

---

## BE-W7D4-2 — Send Activity History to the Details Panel

### Requirement
Fetch all activity log entries for a specific lead ordered from newest to oldest.

### Included
- `lead_activity.php` — activity-history endpoint (`api/lead_activity.php`).
- `lead_details.php` — lead details panel showing the complete history.
- `lead_activity.html` — test page.

### API
`GET api/lead_activity.php?lead_id=1`
Ordering: `ORDER BY created_at DESC, id DESC`.



---

## BE-W7D4-3 — Store Uploaded Invoice Files (Per-Client, Isolated)

### Requirements covered
- Admin uploads a PDF invoice for a selected client.
- Invoice amount is stored with the invoice.
- Every invoice row has a real `client_id` foreign key.
- Files are stored on the server with generated filenames.
- Clients can only list/download invoices belonging to their own account.
- Direct downloads are protected via `uploads/.htaccess` and served via `download.php`.


---

## BE-W7D5-2 — Save Manual Notes on a Lead

### Requirements
Save manual notes on a lead, generating a new `activity_logs` row with `activity_type = "note"` and current timestamp.
- `add_note.php` — Form and handler to save note.
- `activity_history.php` — Displays all activity logs including manual notes.
