# BE-W7D4-2 — Send Activity History to the Details Panel

## Requirement
Build a Core PHP script that fetches all activity log entries for one specific lead and returns them from newest to oldest so FE-A can display a complete history when a lead is opened.

## Included
- `api/lead_activity.php` — activity-history endpoint.
- `lead_details.php` — lead details panel showing the complete history.
- `index.php` — simple test page.
- `config/db.php` — PDO MySQL connection.
- `sql/schema.sql` — database/table structure and demo data.

## Activity ordering
The query uses:

`ORDER BY created_at DESC, id DESC`

This guarantees newest activities are shown first. The `id DESC` tie-breaker keeps entries with the same timestamp deterministic.

## XAMPP setup
1. Put the extracted folder inside `C:\xampp\htdocs\`.
2. Start Apache and MySQL from XAMPP.
3. Open phpMyAdmin.
4. Import `sql/schema.sql`.
5. If your database credentials differ, update `config/db.php`.
6. Open:
   `http://localhost/BE-W7D4-2_Send-Activity-History/`
7. Enter lead ID `1` (or the ID created by the SQL demo data).
8. The details panel can also be opened directly:
   `http://localhost/BE-W7D4-2_Send-Activity-History/lead_details.php?lead_id=1`

## API
GET:

`api/lead_activity.php?lead_id=1`

Example response:

```json
{
  "success": true,
  "lead_id": 1,
  "count": 3,
  "activities": [
    {
      "id": 1,
      "lead_id": 1,
      "activity_type": "Note Added",
      "description": "Customer requested a callback.",
      "created_by": "Areesha Sarwar",
      "created_at": "2026-09-23 16:30:00"
    }
  ]
}
```

The endpoint validates the lead ID, uses a prepared statement, fetches all matching activity rows, and orders them newest to oldest.
