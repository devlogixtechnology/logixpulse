# BE-02 — Build Lead Stage Progression Engine & Audit Activity Logging

Core PHP + MySQL/XAMPP implementation for:

- New Lead → Contacted → Qualified → Proposal Sent → Won/Lost
- Strict validation of legal stage transitions
- Rejection of illegal status skips
- Automatic immutable audit records in `lead_activity_log`
- User ID, timestamp, old stage, new stage and transition metadata
- Time spent in each stage
- Chronological activity-log API
- Verification scripts for normal and illegal transitions

## Requirements

- XAMPP (Apache + MySQL)
- PHP 8.0+ recommended
- MySQL 5.7+/8.x

## Folder structure

```text
BE-02_lead_stage_progression_audit/
├── api/
│   ├── lead_stage.php
│   └── lead_activities.php
├── config/
│   └── db.php
├── helpers/
│   └── response.php
├── middleware/
│   └── auth.php
├── services/
│   └── LeadStageService.php
├── sql/
│   └── schema.sql
├── tests/
│   └── manual_test.php
└── README.md
```

## Setup in XAMPP

1. Copy the folder into:
   `C:\xampp\htdocs\BE-02_lead_stage_progression_audit`

2. Start **Apache** and **MySQL** in XAMPP.

3. Open phpMyAdmin:
   `http://localhost/phpmyadmin`

4. Import:
   `sql/schema.sql`

5. Open the test page:
   `http://localhost/BE-02_lead_stage_progression_audit/tests/manual_test.php`

The test page creates a demo lead and provides links to perform valid transitions and an intentionally invalid transition.

## Authentication

The middleware reads the logged-in user ID from the PHP session:

```php
$_SESSION['user_id']
```

For local verification, `tests/manual_test.php` sets a demo session user ID of `1`.

In the real CRM, the existing login/session system should set `$_SESSION['user_id']` after authentication.

## API endpoints

### Transition a lead

**POST**
`/api/lead_stage.php`

JSON body:

```json
{
  "lead_id": 1,
  "new_stage": "Contacted",
  "metadata": {
    "source": "crm_ui",
    "note": "Initial call completed"
  }
}
```

Legal sequence:

```text
New Lead
   ↓
Contacted
   ↓
Qualified
   ↓
Proposal Sent
   ↓
Won / Lost
```

Won and Lost are terminal stages. An illegal jump such as `New Lead -> Qualified` returns HTTP 422.

### Get chronological activity logs

**GET**
`/api/lead_activities.php?lead_id=1`

The response includes:

- activity ID
- lead ID
- user ID
- timestamp
- old stage
- new stage
- transition metadata
- time spent in the stage in seconds
- human-readable duration

For the current stage, time spent is calculated from the latest stage-change timestamp until the current request time.

## Verification

The implementation includes:

- legal transition validation
- illegal transition rejection
- database persistence of audit records
- chronological log retrieval
- stage-duration calculation

Use `tests/manual_test.php` after importing the schema. The page also shows the current activity log.

## Important database protection

`lead_activity_log` is protected with MySQL triggers that reject UPDATE and DELETE operations. This supports the requirement that audit records are immutable.

