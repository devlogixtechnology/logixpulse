# LogixPulse - Sprint 2 (30 Sep to 4 Oct)
## Epic: Client Onboarding & Digital Contract Agreement (#z8vk7p8j9b)
## Squad: Squad PHP-FE-B (Client Signing Portal) (#z8vk7p8j9j)
### Subtask: [FEB-03] Build Interactive Contract Review Screen with Terms Acceptance (#z8vk7p8j9k)
**Assignee:** Halima (halima.sadia@devlogix.online)

---

## 🎯 Subtask Objective:
Construct a frictionless, branded contract review screen allowing external clients to read agreement terms, acknowledge scope items, and input their legal signing information.

---

## ✅ Implementation Checklist Met:
1. **Branded Landing Screen & Token Support:**
   - Accepts and validates signing token from URL parameter (`?token=lp_sec_904a` or default).
   - Displays authorized token badge on contract header.

2. **Formatted Contract Terms & Scroll Tracking:**
   - Formatted Master Services Agreement clauses (Scope of Work, Payment Terms, Confidentiality & NDA, Governing Law).
   - Real-time scroll tracking on `#contractTermsScrollbox` verifying client scrolls through terms before signing unlocks.
   - Visual scroll indicator badge (`#termsScrollIndicator`).

3. **Mandatory Legal Acknowledgment Checkboxes:**
   - `#chkScope` (Project Scope of Work & Milestones).
   - `#chkPayment` (Payment Schedule & Invoicing Terms).
   - `#chkConfidentiality` (Mutual Confidentiality & NDA).

4. **Embedded Signature Canvas Pad:**
   - Smooth mouse, stylus, and touch curve rendering with `lineCap = 'round'` and `lineWidth = 2.5`.
   - Clear Pad button (`#clearSignatureBtn`).
   - Legal confirmation disclaimer under ESIGN Act and UETA compliance.

5. **Submit Button with Loading Spinner:**
   - `#submitSignatureBtn` remains disabled until all 3 checkboxes are checked AND signature is drawn.
   - Shows loading spinner (`Encrypting & Executing Agreement...`) during upload to `api/submit_signature.php`.

---

## 📂 Subtask Deliverable Files:
* `api/submit_signature.php` — Backend signature upload, file persistence, and audit logging.
* `js/signature.js` — Token handler, scroll tracker, checkbox validator, and canvas pad engine.
* `css/style.css` — Figma design styles for review screen and pad.
* `index.html` — Working review screen module.

---

## 🚀 Recommended Git Branch & Commit:
* **Branch Name:** `feature/FEB-03-contract-review-terms-acceptance` (or `FEB-03-contract-review`)
* **Commit Message:** `feat(FEB-03): build interactive contract review screen with terms acceptance and canvas signature`
