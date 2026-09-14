-- Run these queries to verify the task completion.

SELECT current_database();

SELECT table_name
FROM information_schema.tables
WHERE table_schema = 'public'
ORDER BY table_name;

SELECT role, COUNT(*) AS total_users
FROM users
GROUP BY role
ORDER BY role;

SELECT COUNT(*) AS total_leads FROM leads;

SELECT
    (SELECT COUNT(*) FROM users) AS users_count,
    (SELECT COUNT(*) FROM leads) AS leads_count,
    (SELECT COUNT(*) FROM activity_logs) AS activity_logs_count,
    (SELECT COUNT(*) FROM projects) AS projects_count,
    (SELECT COUNT(*) FROM invoices) AS invoices_count,
    (SELECT COUNT(*) FROM agreements) AS agreements_count,
    (SELECT COUNT(*) FROM magic_codes) AS magic_codes_count,
    (SELECT COUNT(*) FROM password_resets) AS password_resets_count;
