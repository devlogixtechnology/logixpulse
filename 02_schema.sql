-- LOGIX PULSE DATABASE
-- Run this file after connecting to the logix_pulse database.

CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- Optional cleanup for re-running this script during development
DROP TABLE IF EXISTS password_resets CASCADE;
DROP TABLE IF EXISTS magic_codes CASCADE;
DROP TABLE IF EXISTS agreements CASCADE;
DROP TABLE IF EXISTS invoices CASCADE;
DROP TABLE IF EXISTS projects CASCADE;
DROP TABLE IF EXISTS activity_logs CASCADE;
DROP TABLE IF EXISTS leads CASCADE;
DROP TABLE IF EXISTS users CASCADE;

CREATE TABLE users (
    id              BIGSERIAL PRIMARY KEY,
    full_name       VARCHAR(150) NOT NULL,
    email           VARCHAR(180) NOT NULL UNIQUE,
    password_hash   TEXT NOT NULL,
    role            VARCHAR(40) NOT NULL CHECK (
        role IN (
            'system_administrator',
            'executive_member',
            'administrator',
            'vp_manager_head',
            'team_user',
            'client_user'
        )
    ),
    phone           VARCHAR(30),
    is_active       BOOLEAN NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE leads (
    id              BIGSERIAL PRIMARY KEY,
    assigned_to     BIGINT REFERENCES users(id) ON DELETE SET NULL,
    first_name      VARCHAR(80) NOT NULL,
    last_name       VARCHAR(80),
    company_name    VARCHAR(180),
    email           VARCHAR(180),
    phone           VARCHAR(30),
    source          VARCHAR(60),
    status          VARCHAR(30) NOT NULL DEFAULT 'new'
                    CHECK (status IN ('new','contacted','qualified','proposal','won','lost')),
    estimated_value NUMERIC(12,2) DEFAULT 0 CHECK (estimated_value >= 0),
    notes           TEXT,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE activity_logs (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE SET NULL,
    lead_id         BIGINT REFERENCES leads(id) ON DELETE CASCADE,
    action          VARCHAR(100) NOT NULL,
    description     TEXT,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE projects (
    id              BIGSERIAL PRIMARY KEY,
    client_id       BIGINT REFERENCES users(id) ON DELETE SET NULL,
    manager_id      BIGINT REFERENCES users(id) ON DELETE SET NULL,
    project_name    VARCHAR(180) NOT NULL,
    description     TEXT,
    status          VARCHAR(30) NOT NULL DEFAULT 'planned'
                    CHECK (status IN ('planned','active','on_hold','completed','cancelled')),
    start_date      DATE,
    due_date        DATE,
    budget          NUMERIC(12,2) DEFAULT 0 CHECK (budget >= 0),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE invoices (
    id              BIGSERIAL PRIMARY KEY,
    project_id      BIGINT REFERENCES projects(id) ON DELETE SET NULL,
    client_id       BIGINT REFERENCES users(id) ON DELETE SET NULL,
    invoice_number  VARCHAR(50) NOT NULL UNIQUE,
    amount          NUMERIC(12,2) NOT NULL CHECK (amount >= 0),
    status          VARCHAR(30) NOT NULL DEFAULT 'unpaid'
                    CHECK (status IN ('draft','unpaid','paid','overdue','cancelled')),
    due_date        DATE,
    paid_at         TIMESTAMPTZ,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE agreements (
    id              BIGSERIAL PRIMARY KEY,
    project_id      BIGINT REFERENCES projects(id) ON DELETE CASCADE,
    client_id       BIGINT REFERENCES users(id) ON DELETE SET NULL,
    agreement_title VARCHAR(180) NOT NULL,
    agreement_text  TEXT,
    status          VARCHAR(30) NOT NULL DEFAULT 'draft'
                    CHECK (status IN ('draft','sent','signed','expired','cancelled')),
    signed_at       TIMESTAMPTZ,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE magic_codes (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    code            VARCHAR(20) NOT NULL,
    purpose         VARCHAR(40) NOT NULL DEFAULT 'login',
    expires_at      TIMESTAMPTZ NOT NULL,
    used_at         TIMESTAMPTZ,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE password_resets (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    reset_token     VARCHAR(255) NOT NULL UNIQUE,
    expires_at      TIMESTAMPTZ NOT NULL,
    used_at         TIMESTAMPTZ,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_leads_status ON leads(status);
CREATE INDEX idx_leads_assigned_to ON leads(assigned_to);
CREATE INDEX idx_activity_logs_lead_id ON activity_logs(lead_id);
CREATE INDEX idx_projects_client_id ON projects(client_id);
CREATE INDEX idx_invoices_client_id ON invoices(client_id);
CREATE INDEX idx_magic_codes_user_id ON magic_codes(user_id);
CREATE INDEX idx_password_resets_user_id ON password_resets(user_id);
