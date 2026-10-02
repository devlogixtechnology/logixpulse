CREATE DATABASE IF NOT EXISTS client_onboarding
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE client_onboarding;

CREATE TABLE IF NOT EXISTS contracts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id VARCHAR(100) NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    status ENUM('Draft', 'Sent', 'Viewed', 'Executed', 'Expired') NOT NULL DEFAULT 'Sent',
    signer_legal_name VARCHAR(200) NULL,
    executed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_contract_client (client_id),
    INDEX idx_contract_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contract_audits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    contract_id INT UNSIGNED NOT NULL,
    action VARCHAR(50) NOT NULL,
    signer_legal_name VARCHAR(200) NOT NULL,
    signature_path VARCHAR(500) NOT NULL,
    signature_sha256 CHAR(64) NOT NULL,
    signer_ip VARCHAR(45) NOT NULL,
    user_agent VARCHAR(1000) NOT NULL,
    executed_at DATETIME NOT NULL,
    certificate_hash CHAR(64) NOT NULL,
    certificate_json JSON NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_contract
        FOREIGN KEY (contract_id) REFERENCES contracts(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY uq_audit_certificate (certificate_hash),
    INDEX idx_audit_contract (contract_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id VARCHAR(100) NOT NULL,
    contract_id INT UNSIGNED NOT NULL,
    type VARCHAR(100) NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notification_contract
        FOREIGN KEY (contract_id) REFERENCES contracts(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_notification_client (client_id),
    INDEX idx_notification_contract (contract_id)
) ENGINE=InnoDB;

-- Demo contract for local testing.
-- Token is intentionally a test value. Replace/delete it for real use.
INSERT INTO contracts (client_id, token_hash, status)
VALUES (
    'CLIENT-1001',
    SHA2('demo-contract-token-2026-01', 256),
    'Sent'
)
ON DUPLICATE KEY UPDATE client_id = VALUES(client_id);
