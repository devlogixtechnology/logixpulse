# FEB-W7D3-1 — Connect the Timeline to Real Data

**What changed**
- `database/schema.sql` — new `clients` and `timeline_steps` tables.
- `database/seed.sql` — 2 sample clients on 2 different phases, so the
  "done when" criteria (two accounts, two different current phases) is
  verifiable.
- `config/database.php` — PDO connection helper. Returns `null` if the
  DB isn't reachable (or `DB_MOCK_MODE=true` in `.env`), so the app
  doesn't crash without MySQL running.
- `includes/timeline.php` — `getClientTimeline($clientId)`: reads the
  client's real current phase + steps from the DB, and computes each
  step's status (completed / current / upcoming) instead of it being
  hardcoded. Falls back to an in-memory mock dataset with the same
  shape when there's no DB connection.
- `index.php` — timeline card now loops over `getClientTimeline()`
  output instead of 5 hardcoded `<li>` blocks. No CSS/markup classes
  changed, so the existing Figma-matched look is untouched.
- `index.php` — client identity now comes **only** from
  `$_SESSION['client_id']`, matching the "logged-in client" wording
  in the requirement. It's no longer settable via a plain `?client_id=`
  request parameter on every page load.
- `auth/dev-session.php` (new) — dev-only helper that sets
  `$_SESSION['client_id']` to simulate a login, since the real login
  flow doesn't exist yet (separate auth task). Refuses to run when
  `APP_ENV=production`, so it can't be used to hijack a session live.

**How to test**
- With MySQL: run `schema.sql` then `seed.sql`, set `DB_*` in `.env`
  (and `DB_MOCK_MODE=false`), then visit
  `auth/dev-session.php?client_id=1` and `auth/dev-session.php?client_id=2`
  (each redirects to `index.php`) — each should show a different
  current step.
- Without MySQL: leave `DB_MOCK_MODE=true` (default) — same two dev
  logins work off the mock dataset in `includes/timeline.php`.
- With no session set at all, the timeline card shows "Log in to
  view your project timeline." instead of any client's data.

**Resolved limitation**
- Previously `?client_id=` on `index.php` itself could switch/overwrite
  the session on any request — anyone could view any client's timeline
  just by changing a URL, which didn't match "for the logged-in
  client". That override has been moved to the env-gated
  `auth/dev-session.php` and removed from `index.php`.

**Still out of scope (unchanged)**
- Notifications, Recent Documents, Support Tickets, Active Projects,
  and Account Overview cards now show **sample/dummy data** (added on
  PM request, same pattern as the existing `$invoices` sample data on
  the Invoices tab). Each is clearly labeled with a "Sample" tag in
  the card header. Wiring these to a real backend is separate,
  future work for each service — nothing here is live data.
