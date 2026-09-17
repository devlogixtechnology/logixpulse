-- LogixPulse / FEA-W7D4-2 ke liye database schema
-- Ye table columns (stages) aur tasks (cards) ko store karti hai

CREATE TABLE IF NOT EXISTS columns_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,      -- e.g. "Backlog", "In Progress", "Done"
    position INT NOT NULL DEFAULT 0  -- column ka left-to-right order
);

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    column_id INT NOT NULL,          -- ye field drag-drop pe update hoga
    position INT NOT NULL DEFAULT 0, -- card ka order us column ke andar
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (column_id) REFERENCES columns_table(id) ON DELETE CASCADE
);

-- Sample data (test karne ke liye)
INSERT INTO columns_table (name, position) VALUES
    ('Backlog', 1), ('In Progress', 2), ('Done', 3);

INSERT INTO tasks (title, column_id, position) VALUES
    ('FEA-W7D4-2 — Save the Drag into the Database', 1, 1),
    ('Design login screen', 2, 1),
    ('Setup MySQL connection', 3, 1);
