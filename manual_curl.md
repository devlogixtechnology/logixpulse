# Manual cURL Verification

Run from a shell after configuring the application.

## 1. No active session -> 401
```bash
curl -i http://localhost/rec-be-02-session-auth-role-enforcement/api/internal_dashboard.php
```

## 2. Login as staff
```bash
curl -i -c cookies.txt \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"staff@example.com\",\"password\":\"Password123!\"}" \
  http://localhost/rec-be-02-session-auth-role-enforcement/api/login.php
```

Copy the returned `csrf_token`.

## 3. Staff -> internal endpoint = 200
```bash
curl -i -b cookies.txt \
  http://localhost/rec-be-02-session-auth-role-enforcement/api/internal_dashboard.php
```

## 4. Staff -> client endpoint = 403
```bash
curl -i -b cookies.txt \
  http://localhost/rec-be-02-session-auth-role-enforcement/api/client_portal.php
```

## 5. Logout with CSRF token
```bash
curl -i -b cookies.txt \
  -H "Content-Type: application/json" \
  -H "X-CSRF-Token: PASTE_TOKEN_HERE" \
  -d "{}" \
  http://localhost/rec-be-02-session-auth-role-enforcement/api/logout.php
```

## 6. Login as client and test cross-role access
Login using:
`client@example.com / Password123!`

Then:
- `/api/client_portal.php` -> 200
- `/api/internal_dashboard.php` -> 403
