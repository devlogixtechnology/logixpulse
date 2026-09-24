# LogixPulse — CRM & Client Portal

LogixPulse is an integrated CRM and Client Collaboration platform built with Core PHP, MySQL, and responsive HTML5/CSS/JavaScript. It unifies internal CRM lead management, activity logs, file/invoice attachments, and KPI reporting with a modern client-facing portal featuring project progress timelines and invoice management.

---

## Table of Contents

- [System Architecture](#system-architecture)
- [Directory Structure](#directory-structure)
- [Prerequisites](#prerequisites)
- [Quick Start / Local Setup](#quick-start--local-setup)
- [Database Setup & Configuration](#database-setup--configuration)
- [Default Demo Credentials](#default-demo-credentials)
- [Core Features & API Endpoints](#core-features--api-endpoints)
- [Security & Access Control](#security--access-control)
- [Troubleshooting](#troubleshooting)

---

## System Architecture

LogixPulse is structured into two complementary layers:

1. **Client Portal (`client-portal-dashboard/`):**
   - Dynamic project milestone timeline
   - Client invoice viewing, filtering, and isolation
   - Real-time digital signature and agreement workflow (`agreement_page/`)
   - Standalone embeddable timeline widget (`timeline_feature/`)

2. **Internal CRM & Management API (`backend/`, `api/`):**
   - Traditional login and session inspection (`who_is_logged_in.php`)
   - Lead creation, search, stage transition, and manual note logging
   - Activity history timeline with newest-first ordering
   - Protected file & PDF invoice uploads per client
   - Management KPI summary analytics

---

## Directory Structure

```text
devlogixpulse/
├── .env.example                      # Template for environment configuration
├── index.php                         # Root session router (routes client vs unauthenticated)
├── index.html                        # Client portal sign-in screen
├── dashboard.html                    # Static dashboard shell
├── kanban.php                        # Interactive drag-and-drop Kanban board
├── update-stage.php                  # Kanban card position sync handler
├── agreement_page/                   # Master Agreement & drawable digital signature screen
│   ├── index.html
│   ├── script.js
│   └── style.css
├── timeline_feature/                 # Self-contained project timeline component
│   ├── index.html
│   ├── style.css
│   └── timeline.php
├── client-portal-dashboard/          # Dynamic client portal app
│   ├── index.php                     # Client dashboard with timeline and invoice views
│   ├── includes/
│   │   ├── header.php
│   │   ├── navbar.php
│   │   └── timeline.php              # Real DB & mock timeline dataset helper
│   ├── config/database.php           # Wrapper pointing to canonical config
│   └── assets/
├── api/                              # RESTful JSON endpoints
│   ├── login.php                     # Client authentication endpoint
│   ├── logout.php                    # Session termination endpoint
│   ├── who_is_logged_in.php          # Session identity check
│   ├── save_lead.php                 # Lead creation endpoint
│   ├── lead_search.php               # Fulltext lead search API
│   ├── lead_details.php              # Lead data & history endpoint
│   ├── lead_activity.php             # JSON lead activity logs
│   ├── activity_history.php          # Activity history viewer
│   ├── add_note.php                  # Manual note submission endpoint
│   ├── update_lead_stage.php         # Lead status/stage updater with audit logging
│   ├── grouped-leads.php             # Grouped leads dataset (LeadRepository)
│   ├── dashboard_kpi.php             # KPI metrics calculation API
│   ├── upload.php                    # Secure invoice upload handler
│   ├── download.php                  # Client-isolated invoice download handler
│   ├── download-invoice.php          # Text invoice exporter
│   ├── request_magic_code.php        # Passwordless 6-digit OTP request
│   ├── verify_magic_code.php         # Magic code verification & login
│   ├── request_password_reset.php    # Password reset token generator
│   └── reset_password.php            # Secure password reset handler
├── backend/                          # Backend views, forms & controller logic
│   ├── auth.php                      # Core authentication, roles & session helper
│   ├── auth_check.php                # Reusable page guard
│   ├── helpers.php                   # Escaping, redirect, and flash message helpers
│   ├── internal_login.php            # Staff & admin login form
│   ├── client_login.php              # Client login form
│   ├── process_internal_login.php    # Staff login authentication processor
│   ├── process_client_login.php      # Client login authentication processor
│   ├── main_dashboard.php            # Internal staff dashboard view
│   ├── client_dashboard.php          # Client redirect router
│   ├── upload.php                    # Admin invoice upload page
│   ├── download.php                  # Isolated invoice streaming
│   ├── client_invoices.php           # Client invoice listing view
│   ├── lead_details.php              # Lead profile view with timeline
│   ├── activity_history.php          # Activity history view
│   └── add_note.php                  # Manual note creation view
├── config/
│   ├── database.php                  # Unified database connection & .env loader
│   └── db.php                        # Backward-compatible wrapper
├── includes/
│   ├── LeadRepository.php            # Lead querying & grouping repository
│   ├── header.php
│   └── navbar.php
├── sql/
│   ├── 01_create_database.sql        # Database initialization
│   ├── 02_schema.sql                 # Complete 13-table unified schema
│   ├── 03_seed_data.sql              # Demo data with verified Password@123 hashes
│   ├── 04_verification_queries.sql   # Integrity checks
│   └── 05_timeline_schema_and_seed.sql # Client timeline tables & data
├── uploads/
│   ├── .htaccess                     # Script execution block for upload isolation
│   ├── invoices/                     # Secure invoice storage
│   └── signatures/                   # Digital signature storage
└── schema.sql                        # Root all-in-one schema and seed file
```

---

## Prerequisites

- **PHP**: 8.0 or higher (extensions: `pdo`, `pdo_mysql`, `fileinfo`, `mbstring`, `json`)
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Web Server**: Apache, Nginx, or PHP Built-in Server (`php -S`)
- **Optional**: XAMPP / WAMP / Docker for quick local MySQL hosting

---

## Quick Start / Local Setup

### 1. Clone & Configure Environment

Copy `.env.example` to `.env` in the root folder:

```bash
cp .env.example .env
```

Adjust your MySQL database credentials inside `.env` if necessary:

```ini
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=logix_pulse
DB_USER=root
DB_PASS=
```

### 2. Import Database Schema & Seed Data

Import the all-in-one SQL file into MySQL:

```bash
mysql -u root -p < schema.sql
```

Alternatively, run the numbered scripts sequentially in MySQL / phpMyAdmin:
1. `sql/01_create_database.sql`
2. `sql/02_schema.sql`
3. `sql/03_seed_data.sql`
4. `sql/05_timeline_schema_and_seed.sql`

### 3. Run the Web Server

Using the PHP built-in web server:

```bash
php -S 127.0.0.1:8000
```

Open your browser at:
- Application Entry: `http://127.0.0.1:8000/` (automatically redirects to `index.html` or `client-portal-dashboard/`)
- Client Portal Sign In: `http://127.0.0.1:8000/index.html`
- Internal Staff Login: `http://127.0.0.1:8000/backend/internal_login.php`
- Kanban Board: `http://127.0.0.1:8000/kanban.php`
- Master Agreement & Signature: `http://127.0.0.1:8000/agreement_page/index.html`

---

## Default Demo Credentials

All seed accounts are configured with the password: **`Password@123`**

### Client Accounts
| Name | Email | Password | Role |
| :--- | :--- | :--- | :--- |
| John Harris | `client1@acmecorp.com` | `Password@123` | `client` |
| Lisa Wang | `client2@nova.com` | `Password@123` | `client` |
| Robert Gomez | `client3@bluewave.io` | `Password@123` | `client` |
| Patricia Ortiz | `client4@steelvent.com` | `Password@123` | `client` |
| Kevin Brooks | `client5@brightcore.com` | `Password@123` | `client` |

### Internal Staff & Administrator Accounts
| Name | Email | Password | Role |
| :--- | :--- | :--- | :--- |
| System Administrator | `admin@logixpulse.com` | `Password@123` | `admin` |
| Sarah Mitchell | `exec@logixpulse.com` | `Password@123` | `executive` |
| Michael Torres | `staff@logixpulse.com` | `Password@123` | `administrator` |
| David Chen | `head@logixpulse.com` | `Password@123` | `head` |
| Alice Nelson | `team1@logixpulse.com` | `Password@123` | `team` |

---

## Core Features & API Endpoints

### 1. Authentication & Session Management
- **`POST api/login.php`**: Validates email/password against `users`, regenerates session, redirects clients to `client-portal-dashboard/index.php`.
- **`GET api/who_is_logged_in.php`**: Returns the active session user, role, and login timestamp in JSON format.
- **`GET logout.php` / `GET api/logout.php`**: Destroys session, cleans cookies, and redirects to `index.html`.

### 2. CRM Leads & Activity Tracking
- **`POST api/save_lead.php`**: Adds a new lead with validation (`name`, `email`, `phone`, `source`) and automatically writes an initial event in `activity_logs`.
- **`GET api/lead_search.php?search={query}`**: Multi-column search over name, email, phone, and company.
- **`GET api/grouped-leads.php`**: Returns all CRM leads categorized by their current status via `LeadRepository`.
- **`POST api/update_lead_stage.php`**: Updates `status` and `stage` on a lead with database transaction locking and writes a stage change record to `activity_logs`.
- **`GET api/lead_activity.php?lead_id={id}`**: JSON endpoint returning all activity logs for a lead, newest first.
- **`POST api/add_note.php`**: Saves a manual note against a lead.

### 3. Client Invoices & File Storage
- **`POST backend/upload.php`**: Protected endpoint allowing administrators to upload client invoices (PDF up to 10MB) stored with randomized unique filenames.
- **`GET backend/download.php?id={id}`**: Secure invoice download enforcing strict client isolation (clients can only open their own invoices).
- **`GET backend/client_invoices.php`**: Client view listing all invoices assigned to their account.

### 4. Passwordless Magic Code & Reset
- **`POST api/request_magic_code.php`**: Issues a 6-digit OTP code with 10-minute expiry.
- **`POST api/verify_magic_code.php`**: Validates OTP and authenticates user.
- **`POST api/request_password_reset.php`**: Generates a cryptographically secure token (SHA-256 hash stored in database).
- **`POST api/reset_password.php`**: Updates user password using standard `password_hash()`.

### 5. Management Dashboard KPIs
- **`GET api/dashboard_kpi.php`**: Dynamically computes month-to-date and week-to-date KPIs (Total Leads, Active Deals, Meetings This Week, Closed Deals) along with percentage changes compared to previous periods.

---

## Security & Access Control

- **SQL Injection Prevention**: All queries use PDO prepared statements with parameterized inputs.
- **Password Security**: Strong bcrypt password hashing using PHP's native `password_hash()` and `password_verify()`.
- **Upload Hardening**: Uploaded invoice directory (`uploads/`) is protected by `.htaccess` with `Require all denied` to prevent remote script execution.
- **Client Data Isolation**: Invoice downloads verify `invoice.client_id == $_SESSION['user_id']` so no client can inspect another client's billing documents.
- **CSRF & Session Hijacking**: Uses `session_regenerate_id(true)` upon successful authentication and cookie expiration parameters.

---

## Troubleshooting

### "Unable to connect to database"
- Verify that your MySQL server is running on `127.0.0.1:3306`.
- Confirm `.env` contains valid credentials.
- The client portal automatically falls back to an offline mock dataset when MySQL is stopped, so UI testing can proceed uninterrupted.

### "Login failed with Password@123"
- Ensure `schema.sql` or `sql/03_seed_data.sql` was re-imported. The seed file contains verified bcrypt hashes for `Password@123`.
