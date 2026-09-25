# LogixPulse &mdash; Software Requirements Specification (SRS)
## Module 1: Enterprise CRM & Client Onboarding
**Document ID:** `LP-SRS-MOD1-2026-V2.1`  
**Standard:** IEEE Std 830-1998 & ISO/IEC/IEEE 29148:2018 Compliant  
**Version:** 2.1 (Consolidated & Security Hardened)  
**Date:** September 25, 2026  
**Compiled PDF Location:** [LogixPulse_Module1_SRS.pdf](file:///d:/project/logixpulse/LogixPulse_Module1_SRS.pdf)  
**Mirrored PDF Location:** `D:\Documents\SIP'26\04- Engineering Track\03- PHP Practice\LogixPulse_Module1_SRS.pdf`

---

## Executive Summary

The **LogixPulse Platform** is an enterprise-grade, dual-portal web application architected to bridge internal business development operations and external client onboarding workflows into a cohesive, secure ecosystem. Developed during Sprint 1 (Weeks 7 and 8) of the DevLogix Strategic Internship Program (SIP'26) Engineering Track (PHP Practice), Module 1 establishes the operational core of the platform: **CRM & Client Onboarding**.

### Dual-Portal Architectural Paradigm
1. **Internal CRM & Executive Portal (FE-A)**:
   - 4-column visual Kanban board with native HTML5 drag-and-drop.
   - Debounced multi-field search (Name, Email, Phone, Company).
   - Offcanvas Lead Details sliding drawer with full audit trail.
   - Executive Dashboard KPI stat cards (Total Leads, Active Deals, Meetings This Week, Closed Deals MTD with period-over-period % delta).
   - Pipeline Overview & Velocity analytics with deal conversion rates and duration metrics.
   - System-wide live activity audit feed with real-time indicators.
   - Sales Team Leaderboard with quota attainment badges and territory filtering.
2. **Unified Core Engine (BE)**:
   - PHP 8.0+ native session router (`index.php`) and access sentinel (`backend/auth.php`).
   - Centralized PDO singleton (`config/database.php`) with `.env` configuration.
   - 13 unified relational tables supporting PostgreSQL and MySQL/MariaDB.
   - Transactional persistence and Bcrypt cryptographic hashing (`password_hash()`, `password_verify()`).
   - Storage hardening with `.htaccess` script execution prevention.
3. **External Client Onboarding Portal (FE-B)**:
   - Slack-style passwordless 6-digit OTP authentication (10-minute expiry, rate-limited attempts).
   - Cryptographic token self-service password reset flow.
   - Dynamic onboarding milestone timeline (`client_timeline_progress`, `timeline_steps`).
   - Master Service Agreement (MSA) with HTML5 Canvas digital signature pad (Base64 PNG ingestion).
   - Client-isolated secure invoice download streaming (`backend/download.php`).

---

## 1. Introduction & Project Scope

### 1.1 Purpose
This Software Requirements Specification (SRS) defines the functional, interface, behavioral, data, and quality requirements for LogixPulse Module 1. It serves as the formal baseline for developers, QA engineers, and management.

### 1.2 RFC 2119 Conventions
- **MUST / SHALL / REQUIRED**: Mandatory requirement. Non-compliance blocks release.
- **SHOULD / RECOMMENDED**: Strong best practice.
- **MAY / OPTIONAL**: Discretionary feature.

### 1.3 Intended Audience
- **Backend Engineers (Squad BE)**: API schemas, PDO prepared statements, database schemas, RBAC session validation, storage isolation.
- **Frontend Engineers (Squads FE-A / FE-B)**: UI components, drag-and-drop, Canvas signature rendering, fetch integration, error handling.
- **QA & Squad Leads**: Daily acceptance criteria, cross-squad testing matrices, demo exit gates, defect logging.
- **Practice Management & Head**: Delivery compliance, security posture, governance rules, sprint exit criteria.

---

## 2. Master RBAC Permissions Matrix

| Feature / Action | Admin (`admin`) | Executive (`executive`) | BD Head (`head`) | BD Rep (`team`) | Client (`client`) |
| :--- | :---: | :---: | :---: | :---: | :---: |
| Access CRM Board | Allowed | Read Only | Allowed | Allowed | **Blocked (403)** |
| Create Lead (`save_lead.php`) | Allowed | Denied | Allowed | Allowed | **Blocked (403)** |
| Move Lead Stage (`update_lead_stage.php`) | Allowed | Denied | Allowed | Allowed | **Blocked (403)** |
| Add Note (`add_note.php`) | Allowed | Denied | Allowed | Allowed | **Blocked (403)** |
| Delete Lead (`leads_delete.php`) | **Allowed** | Denied | Denied | Denied | **Blocked (403)** |
| Executive KPIs & Velocity | Allowed | Allowed | Allowed | Allowed | **Blocked (403)** |
| Upload Invoice (`upload.php`) | **Allowed** | Denied | Allowed | Denied | **Blocked (403)** |
| Download Invoice (`download.php`) | All Invoices | Denied | All Invoices | Denied | **Own Invoices Only** |
| View Client Timeline | Read All | Denied | Read All | Read All | **Own Timeline Only** |
| Sign Agreement (Canvas) | Denied | Denied | Denied | Denied | **Allowed** |

---

## 3. Subsystem Functional Requirements

### 3.1 Subsystem 1: Dual-Portal Authentication & Access Governance
- **`FR-AUTH-01` Traditional Credential Authentication**: Validates email and password against Bcrypt hashes stored in `users`. Regenerates session (`session_regenerate_id(true)`), stores user ID, email, and role in `$_SESSION`.
- **`FR-AUTH-02` Passwordless Magic Code (OTP)**: Generates a 6-digit random numeric code via `random_int(100000, 999999)` with 10-minute expiration. Throttles after 5 failed attempts; single-use only.
- **`FR-AUTH-03` Self-Service Password Reset**: Generates a 64-character random hex token (`bin2hex(random_bytes(32))`), stores SHA-256 hash in `password_resets` with 1-hour expiration. Updates password using Bcrypt.
- **`FR-AUTH-04` Session Identity Verification & Router**: `index.php` routes unauthenticated users to `index.html`, clients to `client-portal-dashboard/index.php`, and staff/admin to `backend/main_dashboard.php` or `kanban.php`. `who_is_logged_in.php` returns active session details.

### 3.2 Subsystem 2: Internal CRM Pipeline & Kanban Management
- **`FR-CRM-01` 4-Column Visual Kanban Board**: Renders 4 columns (**New**, **Contacted**, **Meeting Booked**, **Closed**). Populated via `LeadRepository::getGroupedLeads()`.
- **`FR-CRM-02` Interactive Drag-and-Drop Stage Transitions**: HTML5 Drag and Drop API with immediate visual feedback and rollback on network failure.
- **`FR-CRM-03` Atomic Stage Update & Transactional Logging**: `update_lead_stage.php` wraps stage update and `activity_logs` insertion inside an atomic PDO transaction.
- **`FR-CRM-04` Add Lead Modal & Dual Validation**: Captures Name, Email, Phone, Source. Enforces server-side validation (`filter_var($email, FILTER_VALIDATE_EMAIL)`). Dynamically prepends card to New column.
- **`FR-CRM-05` Debounced Multi-Column Lead Search**: Debounces input by 300ms, queries `lead_search.php` across name, email, phone, and company using parameterized SQL.
- **`FR-CRM-06` Lead Details Offcanvas Drawer**: Slides open upon card click, displaying lead profile metadata and reverse-chronological audit trail.
- **`FR-CRM-07` Admin Lead Deletion with Confirmation**: Enforces admin-only access and requires confirmation before deleting a lead.

### 3.3 Subsystem 3: Activity Logging & Historical Audit Trail
- **`FR-LOG-01` Manual Free-Text Note Authoring**: BD reps capture qualitative notes via `add_note.php`. Stored in `activity_logs` with author and timestamp.
- **`FR-LOG-02` Chronological Lead Activity Stream**: `lead_activity.php` returns lead history ordered descending (`ORDER BY created_at DESC, id DESC`).
- **`FR-LOG-03` System-Wide Live Recent Activity Feed**: `activity_feed.php` streams cross-entity activity across leads, agreements, and invoices with a pulsating "Live" indicator.

### 3.4 Subsystem 4: Executive Business Intelligence & Analytics
- **`FR-KPI-01` Executive KPI Stat Cards**: `dashboard_kpi.php` computes Total Leads, Active Deals, Meetings This Week, and Closed Deals MTD, including period-over-period percentage delta.
- **`FR-KPI-02` Pipeline Overview & Velocity Analytics**: `pipeline_velocity.php` returns per-stage counts, dollar valuation, conversion rates, average deal velocity in days, and top acquisition source. Includes Value/Volume toggle.
- **`FR-KPI-03` Team Leaderboard & Quota Engine**: `team_leaderboard.php` tracks active leads, meetings held, deals won, total revenue, quota attainment badges (<70% red, 70-99% amber, &ge;100% green), and territory filtering.
- **`FR-KPI-04` Executive Report Export Engine**: Generates sanitized print view and data export of current dashboard metrics.

### 3.5 Subsystem 5: External Client Portal & Onboarding Progression
- **`FR-CLT-01` Interactive Onboarding Milestone Timeline**: `getClientTimeline($clientId)` calculates dynamic status (`completed`, `current`, `upcoming`) based on `client_timeline_progress` and `timeline_steps`. Strict tenant isolation enforced.
- **`FR-CLT-02` Engagement Overview & Active Projects**: Displays client's active projects, deliverables, and account manager contacts from `projects`.

### 3.6 Subsystem 6: Digital Master Agreement & E-Signature Engine
- **`FR-AGR-01` Master Service Agreement (MSA) Legal Shell**: Displays terms and conditions; displays signature image and timestamp when signed.
- **`FR-AGR-02` HTML5 Canvas Digital Signature Pad**: Smooth curve rendering using mouse, pen, or touch input (`lineCap = "round"`, `lineWidth = 2.5`). Clear button resets canvas.
- **`FR-AGR-03` Signature Ingestion & File Persistence**: Serializes canvas to Base64 PNG, decodes via `base64_decode()`, writes to `uploads/signatures/` with randomized unique filename, updates `agreements.status = 'signed'`, and logs audit event.

### 3.7 Subsystem 7: Secure Invoicing & Document Repository
- **`FR-INV-01` Admin Protected Invoice Upload**: `upload.php` enforces admin role, PDF extension whitelist, `finfo_file()` MIME validation (`application/pdf`), 10MB ceiling, and randomized UUID filenames.
- **`FR-INV-02` Client-Isolated Streaming Download**: `download.php` strictly verifies `invoice.client_id == $_SESSION['user_id']`. Rejects unauthorized cross-tenant requests with HTTP 403 Forbidden.
- **`FR-INV-03` Client Invoice Listing**: Lists invoices with amount, issue date, due date, status badge, and download action. Handles empty state cleanly.
- **`FR-INV-04` Storage Vault Hardening**: `.htaccess` in `uploads/` denies execution of PHP, CGI, and shell scripts.

---

## 4. Complete 13-Table Database Schema

1. **`users`**: Master user identity, role, and Bcrypt password hashes.
2. **`leads`**: CRM pipeline prospects with dual-schema columns (`status` and `stage`).
3. **`activity_logs`**: Comprehensive audit log repository for stage moves, manual notes, and admin actions.
4. **`projects`**: Commercial project engagements linked to client accounts.
5. **`invoices`**: Billing documents with original and randomized safe filenames.
6. **`agreements`**: Master Service Agreements with status and signing timestamps.
7. **`magic_codes`**: 6-digit passwordless OTP tokens with expiration and attempt counters.
8. **`password_resets`**: 64-character SHA-256 hashed recovery tokens.
9. **`columns_table`**: Kanban column configuration.
10. **`tasks`**: Kanban tasks associated with columns.
11. **`client_timeline_progress`**: Tracks active onboarding step per client.
12. **`timeline_steps`**: Per-client milestone titles and dates.
13. **`internal_users` & `clients`**: Dedicated legacy compatibility tables.

---

## 5. Non-Functional Requirements & Security Baseline

- **SQL Injection Prevention**: 100% of database queries use PDO prepared statements with `?` parameter placeholders. Zero string concatenation.
- **Password Security**: Native Bcrypt (`password_hash()`, `password_verify()`). Zero plaintext passwords.
- **Broken Access Control (BOLA/IDOR)**: Session-based tenant verification for all document downloads and client endpoints.
- **File Upload Hardening**: MIME verification, extension whitelisting, randomized filenames, and `.htaccess` script execution block.
- **Session Hardening**: `session_regenerate_id(true)` upon login; strict session destruction on logout.
- **Latency**: API response time &lt;200ms for 95% of requests.
- **Responsiveness**: Responsive down to 320px width across all auth, CRM, and portal screens.

---

## 6. Sprint Governance, Rosters & Incident Retrospective

### PHP Squad Rosters (Sprint 1)
- **Squad PHP-BE (Backend)**: Zainab Batool (Squad Lead), Atiqa Maqbool, Areesha Sarwar.
- **Squad PHP-FE-A (CRM Board & Executive)**: Fatima Nadeem (Squad Lead), Minahil Imran, Muskan Arshad, Momna Hafeez.
- **Squad PHP-FE-B (Client Portal & Agreement)**: Momna Munir (Squad Lead), Sayyda Arooj, Kaneez Fatima, Halima Sadia.
- **Supervision**: David Chen (Technical Head), Practice Manager, Abdullah Bin Masood, Alishba Amin.

### Non-Negotiables
- Daily submission deadline: **9:00 PM PKT sharp**.
- 45-minute blocker escalation rule.
- Mandatory server-side validation for all inputs.
- No scope-shrinking without early morning escalation (before 3:00 PM).

### Architectural Incident Retrospective
1. **Config File Sprawl**: Unified under `config/database.php` singleton with `.env` loader.
2. **Entry Point Routing Collision**: Unified under `index.php` role-based session router.
3. **Corrupted Bcrypt Hashes**: All seed files updated with genuine verified hashes for `Password@123`.
4. **Dual Schema Drift**: Support for both `status` and `stage` columns in `leads` table.

---

*Certified and Approved as the Official Production Baseline for LogixPulse Module 1.*
