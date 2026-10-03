-- ============================================
-- LogixPulse — Supabase / PostgreSQL Schema & Seed
-- Compatible with PostgreSQL 14+ / Supabase
-- ============================================

-- 1. users
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL DEFAULT '',
    last_name VARCHAR(100) NOT NULL DEFAULT '',
    name VARCHAR(200) NOT NULL DEFAULT '',
    role VARCHAR(50) NOT NULL DEFAULT 'client',
    status VARCHAR(50) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 2. leads
CREATE TABLE IF NOT EXISTS leads (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL DEFAULT '',
    first_name VARCHAR(100) DEFAULT '',
    last_name VARCHAR(100) DEFAULT '',
    email VARCHAR(255) DEFAULT '',
    phone VARCHAR(50) DEFAULT '',
    company VARCHAR(255) DEFAULT '',
    source VARCHAR(100) DEFAULT '',
    status VARCHAR(50) NOT NULL DEFAULT 'new',
    stage VARCHAR(50) NOT NULL DEFAULT 'new',
    assigned_to INT DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 3. activity_logs
CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id INT DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL,
    lead_id INT DEFAULT NULL REFERENCES leads(id) ON DELETE CASCADE,
    action VARCHAR(255) DEFAULT NULL,
    activity_type VARCHAR(100) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    note TEXT DEFAULT NULL,
    created_by VARCHAR(150) DEFAULT NULL,
    entity_type VARCHAR(100) DEFAULT NULL,
    entity_id INT DEFAULT NULL,
    details JSONB DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_activity_lead ON activity_logs (lead_id);
CREATE INDEX IF NOT EXISTS idx_activity_user ON activity_logs (user_id);
CREATE INDEX IF NOT EXISTS idx_activity_lead_created ON activity_logs (lead_id, created_at, id);

-- 4. projects
CREATE TABLE IF NOT EXISTS projects (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    client_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    status VARCHAR(50) NOT NULL DEFAULT 'active',
    start_date DATE DEFAULT NULL,
    end_date DATE DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 5. invoices
CREATE TABLE IF NOT EXISTS invoices (
    id BIGSERIAL PRIMARY KEY,
    invoice_number VARCHAR(50) DEFAULT NULL,
    client_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    project_id INT DEFAULT NULL REFERENCES projects(id) ON DELETE SET NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(50) NOT NULL DEFAULT 'draft',
    original_filename VARCHAR(255) DEFAULT NULL,
    stored_filename VARCHAR(100) DEFAULT NULL UNIQUE,
    issue_date DATE DEFAULT NULL,
    due_date DATE DEFAULT NULL,
    paid_date DATE DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_invoices_client_id ON invoices (client_id);
CREATE INDEX IF NOT EXISTS idx_invoices_status ON invoices (status);
CREATE INDEX IF NOT EXISTS idx_invoices_created_at ON invoices (created_at);

-- 6. agreements
CREATE TABLE IF NOT EXISTS agreements (
    id SERIAL PRIMARY KEY,
    client_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    project_id INT DEFAULT NULL REFERENCES projects(id) ON DELETE SET NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT DEFAULT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'draft',
    signed_date DATE DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 7. magic_codes
CREATE TABLE IF NOT EXISTS magic_codes (
    id BIGSERIAL PRIMARY KEY,
    user_id INT DEFAULT NULL REFERENCES users(id) ON DELETE CASCADE,
    email VARCHAR(190) DEFAULT NULL,
    code VARCHAR(10) DEFAULT NULL,
    code_hash VARCHAR(255) DEFAULT NULL,
    expires_at TIMESTAMP NOT NULL,
    attempts INT NOT NULL DEFAULT 0,
    used_at TIMESTAMP DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_magic_email ON magic_codes (email);
CREATE INDEX IF NOT EXISTS idx_magic_expiry ON magic_codes (expires_at);

-- 8. password_resets
CREATE TABLE IF NOT EXISTS password_resets (
    id BIGSERIAL PRIMARY KEY,
    user_id INT DEFAULT NULL REFERENCES users(id) ON DELETE CASCADE,
    email VARCHAR(190) DEFAULT NULL,
    token VARCHAR(255) DEFAULT NULL,
    token_hash CHAR(64) DEFAULT NULL,
    expires_at TIMESTAMP NOT NULL,
    attempts INT NOT NULL DEFAULT 0,
    used_at TIMESTAMP DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_reset_email ON password_resets (email);
CREATE INDEX IF NOT EXISTS idx_reset_expiry ON password_resets (expires_at);

-- 9. Kanban tables
CREATE TABLE IF NOT EXISTS columns_table (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS tasks (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    column_id INT NOT NULL REFERENCES columns_table(id) ON DELETE CASCADE,
    position INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 10. Client timeline tables
CREATE TABLE IF NOT EXISTS client_timeline_progress (
    client_id INT NOT NULL PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE,
    current_step_order INT NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS timeline_steps (
    id SERIAL PRIMARY KEY,
    client_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    step_order INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    date_label VARCHAR(50) NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_timeline_steps_client_id ON timeline_steps (client_id);

-- 11. Legacy compatibility tables
CREATE TABLE IF NOT EXISTS internal_users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    status VARCHAR(50) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clients (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'client',
    status VARCHAR(50) NOT NULL DEFAULT 'active',
    current_step_order INT NOT NULL DEFAULT 3,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Seed Data (Password for all accounts: Password@123)
-- Hash: $2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy
-- ============================================

INSERT INTO users (id, email, password_hash, first_name, last_name, name, role, status) VALUES
    (1, 'admin@logixpulse.com',    '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'System',      'Administrator', 'System Administrator', 'admin',         'active'),
    (2, 'exec@logixpulse.com',     '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Sarah',       'Mitchell',      'Sarah Mitchell',      'executive',     'active'),
    (3, 'staff@logixpulse.com',    '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Michael',     'Torres',        'Michael Torres',        'administrator', 'active'),
    (4, 'head@logixpulse.com',     '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'David',       'Chen',          'David Chen',          'head',          'active'),
    (5, 'team1@logixpulse.com',    '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Alice',       'Nelson',        'Alice Nelson',        'team',          'active'),
    (6, 'client1@acmecorp.com',    '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'John',        'Harris',        'John Harris',        'client',        'active'),
    (7, 'client2@nova.com',        '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Lisa',        'Wang',          'Lisa Wang',          'client',        'active'),
    (8, 'client3@bluewave.io',     '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'Robert',      'Gomez',         'Robert Gomez',        'client',        'active')
ON CONFLICT (id) DO UPDATE SET password_hash=EXCLUDED.password_hash, name=EXCLUDED.name;

SELECT setval('users_id_seq', (SELECT COALESCE(MAX(id), 1) FROM users));

INSERT INTO leads (id, first_name, last_name, name, email, phone, company, source, status, stage, assigned_to) VALUES
    (1, 'Emma',    'Clark',    'Emma Clark',    'emma.clark@innohub.com',     '+1-555-0101', 'InnoHub',        'website',   'new',       'New',       1),
    (2, 'Frank',   'Adams',    'Frank Adams',    'frank.adams@solartech.com',  '+1-555-0102', 'SolarTech',      'referral',  'contacted', 'Contacted', 1),
    (3, 'Grace',   'Baker',    'Grace Baker',    'grace.baker@nexgen.com',     '+1-555-0103', 'NexGen',         'linkedin',  'qualified', 'Qualified', 1)
ON CONFLICT (id) DO UPDATE SET status=EXCLUDED.status;

SELECT setval('leads_id_seq', (SELECT COALESCE(MAX(id), 1) FROM leads));

INSERT INTO columns_table (id, name, position) VALUES
    (1, 'Backlog', 1), (2, 'In Progress', 2), (3, 'Review', 3), (4, 'Done', 4)
ON CONFLICT (id) DO UPDATE SET position=EXCLUDED.position;

SELECT setval('columns_table_id_seq', (SELECT COALESCE(MAX(id), 1) FROM columns_table));

INSERT INTO tasks (title, column_id, position) VALUES
    ('Setup secure PostgreSQL Supabase config', 4, 1),
    ('Restore timeline component', 4, 2),
    ('Unify authentication & session handling', 2, 1);

INSERT INTO internal_users (name, email, password, role)
VALUES ('Admin User', 'admin@logixpulse.test', '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'admin')
ON CONFLICT (email) DO UPDATE SET password=EXCLUDED.password;

INSERT INTO clients (name, email, password, role)
VALUES ('Demo Client', 'client@logixpulse.test', '$2y$10$hBeRpcdHiE5QH9Hj/OqOburPpWY0IeyQMRVwXCyviARxo3hdrrufy', 'client')
ON CONFLICT (email) DO UPDATE SET password=EXCLUDED.password;

INSERT INTO client_timeline_progress (client_id, current_step_order) VALUES
    (6, 3), (7, 2), (8, 4)
ON CONFLICT (client_id) DO UPDATE SET current_step_order=EXCLUDED.current_step_order;

INSERT INTO timeline_steps (client_id, step_order, title, date_label) VALUES
    (6, 1, 'Project Kickoff & Discovery', 'Jan 10, 2026'),
    (6, 2, 'UI/UX Prototype Sign-off', 'Jan 24, 2026'),
    (6, 3, 'Core Architecture & API Build', 'Feb 15, 2026'),
    (6, 4, 'Quality Assurance & Staging', 'Mar 01, 2026'),
    (6, 5, 'Production Launch', 'Mar 15, 2026');

INSERT INTO projects (id, name, client_id, status) VALUES
    (1, 'LogixPulse Cloud Portal', 6, 'active'),
    (2, 'Nova ERP Integration', 7, 'active')
ON CONFLICT (id) DO UPDATE SET status=EXCLUDED.status;

SELECT setval('projects_id_seq', (SELECT COALESCE(MAX(id), 1) FROM projects));

INSERT INTO invoices (invoice_number, client_id, project_id, amount, status, original_filename, stored_filename, issue_date, due_date) VALUES
    ('INV-2026-001', 6, 1, 4500.00, 'paid', 'invoice_001.pdf', 'inv_6_001.pdf', '2026-01-15', '2026-02-15'),
    ('INV-2026-002', 6, 1, 3200.00, 'pending', 'invoice_002.pdf', 'inv_6_002.pdf', '2026-02-15', '2026-03-15'),
    ('INV-2026-003', 7, 2, 7800.00, 'draft', 'invoice_003.pdf', 'inv_7_003.pdf', '2026-03-01', '2026-04-01');
