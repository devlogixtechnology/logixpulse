CREATE DATABASE IF NOT EXISTS logixpulse_auth
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE logixpulse_auth;

DROP TABLE IF EXISTS internal_users;
DROP TABLE IF EXISTS clients;

CREATE TABLE internal_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE clients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'client',
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Demo password for internal user: Admin@123
INSERT INTO internal_users (name, email, password, role)
VALUES (
    'Admin User',
    'admin@logixpulse.test',
    '$2y$12$BiMOP34TRyCzcYKekoS4vOVIp5GHANli5qkAe7RaxIBouVF.wFwaq',
    'admin'
);

-- Demo password for client: Client@123
INSERT INTO clients (name, email, password, role)
VALUES (
    'Demo Client',
    'client@logixpulse.test',
    '$2y$12$yVPF3xit1YvQwTXfwlrrBuxx8qgJcQKAjEuUngzcVZfXMYIlCydR.',
    'client'
);
