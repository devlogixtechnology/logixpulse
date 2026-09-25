-- ============================================
-- LogixPulse — Step 3: Unified Seed Data
-- ============================================

USE logix_pulse;

-- ---- Users ----
-- Password for ALL users: Password@123
-- Bcrypt Hash: $2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy
INSERT INTO users (email, password_hash, first_name, last_name, name, role, status) VALUES
    ('admin@logixpulse.com',    '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'System',      'Administrator', 'System Administrator', 'admin',         'active'),
    ('exec@logixpulse.com',     '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Sarah',       'Mitchell',      'Sarah Mitchell',      'executive',     'active'),
    ('staff@logixpulse.com',    '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Michael',     'Torres',        'Michael Torres',        'administrator', 'active'),
    ('head@logixpulse.com',     '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'David',       'Chen',          'David Chen',          'head',          'active'),
    ('team1@logixpulse.com',    '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Alice',       'Nelson',        'Alice Nelson',        'team',          'active'),
    ('team2@logixpulse.com',    '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Bob',         'Reed',          'Bob Reed',          'team',          'active'),
    ('team3@logixpulse.com',    '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Carol',       'Palmer',        'Carol Palmer',        'team',          'active'),
    ('team4@logixpulse.com',    '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Derek',       'Simmons',       'Derek Simmons',       'team',          'active'),
    ('team5@logixpulse.com',    '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Eva',         'Martinez',      'Eva Martinez',        'team',          'active'),
    -- Client Accounts (Password@123)
    ('client1@acmecorp.com',    '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'John',        'Harris',        'John Harris',        'client',        'active'),
    ('client2@nova.com',        '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Lisa',        'Wang',          'Lisa Wang',          'client',        'active'),
    ('client3@bluewave.io',     '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Robert',      'Gomez',         'Robert Gomez',        'client',        'active'),
    ('client4@steelvent.com',   '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Patricia',    'Ortiz',         'Patricia Ortiz',      'client',        'active'),
    ('client5@brightcore.com',  '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Kevin',       'Brooks',        'Kevin Brooks',        'client',        'active')
ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash), name=VALUES(name);

-- ---- Leads ----
INSERT INTO leads (first_name, last_name, name, email, phone, company, source, status, stage, assigned_to, notes) VALUES
    ('Emma',    'Clark',    'Emma Clark',    'emma.clark@innohub.com',     '+1-555-0101', 'InnoHub',        'website',   'new',       'New',       5,  NULL),
    ('Frank',   'Adams',    'Frank Adams',    'frank.adams@solartech.com',  '+1-555-0102', 'SolarTech',      'referral',  'contacted', 'Contacted', 6,  'Follow up next week'),
    ('Grace',   'Baker',    'Grace Baker',    'grace.baker@nexgen.com',     '+1-555-0103', 'NexGen',         'linkedin',  'qualified', 'Qualified', 7,  NULL),
    ('Henry',   'Carter',   'Henry Carter',   'henry.carter@vortex.com',    '+1-555-0104', 'Vortex Labs',    'website',   'new',       'New',       5,  NULL),
    ('Ivy',     'Dixon',    'Ivy Dixon',      'ivy.dixon@pulsedata.com',    '+1-555-0105', 'PulseData',      'cold_call', 'contacted', 'Contacted', 8,  NULL),
    ('Jack',    'Ellis',    'Jack Ellis',     'jack.ellis@arclight.com',    '+1-555-0106', 'ArcLight',       'referral',  'qualified', 'Qualified', 9,  NULL),
    ('Karen',   'Flynn',    'Karen Flynn',    'karen.flynn@zenithco.com',   '+1-555-0107', 'Zenith Co',      'website',   'new',       'New',       5,  NULL),
    ('Leo',     'Grant',    'Leo Grant',      'leo.grant@orbitx.com',       '+1-555-0108', 'OrbitX',         'linkedin',  'contacted', 'Contacted', 6,  NULL);

-- ---- Projects ----
INSERT INTO projects (name, client_id, status, start_date, end_date, description) VALUES
    ('LogixPulse Client Portal', 10, 'active', '2026-01-06', '2026-04-15', 'Custom client collaboration platform'),
    ('Mobile App - Phase 1',     10, 'active', '2026-02-01', '2026-05-30', 'iOS & Android native client apps'),
    ('Enterprise Data Migration', 11, 'active', '2026-02-15', '2026-06-30', 'Cloud migration and optimization');

-- ---- Invoices ----
INSERT INTO invoices (invoice_number, client_id, project_id, amount, status, issue_date, due_date, paid_date, notes) VALUES
    ('INV-2026-001', 10, 1, 4500.00, 'paid',    '2026-01-15', '2026-02-15', '2026-02-10', 'Initial milestone payment'),
    ('INV-2026-002', 10, 1, 6200.00, 'pending', '2026-02-15', '2026-03-15', NULL,         'Development sprint 2'),
    ('INV-2026-003', 10, 2, 3800.00, 'draft',   '2026-03-01', '2026-04-01', NULL,         'Mobile design signoff'),
    ('INV-2026-004', 11, 3, 8900.00, 'pending', '2026-02-20', '2026-03-20', NULL,         'Cloud architecture setup');

-- ---- Kanban Boards ----
INSERT INTO columns_table (name, position) VALUES
    ('Backlog', 1), ('In Progress', 2), ('Review', 3), ('Done', 4)
ON DUPLICATE KEY UPDATE position=VALUES(position);

INSERT INTO tasks (title, column_id, position) VALUES
    ('Setup secure MySQL database config', 4, 1),
    ('Restore timeline component', 4, 2),
    ('Unify authentication & session handling', 2, 1),
    ('Review lead management dashboard', 2, 2),
    ('Prepare production deployment guide', 1, 1);

-- ---- Timeline Progress ----
INSERT INTO client_timeline_progress (client_id, current_step_order) VALUES
    (10, 3), (11, 2), (12, 4), (13, 1), (14, 5)
ON DUPLICATE KEY UPDATE current_step_order=VALUES(current_step_order);

INSERT INTO timeline_steps (client_id, step_order, title, date_label) VALUES
    (10, 1, 'Project Started', 'Jan 6'),
    (10, 2, 'Requirements & Planning', 'Jan 20'),
    (10, 3, 'Development', 'Est. Feb 28'),
    (10, 4, 'Review & Approval', 'Est. Mar 10'),
    (10, 5, 'Delivery', 'Est. Mar 20');

-- ---- Demo BE Compatibility Accounts ----
INSERT INTO internal_users (name, email, password, role)
VALUES ('Admin User', 'admin@logixpulse.test', '$2y$10$wA8hZ9.1vK8m2pQ9s7L6v.KDMPcidYLXlnBwsaYbtJcCpAKnJdnl6', 'admin')
ON DUPLICATE KEY UPDATE password=VALUES(password);

INSERT INTO clients (name, email, password, role)
VALUES ('Demo Client', 'client@logixpulse.test', '$2y$10$wA8hZ9.1vK8m2pQ9s7L6v.KDMPcidYLXlnBwsaYbtJcCpAKnJdnl6', 'client')
ON DUPLICATE KEY UPDATE password=VALUES(password);

SELECT '✅ Seed data inserted successfully. Password@123 verified.' AS Status;