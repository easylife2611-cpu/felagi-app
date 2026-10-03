# REQUIREMENT REGISTRY — Felagi v1.4.2
Total: ~1,700+ requirements
Last Updated: 2026-09-29

## Structure
Every requirement has:
- Unique ID (e.g., REQ-AA01)
- Source document
- Implementation status
- Verification status
- Evidence link

## Categories Summary
| Category | Count | Status |
|----------|-------|--------|
| A — Release Identity | 8 | LOCKED |
| B — Product Surface | 7 | LOCKED |
| C — Revenue Domains | 6 | LOCKED |
| D — Canonical Owners | 11 | LOCKED |
| E — Implementation Order | 10 | LOCKED |
| F — Release Gates | 9 | 3 MET / 6 PENDING |
| G — Security/Privacy | 20 | LOCKED |
| H — Draft Storage | 11 | LOCKED |
| I — Performance | 9 | NOT_MEASURED |
| J — Test Protocols | 12 | LOCKED |
| K — Backend Integration | 14 | REQUIRES_EVIDENCE |
| L — Adapter Boundary | 6 | LOCKED |
| M — AI Contract | 8 | LOCKED |
| N — Flutter Boundaries | 5 | LOCKED |
| O — Admin Boundaries | 5 | LOCKED |
| P — HTML Preview | 6 | LOCKED |
| Q — Deterministic Gen | 6 | LOCKED |
| R — Definition of DONE | 6 | LOCKED |
| S — Historical Findings | 10 | CLOSED |
| T — 1.4 Findings | 14 | 5 UNRESOLVED |
| U — Executed Verification | 10 | SOURCE PASS |
| AA — AI Evaluation | 73 | LOCKED |
| AB — Monetization | 77 | LOCKED |
| AC — Sponsored Ads | 102 | LOCKED |
| AD — Admin Auth | 29 | LOCKED |
| AE — Design Integration | 38 | LOCKED |
| AF — Product Identity | 14 | LOCKED |
| AG — Authority | 5 | LOCKED |
| AH — Screen Inventory | 6 | VERIFIED |
| AI — Navigation/Routes | 12 | LOCKED |
| AJ — User Journeys | 14 | LOCKED |
| AK — Components | 5 | LOCKED |
| AL — Tokens | 4 | LOCKED |
| AM — Runtime States | 8 | LOCKED |
| AN — Localization | 8 | LOCKED |
| AO — Accessibility | 10 | LOCKED |
| AP — Privacy/Security | 8 | LOCKED |
| AQ — Admin Detailed Specs | 38 | LOCKED |
| AR — Reconciled Bindings | 12 | LOCKED |
| AS — Screen Bindings | 46 | LOCKED |
| AT — Journey Matrix | 23 | LOCKED |
| AU — Transitions | 16 | LOCKED |
| AV — Interaction | 22 | LOCKED |
| AW — Premium UX | 53 | LOCKED |
| AX — Token Registry | 51 | LOCKED |
| AY — Component Library | 74 | LOCKED |
| AZ — State Matrix | 20 | LOCKED |
| BA — Patterns | 17 | LOCKED |
| BB — Localization | 49 | LOCKED |
| BC — Responsive | 30 | LOCKED |
| BD — Accessibility Matrix | 37 | LOCKED |
| BE — Runtime State Matrix | 28 | LOCKED |
| BF — Error Recovery | 54 | LOCKED |
| BG — Change Lifecycle | 14 | LOCKED |
| BH — Security/Privacy | 31 | LOCKED |
| BI — Reliability/Deployment | 40 | LOCKED |
| BJ — Test Matrix | 18 | LOCKED |
| BK — Traceability | 9 | LOCKED |
| BL — Acceptance Cases | 8 | LOCKED |
| BM — AI Coverage (51) | 3 | VERIFIED |
| BN — Relational Schema | 42 | LOCKED |
| BO — State Transitions | 16 | LOCKED |
| BP — HTTP API | 30 | LOCKED |
| BQ — AI Criteria v1 | 36 | LOCKED |
| BR — Roles & Permissions | 5 | LOCKED |
| BS — Settings Catalog | 26 | LOCKED |
| BT — DS Foundation | 10 | LOCKED |
| BU — UI Handoff | 17 | LOCKED |
| BV — Flutter Coverage | 15 | LOCKED |
| BW — Brand Rules | 6 | LOCKED |

## Full Detail Locations
- `Developer_Handoff/Traceability_Matrix.md`
- `Design_Data/traceability.json`
- `Design_Data/complete-requirement-coverage.json`
- `Design_Data/ai-requirement-coverage.json`
- `Design_Data/ads-requirement-coverage.json`

## WP-13 — Admin Change Lifecycle Requirements
Date: 2026-09-29
Source: Auth Contract 1.3 + HTTP Binding 1.4 + DFM-FDS-1.4 §8.2/§8.3

### Settings Registry (31 controls)
| Key | Group | Type | Risk |
|-----|-------|------|------|
| feature.needs | FEATURE | BOOLEAN | HIGH |
| feature.offers | FEATURE | BOOLEAN | HIGH |
| feature.messaging | FEATURE | BOOLEAN | HIGH |
| feature.telegram_publication | FEATURE | BOOLEAN | HIGH |
| feature.ai_compare | FEATURE | BOOLEAN | HIGH |
| feature.payments | FEATURE | BOOLEAN | HIGH |
| feature.boosts | FEATURE | BOOLEAN | HIGH |
| telegram.max_destinations_per_need | CONFIG | INTEGER | HIGH |
| telegram.daily_cap_per_destination | CONFIG | INTEGER | HIGH |
| telegram.min_interval_seconds | CONFIG | INTEGER | MEDIUM |
| telegram.max_attempts | CONFIG | INTEGER | MEDIUM |
| marketplace.max_open_needs_per_user | CONFIG | INTEGER | MEDIUM |
| marketplace.offer_deadline_max_days | CONFIG | INTEGER | MEDIUM |
| marketplace.max_offers_per_comparison | CONFIG | INTEGER | HIGH |
| ai.criteria_version | CONFIG | STRING | HIGH |
| ai.prompt_version | CONFIG | STRING | HIGH |
| ai.model_id | CONFIG | STRING | HIGH |
| ai.max_input_tokens | CONFIG | INTEGER | HIGH |
| ai.max_output_tokens | CONFIG | INTEGER | HIGH |
| ai.timeout_seconds | CONFIG | INTEGER | MEDIUM |
| ai.max_attempts | CONFIG | INTEGER | MEDIUM |
| ai.user_daily_cap | CONFIG | INTEGER | MEDIUM |
| ai.need_daily_cap | CONFIG | INTEGER | MEDIUM |
| payments.provider | SECURITY | STRING | CRITICAL |
| uploads.max_bytes | CONFIG | INTEGER | MEDIUM |
| exports.max_rows | CONFIG | INTEGER | MEDIUM |
| content.welcome_am | CONTENT | STRING | LOW |
| content.welcome_en | CONTENT | STRING | LOW |
| privacy.temp_file_days | CONFIG | INTEGER | MEDIUM |
| privacy.export_days | CONFIG | INTEGER | MEDIUM |
| system.safe_mode | SECURITY | BOOLEAN | CRITICAL |

### Change Lifecycle Requirements
| Req ID | Description | Status | Verified By |
|--------|-------------|--------|-------------|
| REQ-WP13-001 | Draft creation (POST /admin/changes) | DONE | test_can_create_draft |
| REQ-WP13-002 | Draft read (GET /admin/changes/{id}) | DONE | (implicit) |
| REQ-WP13-003 | Validation (POST /{id}/validate) | DONE | test_validate_rejects_type_mismatch |
| REQ-WP13-004 | Simulation (POST /{id}/simulate) | DONE | (implicit) |
| REQ-WP13-005 | Preview (POST /{id}/preview) | DONE | (implicit) |
| REQ-WP13-006 | Publish (POST /{id}/publish) | DONE | test_publish_creates_new_version |
| REQ-WP13-007 | Audit trail (GET /{id}/audit) | DONE | test_publish_writes_audit_log |
| REQ-WP13-008 | Rollback (POST /{id}/rollback) | DONE | (implementation complete) |
| REQ-WP13-009 | Stale version → 409 CONFLICT | DONE | test_publish_with_stale_version_returns_409 |
| REQ-WP13-010 | Dependency: Boosts requires Payments | DONE | test_dependency_blocks_boosts_on |
| REQ-WP13-011 | Outbox event emission | DONE | test_publish_writes_outbox_event |
| REQ-WP13-012 | Authorization (policy check) | DONE | test_unauthorized_returns_403 |
| REQ-WP13-013 | Legacy alias: drafts/{id}/publish | DONE | route registered |
| REQ-WP13-014 | Legacy alias: settings/{key}/rollback | DONE | route registered |
| REQ-WP13-015 | Audit hash chain (prev_hash + hash) | DONE | AuditWriter implementation |
| REQ-WP13-016 | Immutable setting_versions | DONE | append-only model |

### Deferred to WP-13b
| Req ID | Description | Reason |
|--------|-------------|--------|
| REQ-WP13-D01 | 5-min reauth window | users.recently_authenticated_at missing |
| REQ-WP13-D02 | Second factor for CRITICAL | Requires 2FA infrastructure |
| REQ-WP13-D03 | Idempotency-Key from header | Storage strategy TBD |
| REQ-WP13-D04 | Apply/Verify workflow | Requires runtime service |

### Constraint Rules (Enforced)
| Rule | Implementation |
|------|----------------|
| Reason required (min 5 chars) | PublishChangeRequest validation |
| Stale version rejected | AdminChangeService::publish throws exception |
| JSON envelope consistency | BaseApiController |
| Production DB never touched | --env=testing isolated |

## WP-13b Requirements (2026-09-29)

Source: Auth Contract §3 + DFM §149/§216/§418/§461

### Reauth Requirements

| Req ID | Description | Status | Verified By |
|--------|-------------|--------|-------------|
| REQ-WP13B-001 | 5-min reauth window for HIGH/CRITICAL | DONE | ReauthTest |
| REQ-WP13B-002 | Reauth timestamp per-user | DONE | users.recently_authenticated_at |
| REQ-WP13B-003 | 401 REAUTH_REQUIRED error | DONE | ReauthTest T07 |
| REQ-WP13B-004 | LOW/MEDIUM does not require reauth | DONE | ReauthTest T05 |

### 2FA Requirements

| Req ID | Description | Status | Verified By |
|--------|-------------|--------|-------------|
| REQ-WP13B-005 | TOTP (RFC 6238) second factor | DONE | TwoFactorTest |
| REQ-WP13B-006 | CRITICAL requires 2FA | DONE | TwoFactorTest T10 |
| REQ-WP13B-007 | Replay protection on TOTP codes | DONE | TwoFactorTest T05 |
| REQ-WP13B-008 | Recovery codes (one-time, hashed) | DONE | TwoFactorTest T06-T08 |
| REQ-WP13B-009 | Encrypted secret storage | DONE | User model encrypted cast |
| REQ-WP13B-010 | 403 TWO_FACTOR_REQUIRED error | DONE | TwoFactorTest T10 |

### Idempotency Requirements

| Req ID | Description | Status | Verified By |
|--------|-------------|--------|-------------|
| REQ-WP13B-011 | Idempotency-Key header support | DONE | IdempotencyTest |
| REQ-WP13B-012 | Same key + different body → 409 | DONE | IdempotencyTest T06 |
| REQ-WP13B-013 | Response replay (cached) | DONE | IdempotencyTest T05 |
| REQ-WP13B-014 | 24h TTL | DONE | IdempotencyRegistry |
| REQ-WP13B-015 | Hourly cleanup | DONE | Scheduler |

### Apply/Verify Requirements

| Req ID | Description | Status | Verified By |
|--------|-------------|--------|-------------|
| REQ-WP13B-016 | Apply is a server job | DONE | ProcessOutboxEvent |
| REQ-WP13B-017 | Verify is a server job | DONE | VerifySettingChange |
| REQ-WP13B-018 | 202 QUEUED response | DONE | Controller |
| REQ-WP13B-019 | Post-publish verification audit | DONE | VerifySettingChange |

### Queue Infrastructure

| Req ID | Description | Status | Verified By |
|--------|-------------|--------|-------------|
| REQ-WP13B-020 | Bounded queue:work via cron | DONE | crontab |
| REQ-WP13B-021 | Outbox locked processing | DONE | ProcessOutboxEvent |
| REQ-WP13B-022 | Non-overlapping schedules | DONE | max-time=50 < 60s |

### Deferred to WP-13c

| Req ID | Description | Reason |
|--------|-------------|--------|
| REQ-WP13C-001 | TOTP enrollment UI | Frontend |
| REQ-WP13C-002 | Recovery codes display UI | Frontend |
| REQ-WP13C-003 | Self-service 2FA disable | Frontend + audit |
| REQ-WP13C-004 | Lost-factor recovery flow | Design unclear |

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)
Requirements for WPs and blocks completed after original registry snapshot.

## WP-05a Requirements — Auth Attempts + PKCE
Source: DFM §218 + RFC 7636

| Req ID | Description | Status | Verified By |
|--------|-------------|--------|-------------|
| REQ-WP05A-001 | auth_attempts table (9 columns per DFM §218) | DONE | migration |
| REQ-WP05A-002 | AuthAttempt model with encrypted PKCE cast | DONE | app/Models/AuthAttempt.php |
| REQ-WP05A-003 | AuthAttemptService (RFC 7636 S256) | DONE | app/Services/ |
| REQ-WP05A-004 | AuthController.telegramStart includes PKCE params | DONE | AuthController patch |
| REQ-WP05A-005 | 64-char PKCE verifier (RFC 7636 range) | DONE | D-077 |
| REQ-WP05A-006 | handoff_hash SHA-256 single-use | DONE | D-079 |
| REQ-WP05A-007 | 14 tests PASS (34 assertions) | DONE | AuthAttemptTest |

## WP-05b Requirements — Telegram Foundation
Source: Design_Integration_Contract.md + DFM §275

| Req ID | Description | Status | Verified By |
|--------|-------------|--------|-------------|
| REQ-WP05B-001 | TelegramDestination model (18 cols) | DONE | 18 cols, scopeActive |
| REQ-WP05B-002 | TelegramPublication model (9 states) | DONE | 9 states, scopePending |
| REQ-WP05B-003 | TelegramPublicationEvent (append-only) | DONE | no timestamps |
| REQ-WP05B-004 | AdminTelegramController (4 read methods) | DONE | app/Http/Controllers |
| REQ-WP05B-005 | 4 GET routes registered | DONE | 2 destinations + 2 publications |
| REQ-WP05B-006 | 12 tests PASS | DONE | TelegramFoundationTest |

## WP-27 Requirements — Telegram OIDC Full Flow
Source: DFM §218 + Auth Contract §3 + Auth Contract §449

| Req ID | Description | Status | Verified By |
|--------|-------------|--------|-------------|
| REQ-WP27-001 | firebase/php-jwt v7.2.1 dependency | DONE | composer.json |
| REQ-WP27-002 | config/services.php telegram section | DONE | OIDC URLs + credentials |
| REQ-WP27-003 | OidcExchangeException (11 reasons) | DONE | app/Exceptions/ |
| REQ-WP27-004 | TelegramOidcService exchangeCode + JWKS | DONE | 7-step validation |
| REQ-WP27-005 | telegramCallback (code → id_token → user) | DONE | AuthController |
| REQ-WP27-006 | telegramExchange (handoff → Sanctum token) | DONE | AuthController |
| REQ-WP27-007 | personal_access_tokens UUID fix | DONE | migration (D-090) |
| REQ-WP27-008 | JWKS cached 1h | DONE | D-087 |
| REQ-WP27-009 | 60s clock skew tolerance | DONE | D-088 |
| REQ-WP27-010 | 20 tests PASS (43 assertions) | DONE | TelegramOidcTest |

## WP-27b Requirements — HMAC-Signed User Binding
Source: DFM §218 (preserved)

| Req ID | Description | Status | Verified By |
|--------|-------------|--------|-------------|
| REQ-WP27B-001 | HMAC-SHA256 signed handoff | DONE | AuthAttemptService |
| REQ-WP27B-002 | Handoff format base64url(JSON{a,e,u}).HMAC | DONE | D-092 |
| REQ-WP27B-003 | 60s TTL on handoff code | DONE | embedded 'e' |
| REQ-WP27B-004 | consumeByHandoff returns {attempt, user} | DONE | signature change |
| REQ-WP27B-005 | Race-free user binding (D-091 resolved) | DONE | T25 multi-user test |
| REQ-WP27B-006 | 6 new tests (T21-T26) | DONE | HMAC test suite |
| REQ-WP27B-007 | No schema change (DFM §218 preserved) | DONE | D-092 trade-off |

## B10-B17 Requirements — Ledger Integrity
Source: Constitution + audit

| Req ID | Description | Status | Verified By |
|--------|-------------|--------|-------------|
| REQ-B10-001 | Fix 8 ledger data integrity violations | DONE | CHANGE_LOG B10 |
| REQ-B13-001 | /downloads/ index (GAP-61) | DONE | HTTP 200 verified |
| REQ-B14-001 | GAP-62 registered as UNKNOWN | DONE | OPEN_GAPS |
| REQ-B15-001 | S001 Welcome at production root | DONE | HTTP 200 + title verified |
| REQ-B16-001 | Non-admin test coverage +16 | DONE | 106 total tests |
| REQ-B17-001 | L188-L192 duplicate IDs → L212-L216 | DONE | IMPLEMENTATION_LEDGER |
| REQ-B17-002 | HANDOFF_STATE full rewrite | DONE | 124 lines |
| REQ-B17-003 | MASTER_BASELINE append-only extensions | DONE | 78 insertions, 0 deletions |
| REQ-B17-004 | GAP-38/GAP-54 merge | DONE | OPEN_GAPS |
| REQ-B17-005 | GAP-07/GAP-60 clarification | DONE | OPEN_GAPS |
| REQ-B17-006 | GAP-42 removed from open list | DONE | OPEN_GAPS |
| REQ-B17-007 | Table count 25 → 38 documented | DONE | MASTER_BASELINE |

## R-TEST-01 to R-TEST-07 — Model Test Coverage (2026-09-30)

**Source:** Original audit (FELAGI_STATUS_REPORT) identified 7 test gaps
across 17 models. Addressed in B25–B28.

| Req ID | Description | Status | Verified By |
|--------|-------------|--------|-------------|
| R-TEST-01 | Payment/Boost/PaymentEvent/BoostPackage tests | COVERED | B25 — 54 tests (IMPLEMENTATION_LEDGER L217) |
| R-TEST-02 | AI/Comparison (4 models) tests | COVERED | B26 — 68 tests (IMPLEMENTATION_LEDGER L218) |
| R-TEST-03 | Category model tests | COVERED | B27 — 15 tests (IMPLEMENTATION_LEDGER L219) |
| R-TEST-04 | NeedAward model tests | COVERED | B27 — 13 tests (IMPLEMENTATION_LEDGER L219) |
| R-TEST-05 | Attachment + Report model tests | COVERED | B27 — 38 tests (IMPLEMENTATION_LEDGER L219) |
| R-TEST-06 | Setting + SettingDraft model tests | COVERED | B28 — 45 tests (IMPLEMENTATION_LEDGER L220) |
| R-TEST-07 | UserRole model tests | COVERED | B28 — 17 tests (IMPLEMENTATION_LEDGER L220) |

### Coverage Summary

| Metric | Before B25–B28 | After B25–B28 |
|--------|----------------|---------------|
| Tests in suite | 162 | 412 |
| Assertions | 320 | 691 |
| Models with tests | 4 / 21 | 17 / 21 |
| R-TEST items COVERED | 0 / 7 | **7 / 7** |

### Remaining Uncovered Models (4)

- Export (has B21 test indirectly via SettingVersion?)
- ScheduledSetting (no direct test)
- IdempotencyKey (no direct test)
- TelegramDestination / TelegramPublication / TelegramPublicationEvent (WP-27 covers indirectly)

### Notes

- Test convention: tests/Feature/Models/ (not tests/Unit/Models/)
- No HasFactory trait on any model — all use ::create()
- Evidence: evidence/WP-B{25..28}_evidence.md + logs
- Verification: 412/412 pass on 2026-09-30 (13.48s)

**End of R-TEST backfill.**

---

## L345 Reconciliation — REQ-WP13C Status Update (2026-10-03)

**Supersedes:** L222-227 ("Deferred to WP-13c" — 4 rows, reason "Frontend")

### Status

| Req ID | Description | Before | After | Evidence |
|--------|-------------|--------|-------|----------|
| REQ-WP13C-001 | TOTP enrollment UI | Deferred | ✅ **COVERED** | `profile/2fa.blade.php` + `TwoFactorTest` |
| REQ-WP13C-002 | Recovery codes display UI | Deferred | ✅ **COVERED** | Same view + `regenerateRecoveryCodes` |
| REQ-WP13C-003 | Self-service 2FA disable | Deferred | ✅ **COVERED** | Same view + `disable` route |
| REQ-WP13C-004 | Lost-factor recovery flow | Deferred | 🔴 **UNKNOWN** | No spec; requires stakeholder |

### Evidence

- 8 API routes: `php artisan route:list | grep 2fa`
- 1 web route: `/profile/2fa`
- Controllers: `TwoFactorController`, `TwoFactorWebController`
- Service: `TwoFactorService`
- View: `resources/views/profile/2fa.blade.php` (TOTP + QR + recovery + disable)
- Tests: **28 PASS** (`tests/Feature/Auth/TwoFactorTest.php`)

### Constitution note

REQ-WP13C-004 remains UNKNOWN, not MISSING. Requires stakeholder to
define the lost-factor recovery UX (e.g., email-based re-enrollment
vs. support-assisted reset).

**Recorded by:** L345

---

## L346-D — REQ-WP13C-004 Spec Request Filed (2026-10-03)

**Action:** Formal spec request created for the last open item in WP-13c.

**New artifact:**
- `docs/spec-requests/REQ-WP13C-004_lost_factor_recovery.md`
- `app/Contracts/LostFactorRecoveryInterface.php` (empty marker, no methods)

**Cross-references:**
| Reference | Location |
|-----------|----------|
| Design proposal (existing) | `docs/reports/GAP-53_DESIGN_PROPOSAL_20261002.md` |
| Stakeholder decisions (existing) | `docs/reports/STAKEHOLDER_DECISIONS_20261002.md` §Decision 4.2 |
| GAP entry | `OPEN_GAPS.md` (GAP-53) |
| Contract marker | `app/Contracts/LostFactorRecoveryInterface.php` |

**REQ-WP13C-004 status:**
- Before L346-D: UNKNOWN (design unclear)
- After L346-D:  UNKNOWN (formal spec request filed)
- Still UNKNOWN — not MISSING — awaiting design owner on 6 open questions

**6 open questions filed:**
1. Main Admin self-recovery allowed?
2. Second admin required (dual control)?
3. Time window between request and approval?
4. Recovery codes reset all or keep remaining?
5. Lost recovery codes + lost TOTP — same flow?
6. Support channel: S021 only or dedicated route?

**Implementation gate:** No backend work begins until design owner
issues LOCKED spec (Constitution: "do not guess missing requirements").

**No code changes to existing behavior.** Interface is a marker only.

**Recorded by:** L346-D
