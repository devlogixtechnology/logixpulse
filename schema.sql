CREATE DATABASE IF NOT EXISTS lead_activity_db
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE lead_activity_db;

CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NULL,
    phone VARCHAR(50) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NOT NULL,
    activity_type VARCHAR(50) NOT NULL DEFAULT 'note',
    note TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_activity_lead
        FOREIGN KEY (lead_id) REFERENCES leads(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    INDEX idx_activity_lead_created (lead_id, created_at)
) ENGINE=InnoDB;

INSERT INTO leads (name, email, phone)
SELECT 'Demo Lead', 'demo@example.com', '0300-0000000'
WHERE NOT EXISTS (SELECT 1 FROM leads LIMIT 1);
