CREATE TABLE IF NOT EXISTS users (
    id BIGSERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role VARCHAR(30) NOT NULL,
    user_type VARCHAR(20) NOT NULL CHECK (user_type IN ('staff', 'client')),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS auth_audit_log (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(100) NOT NULL,
    old_role VARCHAR(30),
    new_role VARCHAR(30),
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_auth_audit_user_id ON auth_audit_log(user_id);

-- Demo hashes for Password123!:
-- staff:  $2y$12$KWPTNIh6t65.hmh8yJo4OOoyxrcyaeyD1Wtx9oDrVi0G.tZB666gK
-- client: $2y$12$KWPTNIh6t65.hmh8yJo4OOoyxrcyaeyD1Wtx9oDrVi0G.tZB666gK

INSERT INTO users (email, password_hash, role, user_type)
VALUES
('staff@example.com', '$2y$12$KWPTNIh6t65.hmh8yJo4OOoyxrcyaeyD1Wtx9oDrVi0G.tZB666gK', 'staff', 'staff'),
('client@example.com', '$2y$12$KWPTNIh6t65.hmh8yJo4OOoyxrcyaeyD1Wtx9oDrVi0G.tZB666gK', 'client', 'client')
ON CONFLICT (email) DO NOTHING;
