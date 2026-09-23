-- ============================================
-- LogixPulse - Step 4: Verification Queries (MySQL Compatible)
-- ============================================

-- 1. Count rows in each table
SELECT 'users'           AS table_name, COUNT(*) AS row_count FROM users
UNION ALL
SELECT 'leads',          COUNT(*) FROM leads
UNION ALL
SELECT 'activity_logs',  COUNT(*) FROM activity_logs
UNION ALL
SELECT 'projects',       COUNT(*) FROM projects
UNION ALL
SELECT 'invoices',       COUNT(*) FROM invoices
UNION ALL
SELECT 'agreements',     COUNT(*) FROM agreements
UNION ALL
SELECT 'magic_codes',    COUNT(*) FROM magic_codes
UNION ALL
SELECT 'password_resets',COUNT(*) FROM password_resets;

-- 2. Verify client users exist
SELECT id, email, first_name, last_name, role, status
FROM users
WHERE role = 'client'
ORDER BY id;

-- 3. Verify invoices per client
SELECT
    u.id            AS client_id,
    u.first_name    AS client_first,
    u.last_name     AS client_last,
    COUNT(i.id)     AS invoice_count
FROM users u
LEFT JOIN invoices i ON i.client_id = u.id
WHERE u.role = 'client'
GROUP BY u.id, u.first_name, u.last_name
ORDER BY u.id;

-- 4. Verify invoice details (CONCAT used for MySQL)
SELECT
    i.invoice_number,
    CONCAT(u.first_name, ' ', u.last_name) AS client_name,
    p.name          AS project_name,
    i.amount,
    i.status,
    i.issue_date,
    i.due_date
FROM invoices i
JOIN users u    ON i.client_id = u.id
LEFT JOIN projects p ON i.project_id = p.id
ORDER BY i.invoice_number;

-- 5. Verify client 15 has zero invoices (empty state test)
SELECT
    u.id,
    u.email,
    COUNT(i.id) AS invoice_count
FROM users u
LEFT JOIN invoices i ON i.client_id = u.id
WHERE u.id = 15
GROUP BY u.id, u.email;