# LogixPulse Client Portal — Mobile Testing Report

## Task Information

* Parent Task: Make It Interactive
* Subtask: FEB-W7D4-3 — Test on a Real Phone Screen
* Project: LogixPulse Client Portal
* Testing Type: Mobile UI / Responsive Testing

---

## 1. Pages Tested

| # | Page Name | File Path | Testing Status | Notes |
|---|-----------|-----------|-----------------|-------|
| 1 | Traditional Login | `index.html` | Tested (browser, 4 viewports) | No overflow found |
| 2 | Magic Code Login | `magic-code.html` | Tested (browser, 4 viewports) | Back-link issue found |
| 3 | Verify Code | `verify-code.html` | Tested (browser, 4 viewports) | No issues found |
| 4 | Reset Password (Forgot Password) | `reset-password.html` | Tested (browser, 4 viewports) | Back-link issue found |
| 5 | Set New Password | `set-new-password.html` | Tested (browser, 4 viewports) | No issues found |
| 6 | Dashboard (Overview + Timeline) | `client-portal-dashboard/index.php` (`#dashboard`) | Tested (browser, 4 viewports, logged in) | Overflow + menu overlap issues |
| 7 | Projects | `client-portal-dashboard/index.php` (`#projects`) | Tested (browser, 4 viewports, logged in) | Overflow issue at 320px only |
| 8 | Documents | `client-portal-dashboard/index.php` (`#documents`) | Tested (browser, 4 viewports, logged in) | Overflow issue at 320px only |
| 9 | Your Invoices | `client-portal-dashboard/index.php` (`#invoices`) | Tested (browser, 4 viewports, logged in, with sample data) | Search box layout bug + table scroll |
| 10 | Support | `client-portal-dashboard/index.php` (`#support`) | Tested (browser, 4 viewports, logged in) | Overflow issue at 320px only |
| 11 | Mobile hamburger menu | `includes/navbar.php` | Tested (interaction test) | Overlaps page content when open |
| 12 | Profile dropdown | `includes/navbar.php` | Tested (interaction test) | No issues found |

No Agreement page exists in the project, so it was not tested.

---

## 2. Testing Viewports

Real browser rendering was used (Chromium via Playwright), not code inspection alone. The project was run on a local PHP server with the project's own MySQL schema/seed files, and pages were logged into and screenshotted at the following phone-sized viewports:

* 320 × 800 — Small mobile
* 375 × 812 — iPhone-sized
* 390 × 844 — Modern mobile
* 412 × 915 — Larger mobile

All findings below are **visually confirmed** by rendering the actual page and, where relevant, backed by JavaScript DOM measurements (`scrollWidth`/`clientWidth`, element bounding boxes) captured from the live render. The one exception is noted in Issue #5 (Invoice Table), where the project's own database had no invoice rows; three sample invoice rows were temporarily inserted into the local test database only (not into any project file) so the table could be rendered and checked. This test data was not added to your project.

---

## 3. Visual Issues Found

### Issue #1 — Page overflows horizontally at 320px width on every dashboard section

* **Page:** Dashboard, Projects, Documents, Your Invoices, Support (all dashboard sections)
* **File:** `client-portal-dashboard/assets/css/style.css` (`.navbar-actions`, `.mobile-toggle`, `.workspace-badge` rules and the `@media (max-width: 1024px/768px/480px)` blocks)
* **Viewport:** 320×800 only (375, 390, 412 are clean)
* **Problem:** The navbar's right-hand group (notification icon, avatar, chevron, hamburger button) does not fully fit next to the brand text at 320px width.
* **Expected Behavior:** Navbar should fit fully inside the 320px viewport with no horizontal scroll.
* **Actual Behavior:** Page scroll width measures 331px against a 320px client width. The hamburger icon is visibly cut off at the right edge of the screen.
* **Severity:** Medium
* **Evidence:** `document.documentElement.scrollWidth` = 331 vs `clientWidth` = 320, measured on all 5 dashboard sections; screenshot `dash_dashboard__320x800.png` shows the hamburger icon clipped.
* **Status:** Found — Fix Later

### Issue #2 — Opening the mobile menu hides page content underneath it (Timeline card almost fully covered)

* **Page:** Dashboard (reproducible from any section, most visible on Dashboard because of the Project Timeline card sitting right below the navbar)
* **File:** `client-portal-dashboard/assets/css/style.css` (`.portal-navbar { position: sticky; z-index: 100; }`, `.mobile-links.open { display: flex; }`) and `client-portal-dashboard/assets/js/script.js` (mobile toggle logic)
* **Viewport:** 375×812 (confirmed; behavior is not width-dependent, so it is expected on 320/390/412 too)
* **Problem:** The hamburger menu's link list is rendered inside the sticky header itself. Opening it grows the header's height (measured 69px → 295px), but the page content below is not measured after that regrowth: `main.dashboard`'s bounding box stays at `top: 0` before and after, so the Project Timeline card (which sits ~106–345px from the top) ends up mostly underneath the taller, higher-stacked (`z-index: 100`) header once the menu is open.
* **Expected Behavior:** Opening the mobile menu should either push page content down by the menu's full height, or overlay the menu as a floating panel above content without permanently hiding it once closed logic resolves.
* **Actual Behavior:** With the menu open, only a small sliver of the Project Timeline card ("Jan 20") is visible peeking out from beneath the header; the "Project Timeline" heading, connector line, and step icons are all hidden behind the header.
* **Severity:** High
* **Evidence:** Bounding-box measurement: header `top:0, bottom:295` after opening vs. Timeline card `top:106, bottom:345.78` (unchanged from before opening, when header was only 69px tall). Screenshots: `mobile_menu_viewport_only__375.png`, `profile_dropdown_open__375x812.png` (same effect visible).
* **Status:** Found — Fix Later

### Issue #3 — Invoice search box breaks into a tall empty box with a misplaced icon on mobile

* **Page:** Your Invoices
* **File:** `client-portal-dashboard/assets/css/style.css` — `.invoice-search { flex: 1 1 240px; }` combined with the `@media (max-width: 480px) { .invoice-toolbar { flex-direction: column; } }` rule
* **Viewport:** 320×800, 375×812, 390×844, 412×915 (all four — this is a ≤480px breakpoint issue, so every tested phone width is affected)
* **Problem:** `.invoice-search` has `flex-basis: 240px`, which is fine as a *width* when `.invoice-toolbar` lays out as a row. Once the mobile media query switches `.invoice-toolbar` to `flex-direction: column`, that same `240px` flex-basis is applied along the (now vertical) main axis, so `.invoice-search` becomes 240px **tall** instead of 240px wide. The actual `<input>` inside it is only ~35px tall, so there is roughly 150–200px of empty space inside the search box, and the search icon (`position: absolute; top: 50%`, centered against the inflated 240px parent) floats far below the input field instead of sitting inside it.
* **Expected Behavior:** Search input should be a normal single-line field with the icon centered inside it; filter chips should follow directly underneath with normal spacing.
* **Actual Behavior:** A large blank gap appears between the search input and the filter chips, with a lone magnifying-glass icon floating in that gap.
* **Severity:** High
* **Evidence:** Bounding boxes at 375×812 — `.invoice-search` box: `height 240px`; `.invoice-search input`: `height 35px`; `.invoice-search svg`: `y ≈ 321` (well below the input, which ends at `y ≈ 244`). Screenshot: `invoices_populated__375x812.png`.
* **Status:** Found — Fix Later

### Issue #4 — Back link shows only an arrow with no label text

* **Page:** Magic Code Login, Reset Password
* **File:** `magic-code.html` line 34, `reset-password.html` line 34 — both contain `<a href="index.html" class="back-link"></a>`; the arrow glyph comes from `css/style.css` → `.back-link::before { content: '\2190'; }`
* **Viewport:** All (not width-dependent, but caught during mobile pass)
* **Problem:** The anchor tag has no text between its opening and closing tags, so the only visible content is the CSS-generated arrow character.
* **Expected Behavior:** The link should read something like "← Back to Login", matching the pattern already used correctly on `set-new-password.html` ("← Back to Login").
* **Actual Behavior:** Only a small, unlabeled "←" is shown, with no indication of where the link goes.
* **Severity:** Medium (usability/accessibility on a touch screen — small, unlabeled tap target)
* **Evidence:** Code comparison across the three pages; screenshots `magic_code__375x812.png` and `reset_password__375x812.png` vs. `set_new_password__375x812.png`.
* **Status:** Found — Fix Later

### Issue #5 — Large vertical whitespace above the form on short mobile screens (login & reset password)

* **Page:** Traditional Login, Reset Password
* **File:** `css/style.css` (auth wrapper rule using `min-height: calc(100vh - 56px)` with `align-items: center; justify-content: center;`)
* **Viewport:** 375×812, 390×844, 412×915
* **Problem:** The auth card is vertically centered in the full viewport height. On a tall, short-content phone screen this leaves a large empty gap above the heading before the user sees any content.
* **Expected Behavior / Figma:** The Figma comps show the card positioned near the top of the screen with a small header/announcement bar area, not vertically centered with a big empty gap above.
* **Actual Behavior:** On mobile, roughly 25–30% of the visible screen above the fold is blank before the "Sign in to..." / "Reset password" heading appears.
* **Severity:** Low
* **Evidence:** Screenshots `login_index__375x812.png`, `reset_password__375x812.png`.
* **Status:** Found — Fix Later

---

## 4. Timeline Issues

The Project Timeline (inside the Dashboard section) was checked specifically per the task instructions.

* The client used for testing (`client1@acmecorp.com`) has **5 timeline steps** in the database: Project Started, Requirements & Planning, Development, Review & Approval, Delivery.
* `.timeline-track` is set to `min-width: 640px` (and `min-width: 560px` at ≤640px screens) inside a `.timeline-scroll` container with `overflow-x: auto`. This is a **contained** horizontal scroll area — it does not break the page layout or cause the whole page to scroll sideways (confirmed: page-level `scrollWidth` stayed equal to `clientWidth` at 375/390/412px).
* However, only about 3 of the 5 steps are visible without scrolling (confirmed: container `clientWidth` 333–373px vs. content `scrollWidth` 560px). "Review & Approval" and "Delivery" are cut off on the right.
* There is **no visual indicator** (scrollbar, edge fade, arrow, or dot indicator) to tell the user more steps exist off-screen. A user could easily believe the project only has 3 stages.
* Scrolling the timeline horizontally does work correctly and reveals the remaining two steps with correct styling (confirmed via `timeline_after_scroll__375.png`).
* Additionally, when the mobile hamburger menu is opened, the Timeline card (being the first card on the Dashboard) is the one most affected by Issue #2 above — it becomes almost entirely hidden behind the expanded sticky header.

**Timeline status: Not fully visible on mobile without scrolling, and the scroll interaction has no visible affordance. Functionally scrollable, but easy to miss.**

---

## 5. Invoice Table Issues

* The project's own test database had zero invoice rows for the seeded client, so by default the Invoices section shows only the "No invoices yet" empty state on mobile — this empty state itself displays correctly at all four viewports, with no overflow.
* To check the actual table's mobile behavior, three sample invoice rows were temporarily added to the local test database only (not to any project file) so the populated table could be rendered.
* With rows present:
  * `.invoice-table` has `min-width: 720px` inside `.invoice-table-scroll` (`overflow-x: auto`). This is a contained scroll area, confirmed not to break the overall page layout at any tested width (page `scrollWidth` stayed equal to `clientWidth`).
  * At every phone width tested (320–412px), the visible wrapper is only 276–368px wide against a 720px-wide table, so the user must scroll horizontally within the table to see the Project, Issued On, Amount, Status, and Actions columns beyond the first one or two.
  * The Invoice Search box + filter chip layout bug described in Issue #3 sits directly above this table and is the more urgent of the two invoice-related problems.

**Invoice Table status: Contained horizontal scroll works and does not break the page, but most columns require scrolling to reach on any phone width. The search/filter toolbar above it has a confirmed layout bug (Issue #3).**

---

## 6. Authentication Screen Issues

| Screen | Status |
|---|---|
| Traditional Login (`index.html`) | No overflow at any viewport. Large empty vertical space above the form on tall screens (Issue #5, low severity). |
| Magic Code Login (`magic-code.html`) | No overflow. Back link shows only an unlabeled arrow (Issue #4). |
| Verify Code (`verify-code.html`) | No issues found at any viewport — the 6-digit code boxes fit correctly even at 320px. |
| Reset Password (`reset-password.html`) | No overflow. Back link shows only an unlabeled arrow (Issue #4). Same vertical whitespace note as Login (Issue #5). |
| Set New Password (`set-new-password.html`) | No issues found — this page has the back link done correctly ("← Back to Login"), for comparison against Issue #4. |

All five auth screens that exist in the project were checked at all four viewports.

---

## 7. Figma vs Code Differences

The Figma screenshots provided cover the Login (Email code / Sign in) and Password Reset flows, plus a desktop Dashboard/Team Management design system reference. The dashboard/team-management Figma frames are desktop-only layouts and are not a 1:1 mobile reference for this project's Client Portal dashboard, so mobile-specific comparisons for the dashboard could not be made with confidence. Confirmed differences are limited to what could be directly compared:

| Page | Figma Expected | Code Observed | Difference |
|---|---|---|---|
| Sign In screen | Heading references the specific organization, e.g. "Sign in to Acme Tech Pod" | Heading reads "Sign in to your Client Portal" (generic) | Heading text does not personalize to the client's organization name |
| Sign In / Reset Password screens | Card sits near the top of the screen with minimal space above it | Card is vertically centered on the page, leaving a large blank area above it on phone-height screens | Vertical placement differs on mobile (Issue #5) |
| Email code screen (Login 2) | Shows a footer note ("Token expires in ...") and a support/contact link in the footer | Not verified against `verify-code.html` line by line beyond overall layout, which matched reasonably well | Minor — no major mismatch found beyond spacing, not logged as a hard issue |

No Figma reference was provided for the Dashboard's mobile view, Timeline component, Invoice Table, Documents, or Support sections, so no Figma-vs-code differences are reported for those — only the code-based/visual findings in Sections 3–6 above apply.

---

## 8. Pages With No Issues Found

* Verify Code (`verify-code.html`) — clean at all four viewports.
* Set New Password (`set-new-password.html`) — clean at all four viewports, and has the correctly-labeled back link.
* Documents section — no section-specific visual issues found, but shared navbar overflow at 320px is documented in Issue #1.
* Support section — no section-specific visual issues found, but shared navbar overflow at 320px is documented in Issue #1.
* Projects section — no section-specific visual issues found, but shared navbar overflow at 320px is documented in Issue #1.
* Profile dropdown — opens and displays correctly at 375×812 with no clipping.

---

## 9. Testing Summary

* **Total pages/sections inspected:** 12 (5 auth screens + 5 dashboard sections + mobile menu + profile dropdown)
* **Total issues found:** 5 (2 High, 2 Medium, 1 Low)
* **Main mobile problems:**
  1. Mobile hamburger menu overlapping/hiding page content when open (High)
  2. Invoice search/filter toolbar breaking into a tall box with a misplaced icon on mobile (High)
  3. Horizontal page overflow at the 320px viewport across all dashboard sections (Medium)
  4. Unlabeled back-arrow links on two auth pages (Medium)
  5. Excess vertical whitespace above auth forms on phone-height screens (Low)
* **Timeline status:** Functionally scrollable and not broken, but only ~60% of steps are visible without scrolling and there is no scroll indicator — flagged for follow-up.
* **Invoice Table status:** Contained horizontal scroll works correctly and does not break the page; most columns require scrolling on any phone width. The bigger problem is the search/filter toolbar above the table (Issue #3).
* **Auth screens status:** All 5 existing auth screens checked at all 4 viewports; 2 of 5 have the unlabeled back-link issue, 2 of 5 have the whitespace note; Verify Code and Set New Password are fully clean.
* **Testing method:** Real headless-browser rendering (Chromium via Playwright) against a locally running copy of the project (PHP built-in server + MySQL using the project's own schema/seed SQL files), not code inspection alone. All screenshots and DOM measurements referenced above were captured from that live render.
* **Ready for future fixes:** Yes. No code was modified during this testing pass — this report only documents findings for a later fix task.

---

*No existing project files were changed as part of this testing task. A local test database (MySQL) and a local PHP server were used only to render the pages for testing purposes; three temporary invoice rows were inserted into that local test database (not into any project file) solely to visually verify the Invoice Table's mobile behavior.*
