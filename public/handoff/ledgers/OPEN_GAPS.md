# OPEN GAPS — Felagi v1.4.2
Last Updated: 2026-09-29

## Critical Blockers
| ID | Gap | Severity | Owner |
|----|-----|----------|-------|
| GAP-08 | Positive-fee activation | CRITICAL | product ops |
| GAP-09 | Production PASS | CRITICAL | release owner |
| GAP-26 | cPanel doc root capability | CRITICAL | hosting admin |

## High Severity
| ID | Gap | Blocker |
|----|-----|---------|
| GAP-01 | Browser/device/AT | No browser |
| GAP-02 | Flutter compilation | No SDK |
| GAP-03 | Live services | No creds |
| GAP-05 | Marketplace baseline | No data |
| GAP-07 | Observability | No telemetry |
| GAP-10 | AI live | No provider |
| GAP-11 | Payment live | No provider |
| GAP-22 | Responsive 46-screen | No browser |
| GAP-23 | A11y 46-screen | No AT |
| GAP-24 | Runtime states | No stack |
| GAP-25 | Error recovery | No stack |
| GAP-27 | PHP/MySQL extensions | No host access |
| GAP-28 | Queue throughput | No host |
| GAP-29 | Backup RPO/RTO drill | No backup target |
| GAP-30 | Webhook signature | No sandbox |
| GAP-31 | Telegram OIDC creds | No Telegram app |

## Medium Severity
| ID | Gap | Blocker |
|----|-----|---------|
| GAP-04 | Amharic runtime | No reviewer |
| GAP-06 | Ads live serving | No infra |
| GAP-12 | Ads media scanning | No scan |
| GAP-13 | SSRF validation | No infra |
| GAP-14 | Ads analytics | No analytics |
| GAP-20 | Font glyph coverage | No license/device |
| GAP-21 | Amharic QA | No reviewer |
| GAP-32 | cron PHP path | No host access |

## Findings (N06-N10)
| ID | Severity | Status | Owner |
|----|----------|--------|-------|
| N06 | HIGH | BLOCKED | frontend/a11y QA |
| N07 | HIGH | BLOCKED | Flutter team |
| N08 | HIGH | REQUIRES_EVIDENCE | backend/security QA |
| N09 | MEDIUM | PARTIAL | Amharic/UX reviewers |
| N10 | HIGH | REQUIRES_EVIDENCE | product operations |

## Environment Findings (ENV-01 to ENV-05)
| ID | Gap | Impact |
|----|-----|--------|
| ENV-01 | Python 3.6.8 too old | Tools/*.py fail |
| ENV-02 | Node.js missing | Tools/*.js fail |
| ENV-03 | No local Flutter/Dart | WP-04 blocked |
| ENV-04 | No local browser | WP-03 blocked |
| ENV-05 | cPanel doc root not verified | WP-21 unknown |

## UNKNOWN Items: ~85 total

## WP-13 Updates (2026-09-29)

### Resolved in WP-13

| ID | Description | Resolution |
|----|-------------|------------|
| GAP-60 | OutboxEvent model missing | ✅ RESOLVED — created (app/Models/OutboxEvent.php) |
| GAP-34 | .env.testing missing (phpunit used production DB) | ✅ RESOLVED — MySQL test DB created (zagcreht_felagi_test) |
| GAP-36 | UserFactory schema mismatch (name/email/password) | ✅ RESOLVED — aligned to telegram_subject/full_name |
| GAP-40 | Missing directories (7) | ✅ RESOLVED — app/Exceptions, app/Services/Admin, app/Policies, etc. |
| GAP-44 | authorize() broken (empty Controller base) | ✅ RESOLVED — AuthorizesRequests trait added to BaseApiController |

### New GAPs (from WP-13)

| ID | Gap | Severity | Owner | Blocker |
|----|-----|----------|-------|---------|
| GAP-45 | Reauth (5-min window) not implemented | HIGH | WP-13b | users.recently_authenticated_at column missing |
| GAP-46 | Second factor (CRITICAL) not implemented | HIGH | WP-13b | 2FA infrastructure missing |
| GAP-47 | Idempotency-Key header ignored | MEDIUM | WP-13b | Storage strategy TBD |
| GAP-48 | Apply/Verify workflow not implemented | MEDIUM | WP-13b | Requires runtime version service |
| GAP-49 | Rollback E2E test not written | LOW | WP-13 | Test only; impl complete |

### Pre-existing GAPs still open (not WP-13 scope)

| ID | Description | Severity |
|----|-------------|----------|
| GAP-01 | Browser/device/AT testing | HIGH |
| GAP-02 | Flutter compilation | HIGH |
| GAP-03 | Live services (payment, AI, Telegram) | HIGH |
| GAP-05 | Marketplace baseline data | HIGH |
| GAP-08 | Positive-fee activation | CRITICAL |
| GAP-09 | Production PASS | CRITICAL |
| GAP-26 | cPanel doc root capability | CRITICAL |
| GAP-38 | APP_DEBUG=true in production | CRITICAL |
| GAP-42 | auth_attempts table missing | ✅ RESOLVED 2026-09-29 (WP-05a) | — | Full PKCE flow |
| GAP-43 | DatabaseSeeder schema mismatch | HIGH |

## WP-13b Updates (2026-09-29)

### Resolved in WP-13b

| ID | Description | Resolution |
|----|-------------|------------|
| GAP-45 | Reauth (5-min window) not implemented | ✅ RESOLVED — ReauthValidator + users.recently_authenticated_at |
| GAP-46 | Second factor (CRITICAL) not implemented | ✅ RESOLVED — TotpService (RFC 6238) + requireFor() |
| GAP-47 | Idempotency-Key header ignored | ✅ RESOLVED — IdempotencyRegistry + middleware |
| GAP-48 | Apply/Verify workflow not implemented | ✅ RESOLVED — ProcessOutboxEvent + VerifySettingChange jobs |

### New GAPs (from WP-13b)

| ID | Gap | Severity | Owner | Blocker |
|----|-----|----------|-------|---------|
| GAP-50 | TOTP enrollment UI (QR + verify) | MEDIUM | WP-13c | Frontend not implemented |
| GAP-51 | Recovery codes display UI | MEDIUM | WP-13c | Frontend not implemented |
| GAP-52 | Self-service 2FA disable | MEDIUM | WP-13c | Frontend + audit flow |
| GAP-53 | Recovery flow (lost factor) | HIGH | WP-13c | Design: "controlled, audited process" |
| GAP-54 | APP_DEBUG=true in production | ✅ RESOLVED 2026-09-29 | — | Config cached + verified |

### Notes

**GAP-50, GAP-51, GAP-52** — 2FA infrastructure አለ (TOTP service + recovery codes). ሆኖም ተጠቃሚ UI የለም — Flutter/Admin frontend ያስፈልጋል. WP-13c ይሸፍናል።

**GAP-53** — Auth Contract §449: "lost-factor recovery is a controlled, audited process, not a secret bypass." ሂደቱ ግን በ design አልተገለጸም።

## WP-27 Updates (2026-09-29)

### Resolved in WP-27

| ID | Description | Resolution |
|----|-------------|------------|
| GAP-31 | Telegram OIDC credentials missing | ✅ RESOLVED — credentials configured |

### New GAPs (from WP-27)

| ID | Gap | Severity | Owner | Blocker |
|----|-----|----------|-------|---------|
| GAP-56 | Live OIDC end-to-end test with real Telegram user | MEDIUM | QA | manual test required |
| GAP-57 | Sanctum UUID migration in production | ✅ DONE | — | applied |

## WP-27b Updates (2026-09-29)

### Resolved in WP-27b

| ID | Description | Resolution |
|----|-------------|------------|
| D-091 | Race condition in telegramExchange | ✅ RESOLVED — HMAC-signed handoff |

## B13 Updates (2026-09-29)

### Resolved in B13
| ID | Gap | Status | Evidence |
|----|-----|--------|----------|
| GAP-61 | downloads/ directory listing 403 | ✅ RESOLVED — static index.html added | HTTP/2 200 verified 2026-09-29 |

**Note:** Originally misidentified in B10 CHANGE_LOG as "GAP-56". Real GAP-56 is the separate OIDC E2E test gap (still open). Downloads listing was never formally registered until B13.

## B14 Updates (2026-09-29)

### New UNKNOWN
| ID | Gap | Severity | Owner | Notes |
|----|-----|----------|-------|-------|
| GAP-62 | Production root (zagcreativity.com/) shows Laravel default | UNKNOWN | design owner | LOCKED design for production landing page does not exist. Per Constitution: UNKNOWN ≠ MISSING. Awaiting design owner direction. |

**Note:** This issue was mislabeled as "GAP-57" in B10 CHANGE_LOG. Real GAP-57 = Sanctum UUID migration (DONE). Never formally registered until B14.

## B15 Updates (2026-09-29)

### Resolved in B15
| ID | Gap | Status | Evidence |
|----|-----|--------|----------|
| GAP-62 | Production root (zagcreativity.com/) shows Laravel default | ✅ RESOLVED — S001 Welcome deployed | HTTP/2 200 · `<title>መግቢያ — ፈላጊ</title>` verified 2026-09-29 |

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)

### GAP State Reconciliation

The following GAP clarifications are documented here in append-only form.
Original GAP entries above are preserved (no silent edits).

### GAP-38 — SUPERSEDED by GAP-54

| Field | Value |
|-------|-------|
| GAP-38 | APP_DEBUG=true in production (originally CRITICAL) |
| GAP-54 | Same issue — RESOLVED 2026-09-29 |
| **Status** | ✅ **SUPERSEDED** — GAP-38 duplicates GAP-54 |
| **Action** | GAP-38 should be considered closed via GAP-54 |

### GAP-07 vs GAP-60 — Clarification

| GAP | Issue | Status |
|-----|-------|--------|
| GAP-07 | Observability (no telemetry) | OPEN (blocked on telemetry env) |
| GAP-60 | OutboxEvent model missing | ✅ RESOLVED (WP-13, app/Models/OutboxEvent.php) |

**Note:** B10 CHANGE_LOG incorrectly described a "GAP-07 → GAP-60 rename".
These are **two distinct GAPs** with different issues. No rename occurred.

### GAP-42 — Resolved but Listed in Open Section

| Field | Value |
|-------|-------|
| GAP-42 | auth_attempts table missing |
| Resolution | ✅ RESOLVED 2026-09-29 (WP-05a — Full PKCE flow) |
| **Current Location** | Listed in "Pre-existing GAPs still open" table |
| **Action** | Marked RESOLVED in-place; no longer an active blocker |

### Summary of B17 GAP Status

| Status | Count | GAPs |
|--------|-------|------|
| SUPERSEDED | 1 | GAP-38 (→ GAP-54) |
| RESOLVED | 3 | GAP-42, GAP-54, GAP-60 |
| OPEN (clarified) | 1 | GAP-07 (Observability) |

### Constitution Compliance

- No silent changes — all 5 GAPs documented here
- No duplicate ownership — GAP-38/GAP-54 clarified
- UNKNOWN ≠ MISSING — GAP-07 remains OPEN (blocked, not deleted)

---

## GAP-70 — Ledger Drift: B17-B24 not recorded in canonical ledgers

**Type:** Documentation integrity / Constitution compliance
**Severity:** HIGH (affects handoff state, not production code)
**Opened:** 2026-09-30

**Evidence of drift:**

| Ledger | Last Entry | HEAD (143a756=B24) |
|---|---|---|
| IMPLEMENTATION_LEDGER | L216 (B16) | B24 → gap B17-B24 |
| WORK_PACKAGES (B-blocks) | B17 | B24 → gap B18-B24 |
| CHANGE_LOG | B21 | B24 → gap B22-B24 |
| TEST_VERIFICATION | B21 | B24 → gap B22-B24 |
| HANDOFF_STATE | B22 | B24 → gap B23-B24 |

**Cause:** B17-B24 code was committed to git but ledger entries were not appended. Partial documentation exists in HANDOFF_STATE (B18, B20, B22 bundle refresh entries).

**Required action (next session):**

1. `git log --oneline 143a756 ^6ea8a97` → list B17-B24 commits
2. For each commit, extract changes → IMPLEMENTATION_LEDGER L217-L224
3. Update WORK_PACKAGES B-blocks table (B18-B24)
4. Backfill CHANGE_LOG + TEST_VERIFICATION
5. Produce bundle refresh at B25_DONE

**Do NOT guess** entries. Each must be traceable to a git commit + evidence.

**Constitution reference:**
- Art. "Maintain one canonical project ledger"
- Art. "No hidden work"
- Art. "No silent changes"

**Status:** OPEN — deferred to next session

---

## GAP-71 — Production Bug: Attachment::isClean() name collision

**Type:** Production bug (pre-existing)
**Severity:** HIGH (blocks B27 tests; method unusable)
**Opened:** 2026-09-30

**Evidence:**

Attachment.php line 74 declares `public function isClean(): bool`.
Laravel's base `Model` class declares `public function isClean($attributes = null)`.
PHP raises TypeError: "Declaration of Attachment::isClean(): bool must be
compatible with Model::isClean($attributes = null)".

**Impact:**
- Every Attachment instantiation triggers TypeError.
- isClean() cannot be called.
- Latent because no Attachment tests existed until B27.

**Required action (with explicit approval — BREAKING change):**

Rename Attachment::isClean() to Attachment::isScanClean():

1. Update app/Models/Attachment.php (line 74).
2. Add CHANGE_LOG entry (public API change).
3. Update any callers (grep currently: none).
4. Re-run tests.

**Constitution reference:**
- Art. "Do not perform destructive, security-sensitive, breaking or
  architecture-changing work without explicit approval".
- This IS a breaking change -> requires approval.

**Status:** RESOLVED — fixed in B27 (2026-09-30)
**Closed by:** B27 — see CHANGE_LOG + L219

---

## GAP-70 — RESOLVED (2026-09-30) — Ledger Drift: B17-B24

**Type:** Documentation integrity / Constitution compliance
**Opened:** 2026-09-30
**Status:** RESOLVED (2026-09-30)

**Original issue:**
B17–B24 code existed in git but was not recorded in canonical ledgers
(IMPLEMENTATION_LEDGER, TEST_VERIFICATION, CHANGE_LOG, WORK_PACKAGES).

**Resolution:**
- IMPLEMENTATION_LEDGER: L221–L227 appended (B18–B24)
- TEST_VERIFICATION: B18/B20/B22/B23/B24 "no test changes" note
  (B19 + B21 already documented in original location)
- CHANGE_LOG: B18/B20/B22/B23/B24 entries appended
  (B19 + B21 already documented in original location)
- HANDOFF_STATE: B18–B24 backfill summary added
- WORK_PACKAGES: B18-B24 marked DONE (was DEFERRED)

**Evidence:**
Git log range 6ea8a97..143a756 (10 commits) now fully documented in
canonical ledgers.

**Constitution compliance:**
- Art. "Maintain one canonical project ledger" — restored
- Art. "No hidden work" — B18-B24 now visible
- Art. "No silent changes" — backfill explicitly marked
- Art. "No duplicate ownership" — existing B19/B21 entries preserved

**Closed by:** Backfill commit (this commit)

---

## GAP-71b — RESOLVED (2026-09-30) — Attachment factory infrastructure

**Type:** Infrastructure gap (deferred from B27)
**Opened:** 2026-09-30 (noted during B27)
**Status:** RESOLVED (2026-09-30)

**Issue:** AttachmentTest used `::create()` directly. No factory existed
for future tests that might want factory-style setup.

**Resolution:**
- NEW database/factories/AttachmentFactory.php
- HasFactory trait added to Attachment model
- States: clean(), rejected(), publicVisibility(), forNeed()
- Smoke tested via tinker
- Existing tests unchanged (still use ::create())

**Closed by:** GAP-71b commit (this commit)

---

## GAP-WP-05c — Admin Read Endpoints Spec (BLOCKED_ON_UNKNOWN)

**Type:** Spec gap (UNKNOWN, not MISSING)
**Severity:** MEDIUM (blocks WP-05c only)
**Opened:** 2026-09-30

**Issue:**
- Spec request exists: `docs/spec-requests/WP-05c_admin_read_endpoints.md`
- Status: PENDING STAKEHOLDER
- 6 UNKNOWN questions (see spec request)
- WP not registered in WORK_PACKAGES.md until 2026-09-30

**Constitution constraint:**
- Art. "Do not guess missing requirements" — no implementation without LOCKED spec
- Art. "UNKNOWN != MISSING" — registered as UNKNOWN

**Action required:**
Stakeholder to answer 6 questions + provide LOCKED spec.

**Status:** ✅ RESOLVED (2026-09-30) — via L262 (commit 7cec529)

**Resolution:** Evidence-based spec extracted from Felagi_Design_Package
(System_Specification/Admin_Authorization_Contract.md + Design_Data/admin.json).
23 endpoints implemented. 37 tests added. Full suite: 736/1753/0/0.

---

## GAP-71c — RESOLVED (2026-09-30) — Remaining factories

**Type:** Infrastructure gap
**Opened:** 2026-09-30
**Status:** RESOLVED

**Issue:** Only UserFactory + AttachmentFactory existed.

**Resolution:**
- 14 new factories: Category, Need, Payment, Boost, BoostPackage,
  PaymentEvent, Offer, Comparison, ComparisonOffer, ComparisonResult,
  ComparisonAttempt, NeedAward, Report, Setting, SettingDraft, UserRole
- HasFactory trait added to 16 models
- Smoke tested all 14 factories

**Note (corrected 2026-09-30 — L258):**
- Message + Notification were already covered by S013+S018 factories.
- Remaining 8 (AuditLog, AuthAttempt, OutboxEvent, Rating, SettingVersion,
  TelegramDestination, TelegramPublication, TelegramPublicationEvent)
  were completed in L258 (commit `7581e3d`).
- Final state: **29 factories · 29 models with HasFactory · 100%**

**Closed by:** GAP-71c commit + L258 (7581e3d)


---

## GAP-64-REOPENED — RESOLVED (2026-09-30) — OIDC not available

**Type:** Production bug (B24 fix incomplete)
**Opened:** 2026-09-30 (user report)
**Status:** RESOLVED via Widget flow

**Issue:**
B24 fix added bot_id but client_id remained numeric bot ID.
Telegram served Widget page instead of OIDC. Callback failed with
"Missing state or code".

**Evidence:**
- BotFather: "Web login is currently unavailable for Felagi @FelagiMarketBot"
- Only Login Widget available

**Resolution:**
- Telegram Widget flow (HMAC verification)
- 12 tests added
- Full suite: 424 tests

**Preserved:**
- OIDC code path kept (DEFERRED)

**Closed by:** WIDGET-FLOW commit


---

## GAP-S003-PHOTO — DEFERRED — profile_photo upload

**Type:** UNKNOWN (design spec requires, backend missing)
**Opened:** 2026-09-30
**Status:** DEFERRED

**Issue:** S003 spec requires `profile_photo` file upload (0–server-bound).
Backend has no file upload endpoint.

**Design ref:** Product_Design/Final_Screen_by_Screen_Specifications.md → S003 Fields

**Resolution path:**
- Backend: add POST /api/v1/profile/photo (multipart)
- Storage: filesystem disk
- Frontend: file input + preview

**Not guessed — explicitly deferred.**

## GAP Resolutions (2026-09-30)

- ✅ GAP-S003-PHOTO — RESOLVED via L253
- ✅ GAP-S019-BOOST-API — RESOLVED via L254
- ✅ GAP-S021-REPORT-API — RESOLVED via L255
- ✅ GAP-S022-TELEGRAM-API — RESOLVED via L256
- ✅ GAP-S023-UNLOCK-API — RESOLVED via L257

---

## GAP Reconciliation — 2026-10-03

After L336, the following GAPs are confirmed RESOLVED (some appear
duplicated in earlier tables — this section is the authoritative status).

### ✅ RESOLVED (evidence-anchored)

| GAP | Status | Evidence |
|---|---|---|
| GAP-07 | OPEN (blocked) | No telemetry env |
| GAP-31 | RESOLVED | Telegram OIDC credentials configured |
| GAP-38 | SUPERSEDED | → GAP-54 |
| GAP-42 | RESOLVED | WP-05a auth_attempts |
| GAP-43 | RESOLVED | WP-13 UserFactory patch (7b97c36) |
| GAP-45 | RESOLVED | WP-13b ReauthValidator |
| GAP-46 | RESOLVED | WP-13b TotpService (RFC 6238) |
| GAP-47 | RESOLVED | WP-13b IdempotencyRegistry |
| GAP-48 | RESOLVED | WP-13b ProcessOutboxEvent + VerifySettingChange |
| GAP-54 | RESOLVED | 2026-09-29 config cached |
| GAP-57 | DONE | Sanctum UUID migration applied |
| GAP-60 | RESOLVED | WP-13 OutboxEvent model |
| GAP-61 | RESOLVED | downloads/index.html (HTTP 200) |
| GAP-62 | RESOLVED | B15 S001 Welcome deployed |

### 🔴 Still OPEN

| GAP | Blocker |
|---|---|
| GAP-01 | Browser/device/AT testing (partial — see L320/L293) |
| GAP-02 | Flutter compilation (SDK required) |
| GAP-03 | Live services (payment, AI, Telegram) |
| GAP-04 | Amharic runtime (native reviewer) |
| GAP-05 | Marketplace baseline data |
| GAP-06 | Ads live serving (infra) |
| GAP-07 | Observability (telemetry env) |
| GAP-08 | Positive-fee activation (product ops) |
| GAP-09 | Production PASS (release owner) |
| GAP-12 | Ads media scanning (AV) |
| GAP-13 | SSRF validation (infra) |
| GAP-14 | Ads analytics (telemetry) |
| GAP-20 | Font glyph coverage (license) |
| GAP-21 | Amharic QA (native reviewer) |
| GAP-26 | cPanel doc root (hosting admin) |
| GAP-30 | Webhook signature (sandbox) |
| GAP-32 | cron PHP path (host access) |
| GAP-50/51/52 | TOTP UI (WP-13c, design unclear) |
| GAP-53 | Recovery flow (design: "controlled process") |
| GAP-56 | Live OIDC E2E (manual test) |

### 🔴 NEW (from L336)

| GAP | Description | Severity |
|---|---|---|
| GAP-L336 | Admin screen deep QA blocked — Playwright session auth | MEDIUM |

**Note:** GAP-L336 blocks completion of G04 admin screen matrix.


---

## GAP-LOCALE-01 — RESOLVED (2026-10-03, L338)

| Field | Value |
|-------|-------|
| Original | Accept-Language ignored; UI always English |
| Severity | HIGH (design violation — Localization/README.md) |
| Fix | `SetLocale` middleware + config default `'am'` |
| Evidence | LocaleSwitchTest 9/9, Localization QA 10/10, Localization Deep 6/6 |
| Closed by | L338 (commit e74dcaa) |

---

## GAP-L338-FIREFOX — BLOCKED (2026-10-03, L338 Block B)

| Field | Value |
|-------|-------|
| Item | Firefox cross-browser QA (46 screens) |
| Status | BLOCKED — hosting restriction |
| Root cause | `CanCreateUserNamespace() clone() failure: ENOSPC` |
| Detail | Firefox requires user namespaces; shared cPanel forbids them |
| Attempted fixes | `security.sandbox.content.level: 0` (no effect) |
| Attempted fixes | `PLAYWRIGHT_SKIP_VALIDATE_HOST_REQUIREMENTS=1` (validation bypassed; runtime crash) |
| Alternative | GTK 3 installed (57 packages); Firefox still needs namespaces |
| Resolution | Deferred — requires hosting sysctl / privileged container |
| Impact | LOW — Chromium matrix sufficient for G04 (Release_Gates) |
| Owner | Hosting / infrastructure |
| Evidence | Firefox SIGSEGV after Juggler pipe; Chromium works normally |

**Recorded:** 2026-10-03. **NOT a regression** — Chromium L337+L338 intact.
