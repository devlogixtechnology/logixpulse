CREATE DATABASE IF NOT EXISTS invoice_portal
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE invoice_portal;

DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','client') NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(100) NOT NULL UNIQUE,
    amount DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_invoices_client
        FOREIGN KEY (client_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    INDEX idx_invoices_client_id (client_id),
    INDEX idx_invoices_created_at (created_at)
) ENGINE=InnoDB;

-- Demo accounts for testing.
-- Passwords:
-- admin123, client123, client456
INSERT INTO users (name, email, password_hash, role) VALUES
('System Admin', 'admin@example.com', '$2y$12$X6DR8L0Dfm5oPo2HXnfFFO5GRTHYv8mQ4WPNkpo26k8dAU9XJ.bV6', 'admin'),
('Client A', 'clienta@example.com', '$2y$12$i4HX.ky7Yt3Q8LihkVIjr.Q7AerVHMlblZNtBxNrbVJwZ1z4iQmbG', 'client'),
('Client B', 'clientb@example.com', '$2y$12$dNEHtfnPr3pWAS6fpKXIE.YMqnb8dmhBGpeix6sGpx6O4qC/YATQG', 'client');
