# LogixPulse

## API Endpoints

### BE-W7D4-1 — Save Lead Stage Changes
Updates a lead stage and creates a matching activity log whenever the stage changes.

**Endpoint:**
`POST api/update_lead_stage.php`

**JSON Payload:**
```json
{
  "lead_id": 1,
  "new_stage": "Meeting Booked"
}
```

**Expected Log:**
`Moved from Contacted to Meeting Booked.`

---

## Database Setup

1. Connect to PostgreSQL (pgAdmin or psql)
2. Run SQL files in order inside `sql/` folder
3. Update `config/database.php` with your DB credentials

## Login

Demo client credentials (from seed data):
- Email: see `03_seed_data.sql` for client user emails
- Password: `Password@123`

## Run Locally

**Prerequisites:** PHP 8+, PostgreSQL, Apache/Nginx

1. Place project in web server document root
2. Run SQL files to set up the database
3. Update `config/database.php` with real DB credentials
4. Access via browser: `http://localhost/LogixPulse/`

---

# Backend Documentation (BE Tasks)

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

### Demo credentials
Internal/team:
- Email: `admin@logixpulse.test`
- Password: `Admin@123`

Client:
- Email: `client@logixpulse.test`
- Password: `Client@123`

---

## BE-W7D2-2 — Build "Who Is Logged In" Check

Core PHP session check.
- `auth_check.php` — reusable session authentication check.
- `protected.php` — protected-page example using the check.

---

## BE-W7D4-2 — Send Activity History to the Details Panel
Fetch all activity log entries for a specific lead ordered from newest to oldest.
- `lead_activity.php` — activity-history endpoint (`api/lead_activity.php`).
- `lead_details.php` — lead details panel showing the complete history.
- `lead_activity.html` — test page.

API: `GET api/lead_activity.php?lead_id=1`
Ordering: `ORDER BY created_at DESC, id DESC`.

---

## BE-W7D4-3 — Store Uploaded Invoice Files (Per-Client, Isolated)
- Admin uploads a PDF invoice for a selected client.
- Invoice amount is stored with the invoice.
- Every invoice row has a real `client_id` foreign key.
- Files are stored on the server with generated filenames.
- Clients can only list/download invoices belonging to their own account.
- Direct downloads are protected via `uploads/.htaccess` and served via `download.php`.

---

## BE-W7D5-2 — Save Manual Notes on a Lead
Save manual notes on a lead, generating a new `activity_logs` row with `activity_type = "note"` and current timestamp.
- `add_note.php` — Form and handler to save note.
- `activity_history.php` — Displays all activity logs including manual notes.

---

## BE-W7D5-4 — Build the Dashboard KPI Summary Endpoint
Single endpoint returning key metrics for the management dashboard:
- Total leads count
- Leads grouped by status
- Recent activity count
- Total invoices and revenue
- Endpoint: `dashboard_kpi.php`
