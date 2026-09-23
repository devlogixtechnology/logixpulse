-- Reference only: structure assumed for the existing `users` table.
-- Passwords must be stored as hashes created with PHP's password_hash().

CREATE TABLE IF NOT EXISTS users (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(100)  NOT NULL,
    email    VARCHAR(150)  NOT NULL UNIQUE,
    password VARCHAR(255)  NOT NULL, -- store output of password_hash()
    role     VARCHAR(50)   NOT NULL DEFAULT 'user'
);

-- Example of inserting a test user with a securely hashed password:
-- INSERT INTO users (name, email, password, role)
-- VALUES ('Test User', 'test@example.com', '$2y$10$exampleHashGeneratedByPasswordHash', 'admin');
