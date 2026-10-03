-- Task: FEB-W7D3-1 - Connect the Timeline to Real Data
-- Two clients on two different phases, to verify the "done when" criteria:
-- different accounts must show different current phases from the DB.

INSERT INTO clients (id, name, email, current_step_order) VALUES
    (1, 'Acme Cloud Technologies', 'contact@acmecloud.io', 3),
    (2, 'Vertex FinTech Core', 'contact@vertexfintech.com', 2);

INSERT INTO timeline_steps (client_id, step_order, title, date_label) VALUES
    (1, 1, 'Project Started', 'Jan 6'),
    (1, 2, 'Requirements & Planning', 'Jan 20'),
    (1, 3, 'Development', 'Est. Feb 28'),
    (1, 4, 'Review & Approval', 'Est. Mar 10'),
    (1, 5, 'Delivery', 'Est. Mar 20'),

    (2, 1, 'Project Started', 'Feb 2'),
    (2, 2, 'Requirements & Planning', 'Est. Feb 24'),
    (2, 3, 'Development', 'Est. Mar 15'),
    (2, 4, 'Review & Approval', 'Est. Mar 28'),
    (2, 5, 'Delivery', 'Est. Apr 5');
