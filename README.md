# BE-W7D4-3 — Store Uploaded Invoice Files (Per-Client, Isolated)

Core PHP + MySQL module for XAMPP.

## Requirements covered
- Admin uploads a PDF invoice for a selected client.
- Invoice amount is stored with the invoice.
- Every invoice row has a real `client_id` foreign key.
- An invoice cannot be saved as unassigned.
- Files are stored on the server with generated filenames.
- Clients can only list/download invoices belonging to their own account.
- Directly requesting another client's invoice download URL/ID is rejected.
- Admin can see every client's invoices, grouped by client.
- Upload form requires client selection before saving.
- Admin and client login/logout included for testing.
- PDF validation uses extension, MIME detection, and PDF signature.
- Download access is checked server-side; the stored file path is never accepted from the browser.

## XAMPP setup
1. Extract this folder into `C:\xampp\htdocs\`.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and import `sql/schema.sql`.
4. Open `http://localhost/BE-W7D4-3-invoice-files/public/login.php`
5. Demo accounts:
   - Admin: `admin@example.com` / `admin123`
   - Client A: `clienta@example.com` / `client123`
   - Client B: `clientb@example.com` / `client456`

## Important
The project uses MySQL database `invoice_portal`.
If your MySQL username/password differs from XAMPP's default (`root` with empty password), update `config/database.php`.

For production, use a non-public storage location if possible. This demo also includes an Apache `.htaccess` deny rule inside the invoice storage directory, while all downloads go through `public/download.php`.
