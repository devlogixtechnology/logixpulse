CREATE DATABASE IF NOT EXISTS logixpulse
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE logixpulse;

CREATE TABLE IF NOT EXISTS internal_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'client',
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(50) DEFAULT '',
    status VARCHAR(50) NOT NULL DEFAULT 'New',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NOT NULL,
    activity_type VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    created_by VARCHAR(150) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activity_lead_created (lead_id, created_at, id),
    CONSTRAINT fk_activity_lead
        FOREIGN KEY (lead_id) REFERENCES leads(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Demo password for internal user: Admin@123
INSERT INTO internal_users (name, email, password, role)
VALUES (
    'Admin User',
    'admin@logixpulse.test',
    '$2y$12$BiMOP34TRyCzcYKekoS4vOVIp5GHANli5qkAe7RaxIBouVF.wFwaq',
    'admin'
) ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Demo password for client: Client@123
INSERT INTO clients (name, email, password, role)
VALUES (
    'Demo Client',
    'client@logixpulse.test',
    '$2y$12$yVPF3xit1YvQwTXfwlrrBuxx8qgJcQKAjEuUngzcVZfXMYIlCydR.',
    'client'
) ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Demo leads and activity logs
INSERT INTO leads (name, email, phone, status)
SELECT 'Ali Khan', 'ali@example.com', '03001234567', 'Contacted'
WHERE NOT EXISTS (SELECT 1 FROM leads WHERE email = 'ali@example.com');

SET @demo_lead_id = (SELECT id FROM leads WHERE email = 'ali@example.com' LIMIT 1);

INSERT INTO activity_logs (lead_id, activity_type, description, created_by, created_at)
SELECT @demo_lead_id, 'Lead Created', 'Lead was added to the CRM.', 'Areesha Sarwar', '2026-09-23 14:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM activity_logs
    WHERE lead_id = @demo_lead_id
      AND activity_type = 'Lead Created'
);

