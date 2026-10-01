-- ============================================
-- LogixPulse — Step 6: Milestone Feed Tables + Seed
-- Task: REC-FEB-03 - Connect Real-Time Client Timeline & Milestone Feed
-- ============================================
-- Adds two new tables that drive the milestone feed under the
-- "Project Timeline" card (api/client_timeline.php).
-- Does not modify any existing table (users, projects, timeline_steps, etc.).
--
-- Status values stored in project_milestones.status:
--   'completed' | 'in_progress' | 'upcoming'
-- (the API also tolerates variants such as 'In Progress' or 'pending').

USE logix_pulse;

-- One row per milestone / deliverable for a client's project
CREATE TABLE IF NOT EXISTS project_milestones (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    client_id     INT          NOT NULL,          -- users.id (role = 'client')
    project_id    INT          NULL,              -- optional link to projects.id
    title         VARCHAR(200) NOT NULL,
    status        VARCHAR(30)  NOT NULL DEFAULT 'upcoming',
    notes         TEXT         NULL,              -- deliverable notes / details
    started_at    DATETIME     NULL,              -- set when work begins
    completed_at  DATETIME     NULL,              -- set when the milestone is done
    due_date      DATE         NULL,              -- planned date for upcoming work
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id)  REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
);

-- Files attached to a milestone (name + size only, same as "Recent Documents")
CREATE TABLE IF NOT EXISTS milestone_attachments (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    milestone_id  INT          NOT NULL,
    file_name     VARCHAR(255) NOT NULL,
    file_size     INT UNSIGNED NULL,              -- size in bytes
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (milestone_id) REFERENCES project_milestones(id) ON DELETE CASCADE
);

CREATE INDEX idx_project_milestones_client_id  ON project_milestones(client_id);
CREATE INDEX idx_milestone_attachments_milestone_id ON milestone_attachments(milestone_id);

-- ---- Development seed (clients from 03_seed_data.sql) ----
-- client 10 (client1@acmecorp.com)  -> full timeline: completed / in progress / upcoming
-- client 11 (client2@nova.com)      -> short timeline
-- client 12 (client3@bluewave.io)   -> mostly completed
-- clients 13 and 14 intentionally have NO milestones (empty state)

INSERT INTO project_milestones (id, client_id, title, status, notes, started_at, completed_at, due_date) VALUES
    (1,  10, 'Project Kickoff',         'completed',   'Kickoff call held with the full team. Scope, timeline and communication plan agreed.', '2026-08-03 09:00:00', '2026-08-03 10:15:00', NULL),
    (2,  10, 'Requirements & Planning', 'completed',   'Requirements document signed off and project plan published.',                          '2026-08-04 09:00:00', '2026-08-19 16:40:00', NULL),
    (3,  10, 'UI/UX Design Approval',   'completed',   NULL,                                                                                    '2026-08-20 09:00:00', '2026-09-08 11:05:00', NULL),
    (4,  10, 'Development Sprint 1',    'in_progress', 'Core portal screens and login flow are being built. First preview build expected soon.', '2026-09-15 09:00:00', NULL,                  '2026-10-10'),
    (5,  10, 'Review & Approval',       'upcoming',    NULL,                                                                                    NULL,                  NULL,                  '2026-10-25'),
    (6,  10, 'Delivery',                'upcoming',    NULL,                                                                                    NULL,                  NULL,                  '2026-11-05'),

    (7,  11, 'Project Kickoff',         'completed',   'Welcome pack shared and kickoff meeting completed.',                                    '2026-09-02 10:00:00', '2026-09-02 11:00:00', NULL),
    (8,  11, 'Requirements & Planning', 'in_progress', NULL,                                                                                    '2026-09-22 09:00:00', NULL,                  '2026-10-06'),
    (9,  11, 'Development',             'upcoming',    NULL,                                                                                    NULL,                  NULL,                  '2026-10-30'),

    (10, 12, 'Project Kickoff',         'completed',   NULL,                                                                                    '2026-07-12 09:00:00', '2026-07-12 09:45:00', NULL),
    (11, 12, 'Requirements & Planning', 'completed',   'Approved requirements are attached.',                                                   '2026-07-13 09:00:00', '2026-07-26 15:20:00', NULL),
    (12, 12, 'Development',             'completed',   'All planned features delivered to the staging environment.',                            '2026-07-27 09:00:00', '2026-09-20 17:30:00', NULL),
    (13, 12, 'Review & Approval',       'in_progress', 'Awaiting your review of the staging build.',                                            '2026-09-21 09:00:00', NULL,                  '2026-10-05');

INSERT INTO milestone_attachments (milestone_id, file_name, file_size) VALUES
    (1,  'Kickoff Meeting Notes.pdf',        184320),
    (2,  'Requirements Document v1.2.pdf',  2516582),
    (2,  'Project Plan.xlsx',                 481280),
    (4,  'Sprint 1 Scope.pdf',                 96256),
    (7,  'Welcome Pack.pdf',                 1258291),
    (11, 'Approved Requirements.pdf',        2202009);

SELECT '✅ Milestone tables created and seeded.' AS Status;
