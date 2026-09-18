CREATE DATABASE IF NOT EXISTS logixpulse
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE logixpulse;

-- Leads
CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NULL,
    phone VARCHAR(50) NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'New',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Projects / deals
CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'active',
    meeting_date DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Deals
CREATE TABLE IF NOT EXISTS deals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id INT UNSIGNED NULL,
    title VARCHAR(180) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'open',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    closed_at DATETIME NULL,
    CONSTRAINT fk_deals_project
        FOREIGN KEY (project_id) REFERENCES projects(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- Invoices
CREATE TABLE IF NOT EXISTS invoices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    deal_id INT UNSIGNED NULL,
    invoice_number VARCHAR(80) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'unpaid',
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    invoice_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    paid_at DATETIME NULL,
    CONSTRAINT fk_invoices_deal
        FOREIGN KEY (deal_id) REFERENCES deals(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- Meetings
CREATE TABLE IF NOT EXISTS meetings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NULL,
    project_id INT UNSIGNED NULL,
    title VARCHAR(180) NOT NULL,
    scheduled_at DATETIME NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'scheduled',
    CONSTRAINT fk_meetings_lead
        FOREIGN KEY (lead_id) REFERENCES leads(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_meetings_project
        FOREIGN KEY (project_id) REFERENCES projects(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- A small demo dataset so the endpoint can be tested after import.
INSERT INTO leads (name, email, phone, status, created_at) VALUES
('Demo Lead 1', 'lead1@example.com', '03000000001', 'New', NOW()),
('Demo Lead 2', 'lead2@example.com', '03000000002', 'Contacted', NOW());

INSERT INTO projects (name, status, meeting_date, created_at) VALUES
('Demo Active Project', 'active', DATE_ADD(NOW(), INTERVAL 1 DAY), NOW()),
('Demo Closed Project', 'completed', NULL, NOW());

INSERT INTO deals (project_id, title, status, created_at, closed_at) VALUES
(1, 'Demo Open Deal', 'open', NOW(), NULL),
(2, 'Demo Closed Deal', 'closed', NOW(), NOW());

INSERT INTO meetings (lead_id, project_id, title, scheduled_at, status) VALUES
(1, 1, 'Demo Meeting', NOW(), 'scheduled');

INSERT INTO invoices (deal_id, invoice_number, status, amount, invoice_date, paid_at) VALUES
(2, 'INV-DEMO-001', 'paid', 10000, NOW(), NOW());
