# BE-W7D5-2 — Save Manual Notes on a Lead

Core PHP + MySQL implementation for XAMPP.

## What this task does

- Lets a team member type a free-text note about a lead.
- Saves the note into `activity_logs`.
- Links every activity to the correct lead using `lead_id`.
- Uses MySQL `NOW()` so the saved activity gets today's date and current time.
- Displays saved notes in that lead's activity history.

## XAMPP setup

1. Start Apache and MySQL in XAMPP.
2. Open phpMyAdmin.
3. Import `sql/schema.sql`.
4. Put this project folder inside `xampp/htdocs/`.
5. If your MySQL password is not blank, update `config/db.php`.
6. Open:
   `http://localhost/BE-W7D5-2_Save_Manual_Notes_on_a_Lead/`

## Main files

- `index.php` — selects a lead.
- `add_note.php` — free-text note form and save action.
- `activity_history.php` — shows the lead's activity history.
- `config/db.php` — PDO database connection.
- `sql/schema.sql` — database, leads, and activity_logs tables.

The code uses prepared statements and HTML escaping for safe database/input handling.
