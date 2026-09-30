# WP-05c — Admin Read Endpoints (LOCKED)

**Date:** 2026-09-30
**Status:** ✅ LOCKED — evidence-based (no guessing)
**Sources:**
- `System_Specification/Admin_Authorization_Contract.md` (canonical 1.3 + HTTP 1.4)
- `Design_Data/admin.json` (per-screen columns + control_ids)
- `Design_Data/api-mappings.json` (endpoint names)
- `app/Http/Controllers/Api/V1/BaseApiController.php` (response envelope)
- `app/Policies/SettingPolicy.php` (role pattern)

---

## 1. Resources (23 admin screens)

All A001–A023 from SOURCE_OF_TRUTH.md:

| Screen | Endpoint | Columns | control_ids (write-only) |
|---|---|---|---|
| A001 | GET /api/v1/admin/dashboard | count, incident, checkedAt | — |
| A002 | GET /api/v1/admin/telegram | destination, permissionProof, rights, caps, quietHours, queue | telegram, telegramDestination |
| A003 | GET /api/v1/admin/health | component, status, latency, checkedAt | — |
| A004 | GET /api/v1/admin/features | fieldSettingKey, effective, dependencies, version | allowNeeds, allowOffers, messaging, aiCompare |
| A005 | GET /api/v1/admin/marketplace | record, reportType, assigned, resolution | maxOffers |
| A006 | GET /api/v1/admin/ai | criteriaVersion, promptVersion, model, caps, cost | (AI-specific) |
| A007 | GET /api/v1/admin/payments | record, amount, fieldCurrency, providerReference, status | payments, reconcilePayment |
| A008 | GET /api/v1/admin/users | maskedIdentity, status, privileged, reviewDate | suspendUser |
| A009 | GET /api/v1/admin/content | contentKey, language, version, updated | welcomeText, supportText, helpText, policyText, noticeText |
| A010 | GET /api/v1/admin/notifications | jobClass, status, attempt, retryAfter | notificationRetry |
| A011 | GET /api/v1/admin/files | record, purposeField, quarantine, retention | fileQuarantine |
| A012 | GET /api/v1/admin/jobs | jobClass, status, attempt, retryAfter | retryLimit, queueConcurrency, retryJob |
| A013 | GET /api/v1/admin/backups | updated, scope, checksum, encrypted, restoreEvidence | verifyBackup |
| A014 | GET /api/v1/admin/integrity | scope, status, findings, checkedAt | checkIntegrity |
| A015 | GET /api/v1/admin/security | session, secretRef, checkedAt | rateLimit, paymentSecret, aiSecret, telegramSecret |
| A016 | GET /api/v1/admin/audit | actor, purposeField, requestId, beforeAfter, version | — |
| A017 | GET /api/v1/admin/settings | fieldSettingKey, effective, risk, version | freeze |
| A018 | GET /api/v1/admin/recovery | safeMode, dependencies, callbacks, paidPending | restoreBackup, diagnose |
| A019 | GET /api/v1/admin/safe-mode | status, version, updated | safeMode |
| A020 | GET /api/v1/admin/monetization | status, version, updated | boost, offerUnlock, offerFee, offerCurrency, boostPackages |
| A021 | GET /api/v1/admin/maintenance | status, version, updated | maintenance, maintenanceText, refreshCache |
| A022 | GET /api/v1/admin/reports | status, version, updated | reviewReport |
| A023 | GET /api/v1/admin/ads | campaignName, status, impressions, clicks | adsEnabled, adsBrowse, adsSearch, adsNeedDetail, adsSeparation, adsSessionCap, adsCampaignPublish, adsCampaignPause, adsDestination |

---

## 2. Authorization (LOCKED)

**From Admin_Authorization_Contract.md:**

- Capability-based: `admin.view.<area>` authorizes read
- **Deny by default**
- Checked server-side on every request
- Applied **BEFORE search, rows, pagination, export, or counts**
- Role alone does not grant capability
- MAIN_ADMIN > ADMIN > MODERATOR
- Secrets: never plaintext in API/audit/logs/diff
- Failed denied attempts = security events

**Policy class:** `AdminReadPolicy` (new) — mirror `SettingPolicy` pattern

---

## 3. Pagination (LOCKED)

From `admin.json.filtering`:
- **Offset-based** (`?page=N&per_page=N`)
- **Default:** 25 rows
- **Maximum:** 100 rows
- **Auth before pagination** — non-authorized rows never counted

---

## 4. Filter / Sort / Search (LOCKED)

From `admin.json.filtering`:
- **Search:** free text (per-screen allowlist)
- **Filters:** status + date (allowlisted per screen)
- **No "select all"** silently means all DB records
- **Export:** redacts private info per DFM policy

---

## 5. Field-level Authorization (LOCKED)

- Every read endpoint filters fields by `admin.view.<area>` capability
- Secret fields → `admin.view.secrets` (MAIN_ADMIN only)
- Masked fields (e.g., A008 `maskedIdentity`) — already masked in payload
- Backend applies scope **before** count/pagination

---

## 6. Response Envelope (LOCKED)

Existing `BaseApiController.success()`:

```json
{
  "success": true,
  "data": [...],
  "message": "",
  "request_id": "uuid-v4",
  "meta": {
    "total": 250,
    "page": 1,
    "per_page": 25,
    "last_page": 10
  }
}

---

## 7. Audit (LOCKED)

Per Admin_Authorization_Contract.md:
> Permission audit includes actor, effective capabilities, target scope,
> request/correlation ID, reason, before/after versions, confirmation digest
> and timestamp. Failed denied attempts are security events.

Each read logs to AuditLog with:
- actor_id, action=admin.read.AREA, entity_type, request_id
- safe_metadata includes scope + filter digest

---

## 8. Implementation Plan

| Component | Files | Notes |
|---|---|---|
| Controller | 1 (AdminReadController) | 23 methods, one per screen |
| Routes | 23 in existing admin group | GET only |
| Policy | 1 (AdminReadPolicy) | mirrors SettingPolicy |
| Request | 1 (AdminListRequest) | pagination + filter validation |
| Resource | 23 transformers | optional; inline if simple |
| Tests | 3-5 files | ~30-40 tests |

Recommended: 1 controller + 23 methods (matches WP-13 style)

---

## 9. Tests Plan

- 23 endpoints × 3 roles (MAIN_ADMIN / ADMIN / MODERATOR) = 69 auth tests
- 23 endpoints × pagination (25/100 over-limit) = 23 pagination tests
- Filter/search per screen = 23 filter tests
- Audit verification = 5 tests
- Field-level auth (secrets) = 5 tests

Total: ~125 tests

---

## 10. Decision Required

- [ ] APPROVE — implement as specified
- [ ] MODIFY — list changes below
- [ ] DEFER — keep BLOCKED_ON_UNKNOWN

Changes requested:
_______________________

Approved by: ______________
Date: ______________
