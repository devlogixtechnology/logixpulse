# BE-W7D4-1 — Save Lead Stage Changes

Updates a lead stage and creates a matching activity log whenever the stage changes.

Endpoint:
POST api/update_lead_stage.php

JSON:
{"lead_id":1,"new_stage":"Meeting Booked"}

Expected log:
Moved from Contacted to Meeting Booked.
