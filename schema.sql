CREATE DATABASE IF NOT EXISTS crm_pipeline
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE crm_pipeline;

DROP TRIGGER IF EXISTS prevent_lead_activity_update;
DROP TRIGGER IF EXISTS prevent_lead_activity_delete;

CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NULL,
    company VARCHAR(190) NULL,
    stage ENUM(
        'New Lead',
        'Contacted',
        'Qualified',
        'Proposal Sent',
        'Won',
        'Lost'
    ) NOT NULL DEFAULT 'New Lead',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_leads_stage (stage),
    INDEX idx_leads_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS lead_activity_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    old_stage ENUM(
        'New Lead',
        'Contacted',
        'Qualified',
        'Proposal Sent',
        'Won',
        'Lost'
    ) NULL,
    new_stage ENUM(
        'New Lead',
        'Contacted',
        'Qualified',
        'Proposal Sent',
        'Won',
        'Lost'
    ) NOT NULL,
    transition_metadata JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_lead_activity_lead
        FOREIGN KEY (lead_id) REFERENCES leads(id)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,

    INDEX idx_activity_lead_created (lead_id, created_at, id),
    INDEX idx_activity_user (user_id)
) ENGINE=InnoDB;

DELIMITER $$

CREATE TRIGGER prevent_lead_activity_update
BEFORE UPDATE ON lead_activity_log
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'lead_activity_log records are immutable and cannot be updated';
END$$

CREATE TRIGGER prevent_lead_activity_delete
BEFORE DELETE ON lead_activity_log
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'lead_activity_log records are immutable and cannot be deleted';
END$$

DELIMITER ;

-- Demo lead for local verification.
INSERT INTO leads (name, email, company, stage)
VALUES ('Demo Lead', 'demo@example.com', 'Demo Company', 'New Lead');

-- Initial stage entry. This gives the first stage a start timestamp,
-- allowing time-spent calculation for New Lead.
INSERT INTO lead_activity_log
    (lead_id, user_id, old_stage, new_stage, transition_metadata)
VALUES
    (
        LAST_INSERT_ID(),
        1,
        NULL,
        'New Lead',
        JSON_OBJECT(
            'event', 'lead_created',
            'source', 'schema_demo'
        )
    );
