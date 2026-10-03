-- ============================================
-- LogixPulse — Step 3: Seed Data (Partial)
-- ============================================

-- ---- Users ----
-- Password for ALL users: Password@123
INSERT INTO users (email, password_hash, first_name, last_name, role, status) VALUES
    ('admin@logixpulse.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'System',      'Administrator', 'admin',      'active'),
    ('exec@logixpulse.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'Sarah',       'Mitchell',     'executive',  'active'),
    ('staff@logixpulse.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'Michael',     'Torres',       'administrator', 'active'),
    ('head@logixpulse.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'David',       'Chen',         'head',       'active'),
    ('team1@logixpulse.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'Alice',       'Nelson',       'team',       'active'),
    ('team2@logixpulse.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'Bob',         'Reed',         'team',       'active'),
    ('team3@logixpulse.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'Carol',       'Palmer',       'team',       'active'),
    ('team4@logixpulse.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'Derek',       'Simmons',      'team',       'active'),
    ('team5@logixpulse.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'Eva',         'Martinez',     'team',       'active'),
    -- 5 Client Users
    ('client1@acmecorp.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'John',        'Harris',       'client',     'active'),
    ('client2@nova.com',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'Lisa',        'Wang',         'client',     'active'),
    ('client3@bluewave.io',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'Robert',      'Gomez',        'client',     'active'),
    ('client4@steelvent.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'Patricia',    'Ortiz',        'client',     'active'),
    ('client5@brightcore.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEaGVRoUbJ1Q6QJY9e1JZ3JZ3J3e', 'Kevin',       'Brooks',       'client',     'active');

-- ---- Leads (8 Shown) ----
INSERT INTO leads (first_name, last_name, email, phone, company, source, status, assigned_to, notes) VALUES
    ('Emma',    'Clark',    'emma.clark@innohub.com',     '+1-555-0101', 'InnoHub',        'website',   'new',       5,  NULL),
    ('Frank',   'Adams',    'frank.adams@solartech.com',  '+1-555-0102', 'SolarTech',      'referral',  'contacted', 6,  'Follow up next week'),
    ('Grace',   'Baker',    'grace.baker@nexgen.com',     '+1-555-0103', 'NexGen',         'linkedin',  'qualified', 7,  NULL),
    ('Henry',   'Carter',   'henry.carter@vortex.com',    '+1-555-0104', 'Vortex Labs',    'website',   'new',       5,  NULL),
    ('Ivy',     'Dixon',    'ivy.dixon@pulsedata.com',    '+1-555-0105', 'PulseData',      'cold_call', 'contacted', 8,  NULL),
    ('Jack',    'Ellis',    'jack.ellis@arclight.com',    '+1-555-0106', 'ArcLight',       'referral',  'qualified', 9,  NULL),
    ('Karen',   'Flynn',    'karen.flynn@zenithco.com',   '+1-555-0107', 'Zenith Co',      'website',   'new',       5,  NULL),
    ('Leo',     'Grant',    'leo.grant@orbitx.com',       '+1-555-0108', 'OrbitX',         'linkedin',  'contacted', 6,  NULL);