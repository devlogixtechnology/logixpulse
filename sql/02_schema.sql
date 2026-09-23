-- ============================================
-- LogixPulse — Step 2: Schema (8 Tables) - MySQL Compatible
-- ============================================

-- 1. users
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    email         VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name    VARCHAR(100) NOT NULL,
    last_name     VARCHAR(100) NOT NULL,
    role          VARCHAR(50)  NOT NULL DEFAULT 'client',
    status        VARCHAR(50)  NOT NULL DEFAULT 'active',
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 2. leads
CREATE TABLE leads (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    first_name    VARCHAR(100),
    last_name     VARCHAR(100),
    email         VARCHAR(255),
    phone         VARCHAR(50),
    company       VARCHAR(255),
    source        VARCHAR(100),
    status        VARCHAR(50)  NOT NULL DEFAULT 'new',
    assigned_to   INT,
    notes         TEXT,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

-- 3. activity_logs
CREATE TABLE activity_logs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT,
    action        VARCHAR(255) NOT NULL,
    entity_type   VARCHAR(100),
    entity_id     INT,
    details       JSON,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 4. projects
CREATE TABLE projects (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(255) NOT NULL,
    client_id     INT          NOT NULL,
    status        VARCHAR(50)  NOT NULL DEFAULT 'active',
    start_date    DATE,
    end_date      DATE,
    description   TEXT,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id)
);

-- 5. invoices
CREATE TABLE invoices (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number  VARCHAR(50)  NOT NULL UNIQUE,
    client_id       INT          NOT NULL,
    project_id      INT,
    amount          DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    status          VARCHAR(50)  NOT NULL DEFAULT 'draft',
    issue_date      DATE,
    due_date        DATE,
    paid_date       DATE,
    notes           TEXT,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (project_id) REFERENCES projects(id)
);

-- 6. agreements
CREATE TABLE agreements (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    client_id     INT          NOT NULL,
    project_id    INT,
    title         VARCHAR(255) NOT NULL,
    content       TEXT,
    status        VARCHAR(50)  NOT NULL DEFAULT 'draft',
    signed_date   DATE,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (project_id) REFERENCES projects(id)
);

-- 7. magic_codes
CREATE TABLE magic_codes (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT,
    code          VARCHAR(6)   NOT NULL,
    expires_at    TIMESTAMP    NOT NULL,
    used_at       TIMESTAMP NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 8. password_resets
CREATE TABLE password_resets (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT,
    token         VARCHAR(255) NOT NULL UNIQUE,
    expires_at    TIMESTAMP    NOT NULL,
    used_at       TIMESTAMP NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Indexes for performance
CREATE INDEX idx_invoices_client_id    ON invoices(client_id);
CREATE INDEX idx_invoices_status       ON invoices(status);
CREATE INDEX idx_projects_client_id    ON projects(client_id);
CREATE INDEX idx_activity_logs_user_id ON activity_logs(user_id);
CREATE INDEX idx_leads_assigned_to     ON leads(assigned_to);

SELECT '✅  All 8 tables and indexes created.' AS Status;