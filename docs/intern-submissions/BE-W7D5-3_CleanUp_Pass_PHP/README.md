# BE-W7D5-3: Clean-Up Pass (Core PHP)

## Files Included
1. `auth_helper.php`: Session login check (`require_login()`) and input validators (`validate_string()`, `validate_email()`).
2. `magic_code.php`: Protected magic code generator and verifier.
3. `password_reset.php`: Protected password reset script checking login and all field rules.
4. `lead_search_protected.php`: Search endpoint protected by `require_login()`.

## Validation Checklist
- Every script calls `require_login()` first.
- Rejects missing, invalid, or malformed parameters with HTTP 400 and clear error message.
