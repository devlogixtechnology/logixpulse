# DevLogix Client Portal — Empty Dashboard

**Task ID:** FEB-W7D1-3
**Task Title:** Build the Empty Client Dashboard (Navbar, Profile Dropdown)
**Squad:** FE-B — Client Onboarding Portal
**Sprint:** Sprint 1 — Foundation Sprint

## Project Purpose

This is the empty Client Portal dashboard for DevLogix's external clients. It delivers the navbar, the profile dropdown, and a set of clearly labelled empty sections. It does not include login, magic code auth, reset password, or any backend/database logic. Those belong to other tasks on the squad.

## Tech Stack

- HTML5
- CSS3
- Vanilla JavaScript (ES6+)
- Bootstrap 5 via CDN (grid, container, spacing utilities)
- PHP Core (procedural, page structure and includes only)

No React, no Vue, no Angular, no jQuery, no npm, no Bootstrap JS bundle, no database, and no authentication logic are used in this task. Bootstrap's own JavaScript is intentionally left out since the dropdown and mobile nav are handled by custom vanilla JS in `script.js`.

## File Structure

```
client-portal-dashboard/
│
├── index.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── images/
│
├── includes/
│   ├── header.php
│   └── navbar.php
│
└── README.md
```

This structure was already correct in the previous version of the project. This update did not need to move any files or fix broken include paths; it added Bootstrap 5, and switched the dashboard layout from a custom CSS grid to Bootstrap's row/col system.

## File-by-File Explanation

### index.php
Entry point. Includes `header.php` and `navbar.php`, then renders four sections inside a Bootstrap `container`: Dashboard, Projects, Documents, and Support. Only one section is visible at a time; `script.js` controls which one based on the navbar link clicked. The Dashboard section keeps the original cards (Active Projects, Recent Documents, Notifications, Support Tickets, Account Overview). Projects, Documents, and Support are new sections, each with a single empty-state card styled the same way as the dashboard cards.

### includes/header.php
Document head: doctype, charset, viewport, page title, the Bootstrap 5 CDN stylesheet, and the project's own `style.css` loaded after it so custom DevLogix styling overrides Bootstrap defaults where needed.

### includes/navbar.php
The Client Portal navbar: DevLogix branding, primary nav links, a notification icon, and the profile trigger with its dropdown menu markup. Each main nav link (Dashboard, Projects, Documents, Support) has a real `href="#section-id"` and a `data-section` attribute that `script.js` reads to switch sections on the same page. The outer wrapper uses Bootstrap's `container-fluid` class for consistent horizontal alignment, with custom classes carrying the actual DevLogix look. A mobile link list is included for small screens, toggled by `script.js`.

### assets/css/style.css
Custom styling on top of Bootstrap: DevLogix color variables under `:root`, navbar styling, profile dropdown styling, dashboard card styling, and media queries for the breakpoints Bootstrap doesn't already cover for this design.

### assets/js/script.js
Vanilla JavaScript for:
- Opening and closing the profile dropdown on click
- Closing it on outside click and on Escape
- Toggling the mobile navigation menu
- Switching between the Dashboard, Projects, Documents, and Support sections when a navbar link is clicked, on both desktop and mobile
- Updating the active navbar link to match the visible section
- Closing the mobile menu automatically after a section is selected

No Bootstrap JavaScript is loaded or required.

## What Changed From the Previous Version

- Applied the Figma design's color system: indigo/violet accent (`#4F46E5` / `#4338CA`) replacing the earlier navy+green DevLogix palette, sampled from the "Navbar Client" component in the supplied Figma file.
- Navbar background changed from dark navy to white, matching the Figma "Navbar Client" frame.
- Nav links now use a pill-shaped active state (rounded indigo background) instead of a plain rounded-rectangle highlight.
- Added an SVG logo mark and a "Client Workspace" badge in the navbar, matching the logo and org-badge pattern shown in the Figma navbar.
- Increased card corner radius and adjusted card shadows to match the softer, more rounded card style seen in the Figma design system.
- Profile dropdown, mobile menu, and section-switching behavior are functionally unchanged, only their colors were updated to match the new light navbar theme.
- Made the navbar functional: Dashboard, Projects, Documents, and Support links now switch between same-page sections instead of pointing to `href="#"`.
- Added Projects, Documents, and Support sections to `index.php`, each with its own empty-state card, kept visually consistent with the existing dashboard cards.
- Added section-switching logic to `script.js`, which shows the selected section, hides the rest, updates the active navbar link on both desktop and mobile, and closes the mobile menu after a selection.
- Added `.app-section` / `.app-section.active` rules to `style.css` to control which section is visible.

The file locations, PHP include paths, and CSS/JS asset paths were already correct in the previous version, so no path fixes were needed this round.

## Note on the Figma Design Source

The Figma file supplied covers the full DevLogix product, including frames for other squads' tasks (Staff Login, Password Reset, the internal CRM "Dashboard - Management" page for Frontend-A). There was no dedicated "empty Client Dashboard" frame for this task in what was provided. This update:

- Matched the **Navbar Client** frame closely, since that component is directly in scope for this task.
- Reused the general card style, spacing, radius, and color language visible across the Figma file (card shadows, rounded corners, pill badges) for the empty-state dashboard sections, without importing any CRM content (Pipeline Overview, Team Leaderboard, deal values) from the internal staff dashboard, since that belongs to a different squad's task and would count as unrelated, invented content here.
- Kept the "DevLogix" product name instead of the "LogixPulse" name used as placeholder branding in the Figma mockups, since renaming the actual product wasn't part of this task's brief.
- Did not copy the "Super Administrator" role badge or the "Password & 2FA" / "Notification Settings" items seen in the Figma's ACCOUNT dropdown, since that dropdown appears to belong to the internal staff sidebar (dark theme, admin role) rather than the Client Portal. The existing dropdown items were kept as-is.

If a dedicated Client Dashboard frame or a Client Portal-specific profile dropdown exists in Figma and wasn't part of what was shared, send that frame and these can be tightened further.

## Design Assumptions

No Figma file has been supplied for this task. The following are assumptions, not confirmed design specifications:

- Section names (Active Projects, Recent Documents, Notifications, Support Tickets, Account Overview) are placeholders that fit a client portal dashboard. They are carried over from the previous version rather than sourced from an official design.
- Dropdown items (My Profile, Account Settings, Help & Support, Log Out) follow a standard portal pattern. Log Out is a UI placeholder only; no session logic exists in this task.
- The DevLogix color palette is used as provided, since it comes from existing project styling context rather than a verified Figma file for this exact dashboard.

If an official Figma design becomes available, layout, section names, spacing, and dropdown items should be updated to match it exactly.

## Setup Instructions

1. Check PHP is installed:

```bash
php -v
```

2. Open the project folder in VS Code.

3. Open the integrated terminal.

4. Navigate to the project folder:

```bash
cd client-portal-dashboard
```

5. Start the PHP development server:

```bash
php -S localhost:8000
```

6. Open in your browser:

```
http://localhost:8000
```

## Testing Note

This project was reviewed by reading through the PHP, CSS, and JS source directly, checking include paths, asset paths, and class references line by line. PHP was not available to actually execute in the environment this was built in, so the project has not been run in a live browser from this side. Please run it locally using the steps above and go through the checklist below before marking the task complete.

## Testing Checklist

### PHP
- [ ] `index.php` loads with no errors or warnings.
- [ ] `header.php` and `navbar.php` include correctly.
- [ ] No broken paths to CSS or JS assets.

### CSS / Layout
- [ ] Bootstrap CDN loads (check browser Network tab).
- [ ] Dashboard cards align two-per-row on desktop and tablet.
- [ ] Cards stack to one column on mobile.
- [ ] No horizontal overflow at any width.
- [ ] No section overlaps another.

### Navbar
- [ ] Navbar is visually distinct from the dashboard background.
- [ ] Clicking Dashboard, Projects, Documents, and Support each shows the correct section.
- [ ] Only one section is visible at a time.
- [ ] Active link updates to match the visible section, on desktop and mobile.
- [ ] Mobile menu toggle opens and closes the link list.
- [ ] Mobile menu closes automatically after a link is clicked.

### Profile Dropdown
- [ ] Clicking the avatar opens the dropdown.
- [ ] Clicking the avatar again closes it.
- [ ] Clicking outside the dropdown closes it.
- [ ] Pressing Escape closes it.
- [ ] Dropdown fits within the screen at 390px and 320px.
- [ ] All dropdown items are readable and clickable.

### Responsive Widths
- [ ] 1440px
- [ ] 1024px
- [ ] 768px
- [ ] 390px
- [ ] 320px

### Console
- [ ] No JavaScript console errors.
- [ ] No PHP warnings in the server log.

## Git Workflow

Following the Sprint 1 workflow, use one feature branch per task ID:

```bash
git checkout -b feature/FEB-W7D1-3-empty-client-dashboard
git add .
git commit -m "Build empty client dashboard with navbar and profile dropdown"
git push -u origin feature/FEB-W7D1-3-empty-client-dashboard
```

After pushing, open a Pull Request into `develop` through your team's repository, and request mentor review before it merges into `module-1`. No PR has been created from this side. This project only prepares the local files for you to commit and push yourself.
