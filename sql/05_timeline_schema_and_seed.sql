-- ============================================
-- LogixPulse — Step 5: Timeline Tables + Seed
-- Task: FEB-W7D3-1 - Connect the Timeline to Real Data
-- ============================================
-- Adds two new tables to drive the "Project Timeline" dashboard card
-- from real data instead of hardcoded steps. Does not modify any
-- existing table (users, invoices, projects, etc.).

USE logix_pulse;

-- Holds each client's current phase (client_id = users.id, role='client')
CREATE TABLE IF NOT EXISTS client_timeline_progress (
    client_id           INT NOT NULL PRIMARY KEY,
    current_step_order  INT NOT NULL DEFAULT 1,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Holds each client's timeline steps (title + date label per phase)
CREATE TABLE IF NOT EXISTS timeline_steps (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    client_id   INT NOT NULL,
    step_order  INT NOT NULL,
    title       VARCHAR(150) NOT NULL,
    date_label  VARCHAR(50) NOT NULL,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_timeline_steps_client_id ON timeline_steps(client_id);

-- ---- Seed: matches the 5 client users from 03_seed_data.sql ----
-- client1@acmecorp.com    -> id 10
-- client2@nova.com        -> id 11
-- client3@bluewave.io     -> id 12
-- client4@steelvent.com   -> id 13
-- client5@brightcore.com  -> id 14

INSERT INTO client_timeline_progress (client_id, current_step_order) VALUES
    (10, 3),
    (11, 2),
    (12, 4),
    (13, 1),
    (14, 5);

INSERT INTO timeline_steps (client_id, step_order, title, date_label) VALUES
    (10, 1, 'Project Started', 'Jan 6'),
    (10, 2, 'Requirements & Planning', 'Jan 20'),
    (10, 3, 'Development', 'Est. Feb 28'),
    (10, 4, 'Review & Approval', 'Est. Mar 10'),
    (10, 5, 'Delivery', 'Est. Mar 20'),

    (11, 1, 'Project Started', 'Feb 2'),
    (11, 2, 'Requirements & Planning', 'Est. Feb 24'),
    (11, 3, 'Development', 'Est. Mar 15'),
    (11, 4, 'Review & Approval', 'Est. Mar 28'),
    (11, 5, 'Delivery', 'Est. Apr 5'),

    (12, 1, 'Project Started', 'Jan 12'),
    (12, 2, 'Requirements & Planning', 'Jan 26'),
    (12, 3, 'Development', 'Feb 20'),
    (12, 4, 'Review & Approval', 'Est. Mar 5'),
    (12, 5, 'Delivery', 'Est. Mar 15'),

    (13, 1, 'Project Started', 'Est. Sep 20'),
    (13, 2, 'Requirements & Planning', 'Est. Oct 1'),
    (13, 3, 'Development', 'Est. Oct 20'),
    (13, 4, 'Review & Approval', 'Est. Nov 5'),
    (13, 5, 'Delivery', 'Est. Nov 15'),

    (14, 1, 'Project Started', 'Jun 3'),
    (14, 2, 'Requirements & Planning', 'Jun 18'),
    (14, 3, 'Development', 'Jul 22'),
    (14, 4, 'Review & Approval', 'Aug 10'),
    (14, 5, 'Delivery', 'Aug 25');

SELECT '✅ Timeline tables created and seeded.' AS Status;
