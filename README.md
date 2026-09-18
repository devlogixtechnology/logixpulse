# BE-W7D5-4 — Build the Dashboard KPI Summary Endpoint

Core PHP + MySQL endpoint for XAMPP.

## Requirement covered

The endpoint returns the four dashboard KPI numbers:

1. Total Leads
2. Active Deals
3. Meetings This Week
4. Closed Deals (Month-to-Date)

Every KPI also returns its previous equivalent period value and a calculated percentage change. Values are queried from the database; KPI numbers are not hardcoded.

## Endpoint

After placing the folder in XAMPP `htdocs`, open:

`http://localhost/BE-W7D5-4_Dashboard_KPI_Summary/api/dashboard_kpi.php`

It returns JSON.

## Database

1. Start Apache and MySQL in XAMPP.
2. Open phpMyAdmin.
3. Import `sql/schema.sql`.
4. `config/db.php` uses:
   - database: `logixpulse`
   - user: `root`
   - password: blank

If your XAMPP MySQL password/database name is different, update `config/db.php`.

## Period comparison

- Total Leads: current month-to-date vs equivalent elapsed period of the previous month.
- Active Deals: current month-to-date vs equivalent elapsed period of the previous month.
- Meetings This Week: current week vs previous week.
- Closed Deals: current month-to-date vs equivalent elapsed period of the previous month.

The endpoint detects common date/status column names and can use `deals`, `projects`, `meetings`, or `invoices` according to the available tables.

## Expected JSON shape

{
  "success": true,
  "kpis": {
    "total_leads": {
      "label": "Total Leads",
      "value": 0,
      "previous_value": 0,
      "percentage_change": 0
    },
    "active_deals": {},
    "meetings_this_week": {},
    "closed_deals_mtd": {}
  }
}
