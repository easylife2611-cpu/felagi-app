# Compliance Assessment — Ethiopian Proclamation 1321/2024

**Date:** 2026-10-01
**HEAD:** e23696d (L300)
**Legal framework:** Proclamation No. 1321/2024 (Personal Data Protection)
**Supervisory authority:** Ethiopian Communications Authority (ECA)

## 1. Executive Summary

Felagi has implemented the technical and documentation measures required by Proclamation 1321/2024. This assessment maps each article of the law to its implementation in the codebase.

| Category | Implemented | External |
|---|---|---|
| Consent & lawful basis (Art. 7-8) | ✅ | — |
| Privacy policy (Art. 13-14) | ✅ | — |
| Retention (Art. 18) | ✅ | — |
| DPIA (Art. 26) | ✅ | — |
| DPO (Art. 27) | ✅ doc | ⏳ appointment |
| Cross-border (Art. 28-31) | ✅ | ⏳ ECA auth |
| Breach (Art. 30) | ✅ | ⏳ ECA contact |
| Data subject rights (Art. 33-40) | ✅ | — |
| ECA registration (Art. 6) | — | ⏳ company action |

## 2. Article-by-Article Mapping

### Article 6 — Registration with ECA

- **Status:** ⏳ External (company action required)
- **Implementation:** None (regulatory registration)
- **Evidence:** N/A
- **Action:** File registration with ECA

### Article 7-8 — Lawful basis and consent

- **Status:** ✅ Implemented
- **Implementation:**
  - `app/Models/ConsentLog.php`
  - `app/Services/Consent/ConsentService.php`
  - `app/Http/Controllers/V1/ConsentController.php`
  - Migration: `consent_logs` table
  - Routes: `/api/v1/consent`, `/grant`, `/revoke`
- **Evidence:**
  - `tests/Feature/Privacy/ConsentLogTest.php` (6 tests)
- **Coverage:** Marketing, ads, AI compare, Telegram, cross-border

### Article 13-14 — Privacy policy and transparency

- **Status:** ✅ Implemented
- **Implementation:**
  - `docs/privacy/PRIVACY_POLICY.md`
  - `docs/privacy/TERMS_OF_SERVICE.md`
- **Evidence:** Documented in Ledger L298

### Article 18 — Data retention

- **Status:** ✅ Implemented
- **Implementation:**
  - `docs/privacy/DATA_RETENTION_POLICY.md`
- **Coverage:** Account, needs, offers, messages, ratings, payments, audit logs, ads, sessions, auth, consents

### Article 26 — DPIA

- **Status:** ✅ Implemented
- **Implementation:**
  - `docs/privacy/DPIA_REPORT.md`
- **High-risk activities assessed:** AI (Gemini), Payments, Sponsored Ads

### Article 27 — DPO

- **Status:** ✅ Doc + ⏳ Appointment
- **Implementation:**
  - `docs/privacy/DPO_APPOINTMENT.md`
  - Contact: dpo@felagi.et
- **Action:** Formally appoint DPO

### Article 28-31 — Cross-border transfer

- **Status:** ✅ Doc + ⏳ ECA auth
- **Implementation:**
  - `docs/privacy/CROSS_BORDER_TRANSFER.md`
- **Transfers:** Telegram (RU/UAE), Gemini (USA), Payment (ET)
- **Action:** File ECA pre-authorization for sensitive categories

### Article 30 — Breach notification (72 hours)

- **Status:** ✅ Implemented
- **Implementation:**
  - `app/Models/BreachIncident.php` (P1-P4, 72h logic)
  - `app/Services/Privacy/BreachNotificationService.php`
  - `app/Http/Controllers/V1/BreachController.php` (8 endpoints)
  - `app/Console/Commands/CheckBreachDeadlines.php`
  - `app/Mail/BreachNoticeMail.php` (am + en)
  - Migration: `breach_incidents` table
- **Evidence:**
  - `tests/Feature/Privacy/BreachNotificationTest.php` (10 tests)

### Article 33-40 — Data subject rights (8 rights)

- **Status:** ✅ Implemented
- **Implementation:**
  - `app/Http/Controllers/V1/PrivacyController.php`
  - `app/Services/Privacy/DataRightsService.php`
  - `app/Services/Privacy/DataExportService.php`
  - Migration: `data_requests` table
  - Routes: 6 endpoints
- **Evidence:**
  - `tests/Feature/Privacy/DataRightsTest.php` (8 tests)

| Right | Article | Endpoint | Status |
|---|---|---|---|
| Information | 33 | Documentation | ✅ |
| Access | 34 | GET /api/v1/privacy/data | ✅ |
| Rectification | 35 | PATCH /api/v1/privacy/data | ✅ |
| Erasure | 36 | DELETE /api/v1/privacy/data | ✅ |
| Restriction | 37 | POST /api/v1/privacy/restrict | ✅ |
| Portability | 38 | GET /api/v1/privacy/export | ✅ |
| Objection | 39 | POST /api/v1/privacy/object | ✅ |
| No automated decision | 40 | Appeal via dpo@felagi.et | ✅ |

## 3. Test Coverage

| Suite | Tests | Status |
|---|---|---|
| ConsentLogTest | 6 | ✅ |
| DataRightsTest | 8 | ✅ |
| EmailAuthTest | 8 | ✅ |
| BreachNotificationTest | 10 | ✅ |
| **Compliance suite** | **32** | ✅ |

Full suite: **1021 passed / 1 skipped / 0 failures**

## 4. External Actions Required

| # | Action | Owner | Legal basis |
|---|---|---|---|
| 1 | ECA registration | Company | Art. 6 |
| 2 | DPO appointment | Board | Art. 27 |
| 3 | ECA cross-border authorization | Company | Art. 28-31 |
| 4 | ECA breach contact registration | Company | Art. 30 |
| 5 | DB localization audit | Infrastructure | Art. 31 |
| 6 | Gemini DPA negotiation | Legal | Art. 28 |

## 5. Evidence Boundary

- **Published ≠ Applied** — merged code is not running policy
- **Applied ≠ Verified** — running policy is not externally proven
- Design coverage is not implementation evidence
- External actions require external actors

## 6. Sign-off

**Developer side:** ✅ Complete for all code-implementable requirements.

**Outstanding:** 6 external actions (regulatory, legal, infrastructure).

**Generated by:** L301
