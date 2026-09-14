# BE-W7D1-1 — Create the Database

## Project
**Database:** `logix_pulse`  
**Database engine:** PostgreSQL  
**Tools:** pgAdmin 4 or `psql`

## Included files

1. `01_create_database.sql` — creates the `logix_pulse` database.
2. `02_schema.sql` — creates all 8 required tables.
3. `03_seed_data.sql` — inserts demo users, 20 leads, and sample project-related records.
4. `04_verification_queries.sql` — verifies tables and row counts.

## Required tables

- users
- leads
- activity_logs
- projects
- invoices
- agreements
- magic_codes
- password_resets

## How to run in pgAdmin

1. Connect to the PostgreSQL VPS using the host, port, username, and password supplied by the Head.
2. Open Query Tool on the `postgres` database.
3. Run `01_create_database.sql`.
4. Refresh the Databases list.
5. Open Query Tool on the new `logix_pulse` database.
6. Run `02_schema.sql`.
7. Run `03_seed_data.sql`.
8. Run `04_verification_queries.sql`.
9. Refresh `Schemas > public > Tables`.

## How to run with psql

Replace the placeholders with the real VPS connection details:

```bash
psql -h VPS_HOST -p VPS_PORT -U VPS_ADMIN -d postgres -f 01_create_database.sql
psql -h VPS_HOST -p VPS_PORT -U VPS_ADMIN -d logix_pulse -f 02_schema.sql
psql -h VPS_HOST -p VPS_PORT -U VPS_ADMIN -d logix_pulse -f 03_seed_data.sql
psql -h VPS_HOST -p VPS_PORT -U VPS_ADMIN -d logix_pulse -f 04_verification_queries.sql
```

## Seeded users

The seed script creates:

- 1 System Administrator
- 1 Executive Member
- 1 Administrator User
- 1 VP/Manager/Head
- 5 Team Users
- 5 Client Users
- 20 sample leads

All demo users use this password:

```text
Password@123
```

**Important:** These are demo credentials only. Change or remove them before production use.

## Important note

The task screenshot says the Squad Lead will provide the exact column list. This package uses a practical, normalized starter schema. Before final production deployment, compare `02_schema.sql` with the exact column list supplied by the Squad Lead/Head and adjust names, types, and constraints if required.

Never place the real VPS password inside this ZIP or commit it to GitHub.
