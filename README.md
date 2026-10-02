# BE-04 — Build Digital Signature Verification & Storage Pipeline

Core PHP + MySQL/XAMPP implementation for the assigned BE-04 task.

## Objective

Process, validate, and securely archive client digital signature data, lock the agreement, and generate an immutable audit certificate.

## Included requirements

- `api/agreement_sign.php` endpoint.
- Accepts contract token, signer legal name and base64 signature image.
- Validates base64 payload and verifies the real image type.
- Sanitizes/validates signer input.
- Stores signature graphics in an isolated per-client directory.
- Protects signature storage from direct Apache access.
- Records signer IP, user-agent and timestamp.
- Creates a SHA-256 signature hash.
- Generates a SHA-256 audit certificate hash and stores the certificate JSON.
- Updates contract status to `Executed`.
- Prevents the same executed contract from being signed again.
- Triggers a client welcome notification in the database.
- Uses a transaction and row lock to reduce duplicate execution races.
- Includes verification/testing checklist.

## XAMPP setup

1. Copy this folder into:
   `C:\xampp\htdocs\`

2. Start Apache and MySQL from XAMPP.

3. Open phpMyAdmin.

4. Import:
   `sql/schema.sql`

5. Confirm `config/db.php` matches your MySQL credentials.
   Default XAMPP values are:
   - host: `127.0.0.1`
   - database: `client_onboarding`
   - user: `root`
   - password: empty

6. Send a POST request to:
   `/BE-04_Digital_Signature_Verification_Storage/api/agreement_sign.php`

## Example request

```json
{
  "contract_token": "demo-contract-token-2026-01",
  "signer_legal_name": "Areesha Sarwar",
  "signature_image": "data:image/png;base64,<REAL_BASE64_IMAGE>"
}
```

## Important

The demo token in `sql/schema.sql` is only for local testing. Do not use it in production.

For production, HTTPS, authenticated contract access, stronger token lifecycle/expiry handling, centralized logging, and proper key management should be added according to the project's existing architecture.

## Requirement mapping

### Implementation Checklist
1. Endpoint → `api/agreement_sign.php`
2. Base64 validation/sanitization → request validation + `getimagesizefromstring`
3. Per-client secure storage → `storage/signatures/<client-id>/`
4. Audit details → `contract_audits`
5. Executed status + welcome notification → `contracts` + `notifications`

### Verification & Testing
The complete test checklist is in:
`tests/test_checklist.md`
