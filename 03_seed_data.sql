-- LOGIX PULSE DEMO DATA
-- Run this file after 02_schema.sql while connected to logix_pulse.
-- Demo password for all seeded users: Password@123

INSERT INTO users (full_name, email, password_hash, role, phone)
VALUES
('System Administrator', 'system.admin@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'system_administrator', '+92-300-0000001'),
('Executive Member', 'executive@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'executive_member', '+92-300-0000002'),
('Administrator User', 'administrator@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'administrator', '+92-300-0000003'),
('VP Manager Head', 'vp.head@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'vp_manager_head', '+92-300-0000004'),
('Team User 1', 'team1@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'team_user', '+92-300-0000011'),
('Team User 2', 'team2@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'team_user', '+92-300-0000012'),
('Team User 3', 'team3@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'team_user', '+92-300-0000013'),
('Team User 4', 'team4@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'team_user', '+92-300-0000014'),
('Team User 5', 'team5@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'team_user', '+92-300-0000015'),
('Client User 1', 'client1@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'client_user', '+92-300-0000021'),
('Client User 2', 'client2@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'client_user', '+92-300-0000022'),
('Client User 3', 'client3@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'client_user', '+92-300-0000023'),
('Client User 4', 'client4@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'client_user', '+92-300-0000024'),
('Client User 5', 'client5@logixpulse.test', crypt('Password@123', gen_salt('bf')), 'client_user', '+92-300-0000025');

INSERT INTO leads
(assigned_to, first_name, last_name, company_name, email, phone, source, status, estimated_value, notes)
SELECT
    (SELECT id FROM users WHERE email = 'team' || (((g - 1) % 5) + 1) || '@logixpulse.test'),
    'Lead' || g,
    'Customer',
    'Demo Company ' || g,
    'lead' || g || '@example.test',
    '+92-301-' || LPAD(g::text, 7, '0'),
    CASE WHEN g % 3 = 0 THEN 'website' WHEN g % 3 = 1 THEN 'referral' ELSE 'social_media' END,
    CASE WHEN g % 5 = 0 THEN 'qualified'
         WHEN g % 4 = 0 THEN 'contacted'
         WHEN g % 7 = 0 THEN 'proposal'
         ELSE 'new' END,
    (g * 1250.00),
    'Sample lead record for testing.'
FROM generate_series(1, 20) AS s(g);

INSERT INTO projects
(client_id, manager_id, project_name, description, status, start_date, due_date, budget)
VALUES
(
 (SELECT id FROM users WHERE email = 'client1@logixpulse.test'),
 (SELECT id FROM users WHERE email = 'vp.head@logixpulse.test'),
 'Client Portal Development',
 'Demo client portal project.',
 'active', CURRENT_DATE, CURRENT_DATE + 60, 250000
);

INSERT INTO invoices
(project_id, client_id, invoice_number, amount, status, due_date)
VALUES
(
 (SELECT id FROM projects WHERE project_name = 'Client Portal Development'),
 (SELECT id FROM users WHERE email = 'client1@logixpulse.test'),
 'INV-0001', 75000, 'unpaid', CURRENT_DATE + 15
);

INSERT INTO agreements
(project_id, client_id, agreement_title, agreement_text, status)
VALUES
(
 (SELECT id FROM projects WHERE project_name = 'Client Portal Development'),
 (SELECT id FROM users WHERE email = 'client1@logixpulse.test'),
 'Client Portal Service Agreement',
 'This is a sample agreement for development and testing.',
 'draft'
);

INSERT INTO activity_logs (user_id, lead_id, action, description)
VALUES
(
 (SELECT id FROM users WHERE email = 'administrator@logixpulse.test'),
 (SELECT id FROM leads WHERE email = 'lead1@example.test'),
 'created',
 'Initial demo lead created.'
);
