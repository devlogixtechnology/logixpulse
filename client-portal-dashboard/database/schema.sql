-- Task: FEB-W7D3-1 - Connect the Timeline to Real Data
-- Minimal schema needed to drive the Project Timeline card from real data.

CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    current_step_order INT NOT NULL DEFAULT 1, -- which timeline step this client is currently on
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS timeline_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    step_order INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    date_label VARCHAR(50) NOT NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);
