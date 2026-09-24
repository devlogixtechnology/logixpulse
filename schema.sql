-- ============================================
-- LogixPulse — Complete Unified Schema & Seed
-- ============================================

CREATE DATABASE IF NOT EXISTS logix_pulse
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE logix_pulse;

-- 1. users
CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    email         VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name    VARCHAR(100) NOT NULL DEFAULT '',
    last_name     VARCHAR(100) NOT NULL DEFAULT '',
    name          VARCHAR(200) NOT NULL DEFAULT '',
    role          VARCHAR(50)  NOT NULL DEFAULT 'client',
    status        VARCHAR(50)  NOT NULL DEFAULT 'active',
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. leads
CREATE TABLE IF NOT EXISTS leads (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(200) NOT NULL DEFAULT '',
    first_name    VARCHAR(100) DEFAULT '',
    last_name     VARCHAR(100) DEFAULT '',
    email         VARCHAR(255) DEFAULT '',
    phone         VARCHAR(50)  DEFAULT '',
    company       VARCHAR(255) DEFAULT '',
    source        VARCHAR(100) DEFAULT '',
    status        VARCHAR(50)  NOT NULL DEFAULT 'new',
    stage         VARCHAR(50)  NOT NULL DEFAULT 'new',
    assigned_to   INT          DEFAULT NULL,
    notes         TEXT         DEFAULT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 3. activity_logs
CREATE TABLE IF NOT EXISTS activity_logs (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT          DEFAULT NULL,
    lead_id       INT          DEFAULT NULL,
    action        VARCHAR(255) DEFAULT NULL,
    activity_type VARCHAR(100) DEFAULT NULL,
    description   TEXT         DEFAULT NULL,
    note          TEXT         DEFAULT NULL,
    created_by    VARCHAR(150) DEFAULT NULL,
    entity_type   VARCHAR(100) DEFAULT NULL,
    entity_id     INT          DEFAULT NULL,
    details       JSON         DEFAULT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activity_lead (lead_id),
    INDEX idx_activity_user (user_id),
    INDEX idx_activity_lead_created (lead_id, created_at, id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. projects
CREATE TABLE IF NOT EXISTS projects (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(255) NOT NULL,
    client_id     INT          NOT NULL,
    status        VARCHAR(50)  NOT NULL DEFAULT 'active',
    start_date    DATE         DEFAULT NULL,
    end_date      DATE         DEFAULT NULL,
    description   TEXT         DEFAULT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. invoices
CREATE TABLE IF NOT EXISTS invoices (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_number    VARCHAR(50)    DEFAULT NULL,
    client_id         INT            NOT NULL,
    project_id        INT            DEFAULT NULL,
    amount            DECIMAL(12,2)  NOT NULL DEFAULT 0.00,
    status            VARCHAR(50)    NOT NULL DEFAULT 'draft',
    original_filename VARCHAR(255)   DEFAULT NULL,
    stored_filename   VARCHAR(100)   DEFAULT NULL UNIQUE,
    issue_date        DATE           DEFAULT NULL,
    due_date          DATE           DEFAULT NULL,
    paid_date         DATE           DEFAULT NULL,
    notes             TEXT           DEFAULT NULL,
    created_at        TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_invoices_client_id (client_id),
    INDEX idx_invoices_status (status),
    INDEX idx_invoices_created_at (created_at),
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. agreements
CREATE TABLE IF NOT EXISTS agreements (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    client_id     INT          NOT NULL,
    project_id    INT          DEFAULT NULL,
    title         VARCHAR(255) NOT NULL,
    content       TEXT         DEFAULT NULL,
    status        VARCHAR(50)  NOT NULL DEFAULT 'draft',
    signed_date   DATE         DEFAULT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 7. magic_codes
CREATE TABLE IF NOT EXISTS magic_codes (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT          DEFAULT NULL,
    email         VARCHAR(190) DEFAULT NULL,
    code          VARCHAR(10)  DEFAULT NULL,
    code_hash     VARCHAR(255) DEFAULT NULL,
    expires_at    DATETIME     NOT NULL,
    attempts      INT UNSIGNED NOT NULL DEFAULT 0,
    used_at       DATETIME     DEFAULT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_magic_email (email),
    INDEX idx_magic_expiry (expires_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. password_resets
CREATE TABLE IF NOT EXISTS password_resets (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT          DEFAULT NULL,
    email         VARCHAR(190) DEFAULT NULL,
    token         VARCHAR(255) DEFAULT NULL,
    token_hash    CHAR(64)     DEFAULT NULL,
    expires_at    DATETIME     NOT NULL,
    attempts      INT UNSIGNED NOT NULL DEFAULT 0,
    used_at       DATETIME     DEFAULT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reset_email (email),
    INDEX idx_reset_expiry (expires_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Kanban Board tables (columns_table and tasks)
CREATE TABLE IF NOT EXISTS columns_table (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    position      INT          NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tasks (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(255) NOT NULL,
    column_id     INT          NOT NULL,
    position      INT          NOT NULL DEFAULT 0,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (column_id) REFERENCES columns_table(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. Client Timeline tables
CREATE TABLE IF NOT EXISTS client_timeline_progress (
    client_id           INT NOT NULL PRIMARY KEY,
    current_step_order  INT NOT NULL DEFAULT 1,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS timeline_steps (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    client_id   INT NOT NULL,
    step_order  INT NOT NULL,
    title       VARCHAR(150) NOT NULL,
    date_label  VARCHAR(50) NOT NULL,
    INDEX idx_timeline_steps_client_id (client_id),
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 11. Legacy compatibility tables for BE demo forms
CREATE TABLE IF NOT EXISTS internal_users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(190) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       VARCHAR(50)  NOT NULL DEFAULT 'user',
    status     ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS clients (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(190) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       VARCHAR(50)  NOT NULL DEFAULT 'client',
    status     ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Seed Data (Password for all accounts: Password@123)
-- ============================================
INSERT INTO users (email, password_hash, first_name, last_name, name, role, status) VALUES
    ('admin@logixpulse.com',    '$2y$10$wA8hZ9.1vK8m2pQ9s7L6v.KDMPcidYLXlnBwsaYbtJcCpAKnJdnl6', 'System',      'Administrator', 'System Administrator', 'admin',         'active'),
    ('exec@logixpulse.com',     '$2y$10$wA8hZ9.1vK8m2pQ9s7L6v.KDMPcidYLXlnBwsaYbtJcCpAKnJdnl6', 'Sarah',       'Mitchell',      'Sarah Mitchell',      'executive',     'active'),
    ('staff@logixpulse.com',    '$2y$10$wA8hZ9.1vK8m2pQ9s7L6v.KDMPcidYLXlnBwsaYbtJcCpAKnJdnl6', 'Michael',     'Torres',        'Michael Torres',        'administrator', 'active'),
    ('head@logixpulse.com',     '$2y$10$wA8hZ9.1vK8m2pQ9s7L6v.KDMPcidYLXlnBwsaYbtJcCpAKnJdnl6', 'David',       'Chen',          'David Chen',          'head',          'active'),
    ('team1@logixpulse.com',    '$2y$10$wA8hZ9.1vK8m2pQ9s7L6v.KDMPcidYLXlnBwsaYbtJcCpAKnJdnl6', 'Alice',       'Nelson',        'Alice Nelson',        'team',          'active'),
    ('client1@acmecorp.com',    '$2y$10$wA8hZ9.1vK8m2pQ9s7L6v.KDMPcidYLXlnBwsaYbtJcCpAKnJdnl6', 'John',        'Harris',        'John Harris',        'client',        'active'),
    ('client2@nova.com',        '$2y$10$wA8hZ9.1vK8m2pQ9s7L6v.KDMPcidYLXlnBwsaYbtJcCpAKnJdnl6', 'Lisa',        'Wang',          'Lisa Wang',          'client',        'active'),
    ('client3@bluewave.io',     '$2y$10$wA8hZ9.1vK8m2pQ9s7L6v.KDMPcidYLXlnBwsaYbtJcCpAKnJdnl6', 'Robert',      'Gomez',         'Robert Gomez',        'client',        'active')
ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash), name=VALUES(name);

INSERT INTO leads (first_name, last_name, name, email, phone, company, source, status, stage, assigned_to) VALUES
    ('Emma',    'Clark',    'Emma Clark',    'emma.clark@innohub.com',     '+1-555-0101', 'InnoHub',        'website',   'new',       'New',       1),
    ('Frank',   'Adams',    'Frank Adams',    'frank.adams@solartech.com',  '+1-555-0102', 'SolarTech',      'referral',  'contacted', 'Contacted', 1),
    ('Grace',   'Baker',    'Grace Baker',    'grace.baker@nexgen.com',     '+1-555-0103', 'NexGen',         'linkedin',  'qualified', 'Qualified', 1)
ON DUPLICATE KEY UPDATE status=VALUES(status);

INSERT INTO columns_table (name, position) VALUES
    ('Backlog', 1), ('In Progress', 2), ('Review', 3), ('Done', 4)
ON DUPLICATE KEY UPDATE position=VALUES(position);

INSERT INTO tasks (title, column_id, position) VALUES
    ('Setup secure MySQL database config', 4, 1),
    ('Restore timeline component', 4, 2),
    ('Unify authentication & session handling', 2, 1);

INSERT INTO internal_users (name, email, password, role)
VALUES ('Admin User', 'admin@logixpulse.test', '$2y$10$wA8hZ9.1vK8m2pQ9s7L6v.KDMPcidYLXlnBwsaYbtJcCpAKnJdnl6', 'admin')
ON DUPLICATE KEY UPDATE password=VALUES(password);

INSERT INTO clients (name, email, password, role)
VALUES ('Demo Client', 'client@logixpulse.test', '$2y$10$wA8hZ9.1vK8m2pQ9s7L6v.KDMPcidYLXlnBwsaYbtJcCpAKnJdnl6', 'client')
ON DUPLICATE KEY UPDATE password=VALUES(password);
