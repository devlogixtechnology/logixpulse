# BE-04 Verification & Testing Checklist

## 1. Submit signed contract payload
POST JSON to:

`http://localhost/BE-04_Digital_Signature_Verification_Storage/api/agreement_sign.php`

Required fields:
- `contract_token`
- `signer_legal_name`
- `signature_image` (PNG/JPEG/WEBP base64 data URI)

Expected successful response:
- `success: true`
- contract status becomes `Executed`
- audit certificate hash is returned

## 2. Verify protected signature storage
After a successful request, the signature is stored under:

`storage/signatures/<client-id>/`

The storage directory is blocked from direct Apache access by `.htaccess`.

## 3. Verify audit details
Check `contract_audits` for:
- signer legal name
- signature path
- signature SHA-256
- signer IP address
- user-agent
- execution timestamp
- certificate hash
- certificate JSON

## 4. Verify contract status
Check `contracts.status`.

It must be:

`Executed`

## 5. Verify welcome notification
Check `notifications`.

A `client_welcome` notification is created after execution.

## 6. Replay protection
Submitting the same contract token again after execution should return HTTP `409` with:

`This contract has already been executed.`

## 7. Invalid payload tests
The endpoint rejects:
- missing required fields
- invalid JSON
- invalid token format
- invalid base64
- unsupported image type
- non-image binary data
- images larger than 2 MB
- invalid signer names
