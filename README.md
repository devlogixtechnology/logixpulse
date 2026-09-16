
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
4. Access via browser: `http://localhost/LogixPulse/