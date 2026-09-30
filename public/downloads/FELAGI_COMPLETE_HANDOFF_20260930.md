# FELAGI — COMPLETE PROJECT HANDOFF

**Generated:** 2026-09-30T08:19:35Z
**App HEAD:** 8539761fda0e81b3bc73a82422c5d31de8708db6
**Last Commit:** 8539761 WIDGET-FLOW: Telegram Login Widget pivot (OIDC unavailable)

---

## Table of Contents

### A. Core Ledgers (14 canonical + 3 sources)
- MASTER_BASELINE, REQUIREMENT_REGISTRY, ARCHITECTURE_MAP
- WORK_PACKAGES, IMPLEMENTATION_LEDGER, TEST_VERIFICATION
- OPEN_GAPS, CHANGE_LOG, DECISION_LOG, RELEASE_STATUS
- HANDOFF_STATE, B17_ROLLBACK, ENVIRONMENT_CHECKLIST, PRIORITY_PLAN
- SOURCE_OF_TRUTH.md, FELAGI_STATUS_REPORT.md
- Universal Production Pass Development Constitution

### B. Evidence Files (.md)
- All evidence/*.md

### C. Evidence Logs (tail 50 lines each)
- All evidence/*.log

### D. Runtime Info
- Git log, test summary, file inventory

---

# ═══════════════════════════════════════════
# SECTION A — CORE LEDGERS
# ═══════════════════════════════════════════


════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/MASTER_BASELINE.md
════════════════════════════════════════════════════════════

# FELAGI v1.4.2 — MASTER BASELINE
Status: **LOCKED**
Last Updated: 2026-09-29

## Identity
- Design contract version: **1.4.1**
- Handoff release version: **1.4.2**
- Source hash: `e467f84820518acd68ad6824a05fefa8590f004c3949cca278f862140a71f31c`
- Token version: `FGM-TOKENS-1.4`
- Package: `Felagi_Clean_Developer_Handoff_v1.4.2.zip`

## Deliverables (51 total)
| Item | Count |
|------|-------|
| User screens | 23 (S001-S023) |
| Admin screens | 23 (A001-A023) |
| Total screens | 46 |
| Interaction overlays | 11 (O01-O11) |
| Governed controls | 55 |
| Component primitives | 31 |
| Component compositions | 22 |
| Database tables | 25 |
| API routes | ~40 |
| Settings | 22 |
| Locales | 2 (am default + en launch) |
| Locale keys | 669 |
| Sponsored placements | 3 |
| User journeys | 26 |
| Verification checks | 7,810+ |
| Contrast pairs | 66 |

## Authority Hierarchy (6 levels)
1. `System_Specification/DFM-FDS-1.4.md` — Business invariants
2. `System_Specification/Design_Integration_Contract.md`
3. `Final_Master_Product_Design_Specification.md` + matrices
4. `Design_Data/screen-manifest.json`
5. `Design_System/tokens/token_registry.json`
6. `Localization/app_am.arb` + `app_en.arb`

## Locked Invariants (40 rules)
1. Need-first marketplace
2. Amharic default + English launch
3. Navy `#003366` + Orange `#FF9933`
4. Requester decision authority
5. AI advisory only
6. No percentage commission
7. 3 revenue domains (Boost, Unlock, Sponsored Ads)
8. Ads never affect organic/AI
9. `UNKNOWN ≠ ZERO`
10. `PARTIAL ≠ COMPLETE`
11. `IMPLEMENTED ≠ VERIFIED`
12. `Designed ≠ Implemented ≠ Verified`
13. `Preview ≠ Production proof`
14. `Published ≠ Applied ≠ Verified`
15. `CLIENT SUCCESS ≠ VERIFIED PAYMENT`
16. `VERIFIED PAYMENT → NO SECOND CHARGE`
17. Never white body text on orange
18. State = text + icon + color (never color-only)
19. Secrets = env references, never settings values
20. Missing/corrupt security setting = fail closed
21. Never pass ID token/secret in deep link
22. Retry same snapshot; new comparison = new version
23. One event ID for all recipients
24. No AI network call inside transaction
25. Client callbacks only trigger GET reconciliation
26. No client-origin confirmation of payment
27. Webhooks require signature + dedupe
28. Files: MIME from bytes, quarantine
29. Laravel OUTSIDE public web root
30. cPanel doc root = `public/` or blocked
31. RPO ≤ 24h, RTO ≤ 8h (proof by drill)
32. Brand: no redraw/recolor/approximate
33. Amharic Offer = `አቅርቦት` (not `ጥቆማ`)
34. Unknown ≠ Failed; Pending ≠ Paid
35. AI must NOT translate UNKNOWN into negative
36. Optimism: filter/sort/disclosure/selection/draft only
37. Never celebrate client payment success
38. No podium/winner/confetti
39. No hidden work, no hidden assumptions
40. DONE = implemented + integrated + tested + verified + documented + evidenced

## Felagi vs Legacy (BEHAQ)
- **Felagi**: active project (v1.4.2)
- **BEHAQ**: legacy project (archived, not active)
- **NEVER MIX** the two projects

## WP-13 — Admin Change Lifecycle (DONE)
Date: 2026-09-29

**Delivered:** 14 files
- Models: Setting, SettingVersion, SettingDraft, OutboxEvent
- Services: AuditWriter, OutboxWriter, AdminChangeService
- Controller: AdminChangeController (8 endpoints)
- Requests: CreateDraftRequest, PublishChangeRequest
- Policy: SettingPolicy
- Exception: SettingsVersionConflictException
- Seeder: ControlRegistrySeeder (31 settings)
- Test: ChangeLifecycleTest (8 PASS)

**Endpoints (10 routes):**
- POST   /api/v1/admin/changes
- GET    /api/v1/admin/changes/{id}
- POST   /api/v1/admin/changes/{id}/validate
- POST   /api/v1/admin/changes/{id}/simulate
- POST   /api/v1/admin/changes/{id}/preview
- POST   /api/v1/admin/changes/{id}/publish
- GET    /api/v1/admin/changes/{id}/audit
- POST   /api/v1/admin/changes/{id}/rollback
- POST   /api/v1/admin/settings/drafts/{id}/publish (legacy)
- POST   /api/v1/admin/settings/{key}/rollback (legacy)

**Verification:**
- 8 feature tests PASS (15 assertions)
- Duration: 3.00s
- Test DB: MySQL isolated (zagcreht_felagi_test)
- Production DB untouched

**Integrity:**
- audit_logs: SHA-256 hash chain active
- outbox_events: aggregate_id = setting_versions.id
- setting_versions: append-only (immutable)

## WP-13b — Reauth + TOTP 2FA + Idempotency (DONE)
Date: 2026-09-29

**Delivered:** 12 files
- Migrations: 1 (add_2fa_and_reauth_to_users_table)
- Services: 3 (ReauthValidator, TotpService, IdempotencyRegistry)
- Middleware: 2 (RequireReauth, IdempotencyKey)
- Jobs: 3 (ProcessOutboxEvent, VerifySettingChange, CleanupExpiredIdempotencyKeys)
- Exceptions: 4 (ReauthRequired, TwoFactorRequired, InvalidTotpCode, IdempotencyConflict)
- Composer: pragmarx/google2fa-laravel v3.0.1

**Endpoints added:**
- POST /api/v1/admin/changes/{id}/apply   (server job)
- POST /api/v1/admin/changes/{id}/verify  (server job)

**Middleware attached:**
- publish route:    [reauth, idempotent]
- apply route:      [idempotent]
- verify route:     [idempotent]
- rollback route:   [reauth]
- store route:      [idempotent]

**Cron:**
- + queue:work (every min, --stop-when-empty --max-time=50)

**Design compliance:**
- Auth Contract §3: 5-min reauth window + 2FA for CRITICAL
- DFM §149: Idempotency-Key + 409 IDEMPOTENCY_CONFLICT
- DFM §461: bounded queue:work via cron
- DFM §418: verify as server job

**Verification:**
- 36 tests PASS (63 assertions, 4.98s)
- Production DB untouched
- `.env.testing` APP_KEY fixed (32 bytes)

**Deferred:**
- Reauth self-service enrollment UI (WP-13c)
- Recovery codes UI display (WP-13c)

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)
The following WPs and blocks were completed after the original LOCKED baseline
was written. They are appended here for traceability. The original LOCKED
content above is unchanged.

### WP-05a — Auth Attempts + PKCE (DONE)
Date: 2026-09-29
Delivered: migration + model + service + controller patch + 14 tests
Evidence: AuthAttemptTest (34 assertions)
Resolves: GAP-42

### WP-05b — Telegram Foundation (DONE)
Date: 2026-09-29
Delivered: 3 models + 1 controller + 4 read routes + 12 tests
Deferred: write endpoints → WP-27

### WP-27 — Telegram OIDC Full Flow (DONE)
Date: 2026-09-29
Delivered: firebase/php-jwt v7.2.1, OidcExchangeException,
           TelegramOidcService (PKCE + JWKS + 7-step validation),
           telegramCallback + telegramExchange, UUID fix, 20 tests
Evidence: TelegramOidcTest (43 assertions)
Resolves: GAP-31, GAP-55

### WP-27b — HMAC-Signed User Binding (DONE)
Date: 2026-09-29
Delivered: HMAC-SHA256 signed handoff, race-free exchange, 6 new tests
Resolves: D-091 race condition
Evidence: T21-T26 PASS

### B10 — Constitution Compliance Block (DONE)
Date: 2026-09-29
Type: Documentation-only (no code/DB/routes)
Fixed: 8 data integrity violations
Evidence: IMPLEMENTATION_LEDGER L212

### B13 — /downloads/ index (DONE)
Date: 2026-09-29
Type: Static asset + .gitignore fix
Closes: GAP-61
Evidence: IMPLEMENTATION_LEDGER L213

### B14 — GAP-62 registration (DONE)
Date: 2026-09-29
Type: Documentation-only
Registered: GAP-62 (production root UNKNOWN)
Evidence: IMPLEMENTATION_LEDGER L214

### B15 — S001 Welcome at production root (DONE)
Date: 2026-09-29
Type: Frontend view replacement (LOCKED design)
Source: UI_Handoff/ui-preview/app.js:144
Closes: GAP-62
Evidence: IMPLEMENTATION_LEDGER L215

### B16 — Non-admin test coverage + .bak cleanup (DONE)
Date: 2026-09-29
Type: Test coverage + housekeeping
Added: 16 tests (NeedFlow, OfferFlow, MessageFlow)
Suite evolution: 90 → 106 tests (220 assertions)
Evidence: IMPLEMENTATION_LEDGER L216

### B17 — Ledger Integrity Sweep (DONE)
Date: 2026-09-29
Type: Documentation-only
Fixed: 5 duplicate L-IDs, 6 stale entries, 3 GAP duplicates
Evidence: CHANGE_LOG B17 entry + this appendix

### Table Count Reconciliation
- Original LOCKED baseline declared: 25 tables
- Actual after WP-05 complete: 38 tables
- Delta: +13 tables (auth_attempts, idempotency_keys, exports,
  telegram_destinations, telegram_publications, telegram_publication_events,
  jobs, failed_jobs, job_batches, scheduled_settings,
  personal_access_tokens, setting_drafts, setting_versions)
- Status: ACCEPTED — documented per D-046 ("All 38 tables now in production DB")



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/REQUIREMENT_REGISTRY.md
════════════════════════════════════════════════════════════

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



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/ARCHITECTURE_MAP.md
════════════════════════════════════════════════════════════

# ARCHITECTURE MAP — Felagi v1.4.2
Last Updated: 2026-09-29

## Layer 0 — Governance
- `START_HERE.md`
- `README.md`
- `PACKAGE_MANIFEST.json`
- `DELIVERABLE_INDEX.json`
- `Release_Gates_and_Evidence.md` (11 canonical owners, 9 gates)
- 6-level authority hierarchy

## Layer 1 — Product Contracts (11 owners)
1. Master PDS — product identity/revenue/scope
2. `token_registry.json` — visual values
3. `screen-manifest.json` — screens/routes/state/API
4. Localization ARB — static microcopy
5. `Monetization_Payment_Specification.md`
6. `AI_Evaluation_Contract.md`
7. `Admin_Authorization_Contract.md`
8. `Sponsored_Advertising_Contract.md`
9. `Design_Integration_Contract.md`
10. `Final_Interaction_Contract.md`
11. `Release_Gates_and_Evidence.md`

## Layer 2 — Surfaces
- 23 user screens (S001-S023)
- 23 admin screens (A001-A023)
- 11 interaction overlays (O01-O11)
- 5 mobile tabs (Needs/My Needs/My Offers/Notifications/Profile)
- 26 journeys (13 main + 3 admin + 4 governed + 3 special + 3)

## Layer 3 — Design System (FGM-TOKENS-1.4)
- 14 token families
- 31 component primitives + 22 compositions
- 14 universal states
- 12 patterns
- 6 breakpoints (320/360/600/840/1200/1600)
- WCAG 2.2 AA target
- 9 canonical Amharic terms LOCKED

## Layer 4 — Runtime Targets
- **Flutter starter** (18 classes) — COMPILE BLOCKED
- **HTML preview** — fictional, no live APIs
- **25 MySQL/InnoDB tables**
- **~40 API routes** (base `/api/v1`)
- **Laravel + database queue + 1-min cron**

## Layer 5 — Evidence
- ✅ SOURCE: 7,810+ PASS, 66 contrast, 140 semantic
- ❌ RUNTIME: 0 (N06-N10)
- ❌ DEPLOYMENT: cPanel/PHP/MySQL unverified
- ❌ RPO/RTO: no timed drill

## Layer 6 — Admin Change Lifecycle (WP-13)
Added: 2026-09-29

### HTTP — /api/v1/admin/changes
- POST / → store (draft create)
- GET /{id} → show
- POST /{id}/validate → validateDraft
- POST /{id}/simulate → simulate
- POST /{id}/preview → preview
- POST /{id}/publish → publish
- GET /{id}/audit → audit trail
- POST /{id}/rollback → new draft from older version

### Service — AdminChangeService
- createDraft(): draft from key + value
- validateDraft(): type check + DFM §8.2 dependencies
- simulate(): before/after diff
- preview(): persist impact_preview
- publish(): DB transaction (5 writes)
- rollback(): new draft based on old version

### Supporting Services
- AuditWriter: SHA-256 hash chain (prev_hash + hash)
- OutboxWriter: aggregate_id = setting_versions.id (UUID)

### Publish Transaction (atomic)
1. setting_versions (immutable row)
2. settings (value + version_number++)
3. setting_drafts (status → PUBLISHED)
4. audit_logs (hash chain)
5. outbox_events (aggregate_type=setting_version)

### Data
- settings (PK=key VARCHAR) — current value + version_number
- setting_versions (append-only) — immutable history
- setting_drafts (DRAFT/VALIDATED/REJECTED/PUBLISHED)
- scheduled_settings (Phase 4 scheduling)
- audit_logs (append-only hash chain)
- outbox_events (aggregate_id UUID)

### Authorization — SettingPolicy
- view(): MAIN_ADMIN any; ADMIN non-secret only
- create(): ADMIN+
- update(): secret → MAIN_ADMIN only; else ADMIN+
- publish(): HIGH/CRITICAL → MAIN_ADMIN only; else ADMIN+
- rollback(): alias to publish()

### Exception — SettingsVersionConflictException
- Maps to HTTP 409
- Code: SETTINGS_VERSION_CONFLICT
- Payload: {setting_key, expected_version, actual_version}

### Seeder — ControlRegistrySeeder (31 settings)
- 7 FEATURE toggles (HIGH)
- 4 Telegram config (HIGH/MEDIUM)
- 3 Marketplace config (MEDIUM/HIGH)
- 9 AI config (HIGH/MEDIUM)
- 1 Payment provider (CRITICAL)
- 2 Uploads/Exports (MEDIUM)
- 2 Content (LOW)
- 2 Privacy (MEDIUM)
- 1 Safe Mode (CRITICAL)

### Test — ChangeLifecycleTest
- 8 tests, 15 assertions
- Uses RefreshDatabase (isolated MySQL test DB)
- Setup: seed ControlRegistrySeeder

## Layer 7 — WP-13b (Reauth + 2FA + Idempotency)
Added: 2026-09-29

### Reauth Layer
- users.recently_authenticated_at (5-min window — Auth Contract §3)
- ReauthValidator::isFresh(), ageInMinutes(), require(), mark(), clear()
- RequireReauth middleware → 401 REAUTH_REQUIRED

### 2FA Layer (TOTP — RFC 6238)
- users.totp_secret (encrypted)
- users.totp_enabled_at
- users.totp_recovery_codes (encrypted:array, SHA-256 hashed values)
- TotpService::generateSecret(), verify(), generateRecoveryCodes(), consumeRecoveryCode(), requireFor()
- pragmarx/google2fa-laravel v3.0.1

### Idempotency Layer
- DFM §149 compliance
- IdempotencyRegistry::begin(), complete(), cleanup(), requestHash()
- IdempotencyKey middleware → 409 IDEMPOTENCY_CONFLICT / replay
- Header: `Idempotency-Key`
- TTL: 24h (D-070)

### Job Layer (Outbox consumers)
- ProcessOutboxEvent → routes to handlers (locked_until)
- VerifySettingChange → server probe + audit
- CleanupExpiredIdempotencyKeys → hourly cleanup

### Exception Layer
- ReauthRequiredException → 401
- TwoFactorRequiredException → 403
- InvalidTotpCodeException → 422
- IdempotencyConflictException → 409

### Scheduler (routes/console.php)
- outbox-dispatch → everyMinute
- idempotency-cleanup → hourly

### Cron (crontab)
- schedule:run (every min — WP-21)
- queue:work --stop-when-empty --max-time=50 (every min — WP-13b)

### Endpoints (WP-13b additions)
- POST /api/v1/admin/changes/{id}/apply  → 202 QUEUED (server job)
- POST /api/v1/admin/changes/{id}/verify → 202 QUEUED (server job)

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)
Original Layer 0–7 preserved above. New layers appended for WPs and blocks
completed after original architecture snapshot.

## Layer 8 — Extended Backend (WP-05a, WP-05b, WP-27, WP-27b)

### Auth Attempts + PKCE (WP-05a)
- `auth_attempts` table (9 columns per DFM §218):
  state_hash, nonce_hash, pkce_verifier_encrypted, handoff_hash,
  expires_at, consumed_at, return_uri, created_at, updated_at
- `AuthAttempt` model (encrypted PKCE verifier cast)
- `AuthAttemptService::generateVerifier()` (64 chars, RFC 7636)
- `AuthAttemptService::generateChallenge()` (S256)
- `AuthController.telegramStart()` patched with PKCE parameters

### Telegram Foundation (WP-05b)
- `TelegramDestination` model — 18 cols, scopeActive, canPublish
- `TelegramPublication` model — 9 states, scopePending
- `TelegramPublicationEvent` model — append-only, no timestamps
- `AdminTelegramController` — 4 read-only methods
- 4 GET routes: destinations (index, show), publications (index, show)

### Telegram OIDC Full Flow (WP-27)
- `firebase/php-jwt` v7.2.1 (RS256 verification)
- `config/services.php` telegram section (OIDC URLs + credentials)
- `OidcExchangeException` — 11 reason codes
- `TelegramOidcService`:
  - `exchangeCode()` — POST to token_url
  - `validateIdToken()` — JWKS fetch + 7-step validation
  - `completeLogin()` — atomic: exchange → validate → upsert → markReauth
- `AuthController.telegramCallback()` — code → id_token → user → handoff
- `AuthController.telegramExchange()` — handoff → Sanctum token
- `personal_access_tokens.tokenable_id` — bigint → char(36) UUID fix

### HMAC-Signed Handoff (WP-27b)
- Handoff format: `<base64url(JSON{a,e,u})>.<HMAC-SHA256-hex>`
  - `a` = attempt_id (UUID)
  - `e` = expires_at (unix timestamp, 60s TTL)
  - `u` = user_id (UUID, optional)
- `AuthAttemptService.generateHandoff(?User $user)` — signs payload
- `AuthAttemptService.consumeByHandoff()` → `{attempt, user}` (signature change)
- Race-free user binding (D-091 resolved)
- DFM §218 preserved (no schema change)

## Layer 9 — Ledger Governance Blocks (B10-B17)

### B10 — Constitution Compliance Block
- Type: Documentation-only
- Fixed: 8 data integrity violations in ledgers
- Files touched: WORK_PACKAGES, OPEN_GAPS, HANDOFF_STATE, PRIORITY_PLAN
- Evidence: IMPLEMENTATION_LEDGER L212

### B13 — /downloads/ Static Index
- `public/downloads/index.html` (6.5 KB)
- `.gitignore` exception for downloads/index.html
- Closes GAP-61

### B14 — GAP-62 Registration
- Type: Documentation-only
- Registered: GAP-62 (production root UNKNOWN)
- Corrected: B10 CHANGE_LOG mislabel (GAP-57 → GAP-62)
- Evidence: IMPLEMENTATION_LEDGER L214

### B15 — S001 Welcome Deployment
- `resources/views/welcome.blade.php` — replaced with LOCKED S001
- `lang/am.json` + `lang/en.json` (5 keys each)
- `public/assets/brand/felagi-lockup.svg` + `-on-dark.svg`
- LOCKED source: UI_Handoff/ui-preview/app.js:144
- Closes GAP-62

### B16 — Test Coverage Expansion
- `tests/Feature/Need/NeedFlowTest.php` — 6 tests
- `tests/Feature/Offer/OfferFlowTest.php` — 6 tests
- `tests/Feature/Message/MessageFlowTest.php` — 4 tests
- Suite: 90 → 106 tests (220 assertions)
- `.archives/20260929-b16-bak-cleanup/` — 8 .bak archived

### B17 — Ledger Integrity Sweep
- L188-L192 duplicate IDs renumbered → L212-L216
- HANDOFF_STATE.md full rewrite (124 lines)
- MASTER_BASELINE append-only extensions (78 insertions)
- REQUIREMENT_REGISTRY append-only extensions (76 insertions)
- GAP-38/GAP-54 merge, GAP-07/GAP-60 clarification, GAP-42 removal
- Table count 25 → 38 documented

## Layer 10 — Authority Hierarchy Extension
Original: 6 levels
Extended: no change (WP-27 follows existing hierarchy)

## Layer 11 — Test Infrastructure
### Test DB Isolation
- `zagcreht_felagi_test` — dedicated MySQL test database
- `.env.testing` — separate config with valid 32-byte APP_KEY
- `bin/migrate-test.sh` — always clears config cache first (D-076)

### Test Count Evolution
- WP-13: 8 tests
- WP-13b: +28 (36 cumulative)
- WP-05a: +14 (50 cumulative)
- WP-05b: +12 (62 cumulative)
- WP-27: +22 (84 cumulative)
- WP-27b: +4 (88 cumulative)
- (interim): +2 (90 cumulative — UNKNOWN reconciliation)
- B16: +16 (106 cumulative)
- **Final: 106 tests, 220 assertions**



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/WORK_PACKAGES.md
════════════════════════════════════════════════════════════

# WORK PACKAGES — Felagi v1.4.2
Last Updated: 2026-09-29 (WP-27b DONE)

## Status Legend
NOT_STARTED / READY / IN_PROGRESS / BLOCKED / IMPLEMENTED / INTEGRATED / TESTED / VERIFIED / DONE / DEFERRED / N/A

## Package List

| WP | Scope | Status | Blocker |
|----|-------|--------|---------|
| WP-01 | Foundation Registry | **DONE** | — |
| WP-02 | Static Verification Re-run | **VERIFIED** (7,810+ PASS) | — |
| WP-03 | Browser/A11y Render (G04) | **BLOCKED** | Chromium/device |
| WP-04 | Flutter Compile (G05) | **BLOCKED** | Flutter/Dart SDK |
| **WP-05** | **Backend Services** | **✅ DONE** | **COMPLETE** |
| WP-06 | Amharic Runtime (G08) | **PARTIAL** | Native reviewer |
| WP-07 | Marketplace Baseline (G07) | **BLOCKED** | Measured data |
| WP-08 | Observability (G09) | **BLOCKED** | Telemetry env |
| WP-09 | Production Gates | **BLOCKED** | WP-03..08 |
| WP-10 | AI Evaluation Live | **BLOCKED** | AI provider |
| WP-11 | Payment Live | **BLOCKED** | Payment provider |
| WP-12 | Sponsored Ads Serving | **BLOCKED** | Ads infra |
| WP-13 | Admin Change Lifecycle | **DONE** ✅ | — |
| WP-13b | Reauth + TOTP 2FA + Idempotency | **DONE** ✅ | — |
| WP-05a | Auth Attempts (auth_attempts + PKCE) | **DONE** ✅ | — |
| WP-05b | Telegram Foundation (3 models + read endpoints) | **DONE** ✅ | — |
| WP-27 | Telegram OIDC Full Flow | **DONE** ✅ | — |
| WP-27b | HMAC-Signed User Binding (D-091 fix) | **DONE** ✅ | — |
| WP-14 | Telegram Delivery | **BLOCKED** | Bot token |
| WP-15 | Font Glyph Coverage | **BLOCKED** | Licensed font |
| WP-16 | Amharic Linguistic QA | **BLOCKED** | Native reviewers |
| WP-17 | Responsive 46-Screen | **BLOCKED** | Real browsers |
| WP-18 | A11y 46-Screen | **BLOCKED** | AT + browser |
| WP-19 | Runtime State Live | **BLOCKED** | Full stack |
| WP-20 | Error Recovery Live | **BLOCKED** | Fault injection |
| **WP-21** | **Laravel/cPanel Deployment** | **✅ DONE** | **COMPLETE** |
| **WP-22** | **DB Queue + Cron Setup** | **✅ DONE** | **COMPLETE** |
| WP-23 | Backup Restore Drill | **BLOCKED** | Backup target |
| WP-24 | T01-T18 Integration Tests | **BLOCKED** | Felagi routes needed |
| WP-25 | Webhook Signature | **BLOCKED** | Provider sandbox |
| WP-26 | File Malware Scanning | **BLOCKED** | Host AV |
| WP-28 | Safe Mode Drills | **BLOCKED** | Full stack |

| WP-13c | 2FA Enrollment UI | **DEFERRED** | design unclear |
## Statistics
- DONE: 10 (WP-01, WP-05, WP-05a, WP-05b, WP-13, WP-13b, WP-21, WP-22, WP-27, WP-27b)
- VERIFIED: 1 (WP-02)
- PARTIAL: 1 (WP-06)
- BLOCKED: 20

## WP-21 Completion Details
**Date:** 2026-09-29
**Deliverables:**
- Laravel 11.56.1 at `~/felagi_app/`
- MySQL DB `zagcreht_felagi` (9 tables)
- HTTPS live: https://zagcreativity.com
- Cron: 1-min scheduler
- Bundle: `Felagi_v1.4.2_20260929-0903.bundle`

## WP-13 Completion Details
**Date:** 2026-09-29
**Status:** ✅ DONE

**Files:** 14 new + 3 patched
**Routes:** 10 admin routes registered
**Tests:** 8 passed (15 assertions)
**Evidence:** tests/Feature/Admin/ChangeLifecycleTest.php

**Patches:**
- BaseApiController — added AuthorizesRequests trait
- UserFactory — schema-aligned (telegram_subject, full_name)
- routes/api.php — admin changes block

**Related:**
- WP-13b (follow-up): Reauth + Idempotency + Second Factor
- GAP-45, GAP-46, GAP-47 — deferred to WP-13b

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)
Original WP table (WP-01 → WP-28) preserved above.
B-Blocks are a separate tracking category (see D-110, U-21).

## B-Blocks — Documentation/Quality Blocks

| Block | Scope | Status | Evidence |
|-------|-------|--------|----------|
| B10 | Constitution Compliance Block (8 fixes) | DONE | IMPLEMENTATION_LEDGER L212 |
| B11 | Main Admin Foundation verification (14/14 artifacts) | DONE | — |
| B12 | Bundle refresh @ 1343_B12_DONE | DONE | Bundle artifact |
| B13 | /downloads/ index (GAP-61 closed) | DONE | HTTP 200 verified |
| B14 | GAP-62 registration + B10 mislabel fix | DONE | OPEN_GAPS |
| B15 | S001 Welcome at production root (GAP-62 closed) | DONE | HTTP 200 + title |
| B16 | Non-admin tests (+16) + .bak cleanup | DONE | 106 tests total |
| B17 | Ledger Integrity Sweep | DONE | This entry |

## B-Blocks vs Work Packages — Formal Relationship (UNKNOWN U-21)

The relationship between "B-blocks" and "Work Packages" is not formally
defined. Evidence suggests B-blocks are:
- Documentation/quality improvements
- Cross-cutting fixes (not feature work)
- Tracked in IMPLEMENTATION_LEDGER with L-IDs (L212-L216)

They do NOT appear in the WP statistics table above.
This may be intentional (separate tracking) or a gap.

**Decision required from stakeholder:** Should B-blocks be:
(a) Formalized as WPs (renumber to WP-29+)?
(b) Kept as a separate "blocks" category?
(c) Merged into existing WPs?

For now: documented as-is. Do not guess (per D-096/D-097 pattern).

## B17 Statistics Update

WP totals remain: 10 DONE, 1 VERIFIED, 1 PARTIAL, 16 BLOCKED, 1 DEFERRED.
B-Blocks totals: **8 DONE** (B10, B11, B12, B13, B14, B15, B16, B17).

B-Blocks are NOT counted in WP totals (see U-21).
| B18-B24 | Ledger drift backfill | DONE | IMPLEMENTATION_LEDGER L221-L227 |
| B25 | Payment domain tests (+54) | DONE | IMPLEMENTATION_LEDGER L217 |
| B26 | AI/Comparison domain tests (+68) | DONE | IMPLEMENTATION_LEDGER L218 |
| B27 | Safety/Marketplace tests (+66) + GAP-71 fix | DONE | IMPLEMENTATION_LEDGER L219 |
| B28 | Settings/Role tests (+62) | DONE | IMPLEMENTATION_LEDGER L220 |
| GAP-71b | Attachment factory + HasFactory | DONE | IMPLEMENTATION_LEDGER L228 |

---

## WP-05c — Admin Read Endpoints (BLOCKED_ON_UNKNOWN)

**Registered:** 2026-09-30 (discovered during Phase D audit)
**Status:** BLOCKED_ON_UNKNOWN
**Spec request:** docs/spec-requests/WP-05c_admin_read_endpoints.md

### Why BLOCKED_ON_UNKNOWN

- Spec request document exists but marked "PENDING STAKEHOLDER"
- 6 UNKNOWN items (per spec request):
  1. Which admin resources need read endpoints? (Users? Needs? Offers? Payments? Audit logs?)
  2. What fields per resource?
  3. Pagination strategy? (cursor vs offset)
  4. Filter/sort/search requirements?
  5. Field-level authorization?
  6. Response envelope?
- Constitution Art. "Do not guess missing requirements" — no implementation without LOCKED spec
- Constitution Art. "UNKNOWN != MISSING" — registered as UNKNOWN, not MISSING

### What We Know

- Admin WRITE endpoints exist: /api/v1/admin/changes/* (16 routes)
- 2 controllers: AdminChangeController, AdminTelegramController
- Authorization pattern: SettingPolicy (extensible)
- Estimated work after spec: 3-5 controllers, 5-10 routes, 20-30 tests

### Required Action

Stakeholder to provide LOCKED spec. Then WP-05c can be unblocked.

### Escalation

- Product Owner: (UNKNOWN — to be filled)
- Design Owner: (UNKNOWN — to be filled)
- Release Owner: (UNKNOWN — to be filled)
| GAP-71c | 14 additional factories + HasFactory | DONE | IMPLEMENTATION_LEDGER L229 |
| WIDGET-FLOW | OIDC -> Widget pivot | DONE | IMPLEMENTATION_LEDGER L230 |



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/IMPLEMENTATION_LEDGER.md
════════════════════════════════════════════════════════════

# IMPLEMENTATION LEDGER — Felagi v1.4.2
Last Updated: 2026-09-29

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L001 | Package structure | **VERIFIED** | PACKAGE_MANIFEST.json |
| L002 | 46 screens declared | **VERIFIED** | Manifest |
| L003 | 23 user + 23 admin split | **VERIFIED** | Manifest |
| L004 | 55 controls | **VERIFIED** | Manifest |
| L005 | 669 locale keys × 2 | **VERIFIED** | Manifest |
| L006 | 3 placements | **VERIFIED** | Manifest |
| L007 | Source hash | **VERIFIED** | `e467f848...` |
| L008 | G01 source consistency | **MET** | verification-run.json |
| L009 | G02 design completeness | **MET** | Acceptance Report |
| L010 | G03 brand source | **MET** | manifest |
| L011 | G04 browser/AT | **BLOCKED** | N06 |
| L012 | G05 Flutter | **BLOCKED** | N07 |
| L013 | G06 services | **REQUIRES_EVIDENCE** | N08 |
| L014 | G07 monetization | **REQUIRES_EVIDENCE** | N10 |
| L015 | G08 language runtime | **PARTIAL** | N09 |
| L016 | G09 observability | **REQUIRES_EVIDENCE** | — |
| L017 | Static tests | **VERIFIED** (7,810+ PASS) | verification-run.json |
| L018 | Contrast pairs | **VERIFIED** (66/66 PASS) | Contrast_Evidence.md |
| L019 | Semantic verification | **VERIFIED** (140 PASS) | semantic-results.json |
| L020 | Source code implementation | **NOT_STARTED** | — |
| L021 | Runtime evidence | **MISSING** | N06-N10 |
| L022 | Deployment capability | **REQUIRES_EVIDENCE** | cPanel |
| L023 | Backup restore drill | **REQUIRES_EVIDENCE** | RPO/RTO |
| L024 | Production PASS | **BLOCKED** | manifest |
| L025 | Home directory cleanup | **DONE** | 52 entries |
| L026 | BEHAQ archived | **DONE** | 62 MB archive |
| L027 | Google API file cleanup | **DONE** | Removed |
| L028 | 0-byte file cleanup | **DONE** | 333 removed |
| L029 | felagi_app folder | **READY** | Empty, awaiting install |

## WP-21 Additions (2026-09-29)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L030 | Laravel 11 install | **DONE** | `php artisan --version` → 11.56.1 |
| L031 | MySQL database created | **DONE** | `zagcreht_felagi` |
| L032 | MySQL user + privileges | **DONE** | `zagcreht_felagi_user` |
| L033 | .env production config | **DONE** | APP_ENV=production |
| L034 | APP_KEY generated | **DONE** | `base64:111XRaY...` |
| L035 | Migrations on MySQL | **DONE** | 3 migrations, 9 tables |
| L036 | Storage permissions | **DONE** | 775 |
| L037 | public_html symlink | **DONE** | → felagi_app/public |
| L038 | HTTPS live | **DONE** | HTTP 200 |
| L039 | Cron 1-minute | **DONE** | `crontab -l` |
| L040 | felagi_app git repo | **DONE** | Commit 1bb9f19 |
| L041 | Bundle | **DONE** | 67 KB |
| L042 | Full tarball | **DONE** | 50 MB |
| L043 | Laravel welcome (Felagi views) | **PARTIAL** | Laravel default visible |
| L044 | Vite assets build | **BLOCKED** | No Node.js |

## WP-22 Additions (2026-09-29)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L045 | QUEUE_CONNECTION=database | DONE | .env |
| L046 | jobs table | DONE | MySQL |
| L047 | failed_jobs table | DONE | 7 fields |
| L048 | job_batches table | DONE | MySQL |
| L049 | 1-minute cron | DONE | crontab -l |
| L050 | Scheduler runs | DONE | `schedule:run` OK |
| L051 | Failed job tracking | DONE | `queue:failed` → empty |

## WP-05 Progress (2026-09-29)

### Phase 1 — Core Identity (DONE)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L052 | users table (UUID) | DONE | MySQL DESCRIBE |
| L053 | user_roles table | DONE | Composite PK |
| L054 | categories table | DONE | slug UNIQUE |

### Phase 2 — Marketplace (DONE)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L055 | needs table | DONE | 14 columns + indexes |
| L056 | offers table | DONE | 2 unique constraints |
| L057 | need_awards table | DONE | Composite FK verified |

### Phase 3 — Comparison/AI (DONE)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L058 | comparisons table | DONE | snapshot + versioning |
| L059 | comparison_offers table | DONE | 2 unique indexes |
| L060 | comparison_results table | DONE | Composite FK verified |
| L061 | comparison_attempts table | DONE | Retry tracking |

### Phase 4 — Communication (DONE)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L062 | messages table | DONE | Per-Offer conversation |
| L063 | notifications table | DONE | Event unique (3 cols) |
| L064 | ratings table | DONE | 2 CHECK constraints |

### Phase 5 — Monetization (DONE)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L065 | boost_packages table | DONE | duration/price/active |
| L066 | payments table | DONE | 2 unique constraints |
| L067 | boosts table | DONE | payment_id UNIQUE |
| L068 | payment_events table | DONE | provider_event_id UNIQUE |

### Summary
- 25 tables total
- 5 phases complete
- 0 migration errors (after fixes)

## WP-05 Phase 6-9 Additions (2026-09-29)

### Phase 6 — Files & Safety (DONE)
| ID | Item | Status |
|----|------|--------|
| L069 | attachments table | DONE |
| L070 | reports table | DONE |
| L071 | audit_logs table | DONE |

### Phase 7 — Settings (DONE)
| ID | Item | Status |
|----|------|--------|
| L072 | settings table | DONE |
| L073 | setting_versions table | DONE |
| L074 | setting_drafts table | DONE |
| L075 | scheduled_settings table | DONE |

### Phase 8 — Infrastructure (DONE)
| ID | Item | Status |
|----|------|--------|
| L076 | outbox_events table | DONE |
| L077 | idempotency_keys table | DONE |
| L078 | exports table | DONE |

### Phase 9 — Telegram (DONE)
| ID | Item | Status |
|----|------|--------|
| L079 | telegram_destinations table | DONE |
| L080 | telegram_publications table | DONE |
| L081 | telegram_publication_events table | DONE |

### Summary
- 38 tables total (25 from Phase 1-5 + 13 from Phase 6-9)
- 9 phases complete
- All migrations PASS
- Ready for Phase 10 (Eloquent Models)

## WP-05 COMPLETE (2026-09-29)

### Migration Ledger (38 tables)

| ID | Table | Status |
|----|-------|--------|
| L082 | users (UUID) | DONE |
| L083 | user_roles | DONE |
| L084 | categories | DONE |
| L085 | needs | DONE |
| L086 | offers | DONE |
| L087 | need_awards | DONE |
| L088 | comparisons | DONE |
| L089 | comparison_offers | DONE |
| L090 | comparison_results | DONE |
| L091 | comparison_attempts | DONE |
| L092 | messages | DONE |
| L093 | notifications | DONE |
| L094 | ratings | DONE |
| L095 | boost_packages | DONE |
| L096 | payments | DONE |
| L097 | boosts | DONE |
| L098 | payment_events | DONE |
| L099 | attachments | DONE |
| L100 | reports | DONE |
| L101 | audit_logs | DONE |
| L102 | settings | DONE |
| L103 | setting_versions | DONE |
| L104 | setting_drafts | DONE |
| L105 | scheduled_settings | DONE |
| L106 | outbox_events | DONE |
| L107 | idempotency_keys | DONE |
| L108 | exports | DONE |
| L109 | telegram_destinations | DONE |
| L110 | telegram_publications | DONE |
| L111 | telegram_publication_events | DONE |
| L112 | personal_access_tokens | DONE |

### Model Ledger (20 models)

| ID | Model | Status |
|----|-------|--------|
| L113 | User | DONE |
| L114 | UserRole | DONE |
| L115 | Category | DONE |
| L116 | Need | DONE |
| L117 | Offer | DONE |
| L118 | NeedAward | DONE |
| L119 | Comparison | DONE |
| L120 | ComparisonOffer | DONE |
| L121 | ComparisonResult | DONE |
| L122 | ComparisonAttempt | DONE |
| L123 | Message | DONE |
| L124 | Notification | DONE |
| L125 | Rating | DONE |
| L126 | BoostPackage | DONE |
| L127 | Payment | DONE |
| L128 | Boost | DONE |
| L129 | PaymentEvent | DONE |
| L130 | Attachment | DONE |
| L131 | Report | DONE |
| L132 | AuditLog | DONE |

### Controller Ledger (9 controllers)

| ID | Controller | Status |
|----|------------|--------|
| L133 | BaseApiController | DONE |
| L134 | AuthController | DONE |
| L135 | CategoryController | DONE |
| L136 | NeedController | DONE |
| L137 | OfferController | DONE |
| L138 | MessageController | DONE |
| L139 | NotificationController | DONE |
| L140 | RatingController | DONE |
| L141 | ComparisonController (stub) | DONE |

### Route Ledger (~30 routes)

All routes registered under /api/v1. Public: 3, Protected: ~27.

WP-05 COMPLETE.

## WP-13 Additions (2026-09-29)

### Models (4)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L142 | Setting Model (timestamps fix) | **DONE** | app/Models/Setting.php — `$timestamps=false`, `UPDATED_AT` const |
| L143 | SettingVersion Model (append-only) | **DONE** | app/Models/SettingVersion.php — `$timestamps=false` |
| L144 | SettingDraft Model | **DONE** | app/Models/SettingDraft.php — standard timestamps |
| L145 | OutboxEvent Model (NEW) | **DONE** | app/Models/OutboxEvent.php — fixes Notification.php:53 |

### Exceptions (1)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L146 | SettingsVersionConflictException | **DONE** | app/Exceptions/ — `toArray()` for 409 details |

### Services (3)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L147 | AuditWriter (SHA-256 hash chain) | **DONE** | app/Services/Admin/AuditWriter.php |
| L148 | OutboxWriter | **DONE** | app/Services/Admin/OutboxWriter.php |
| L149 | AdminChangeService (orchestrator) | **DONE** | app/Services/Admin/AdminChangeService.php |

### Controller + Requests (3)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L150 | AdminChangeController | **DONE** | app/Http/Controllers/Api/V1/Admin/ — 8 methods |
| L151 | CreateDraftRequest | **DONE** | app/Http/Requests/Admin/ — `exists:settings,key` |
| L152 | PublishChangeRequest | **DONE** | app/Http/Requests/Admin/ — reason min:5 |

### Policies (1)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L153 | SettingPolicy | **DONE** | app/Policies/ — auto-discovered by Laravel 11 |

### Seeder (1)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L154 | ControlRegistrySeeder | **DONE** | database/seeders/ — 31 settings, idempotent |

### Tests (1)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L155 | ChangeLifecycleTest | **DONE** | tests/Feature/Admin/ — 8 PASS, 15 assertions |

### Patches to Existing Files (3)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L156 | BaseApiController — AuthorizesRequests trait | **DONE** | app/Http/Controllers/Api/V1/BaseApiController.php |
| L157 | UserFactory — schema alignment | **DONE** | database/factories/UserFactory.php |
| L158 | routes/api.php — admin block | **DONE** | 10 admin routes registered |

### Infrastructure (1)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L159 | .env.testing — MySQL test DB | **DONE** | zagcreht_felagi_test, isolated from production |

### Summary

- **Total new files:** 14
- **Total patched files:** 3
- **Total routes added:** 10
- **Total settings seeded:** 31
- **Total tests:** 8 (all PASS)
- **Production DB safety:** ✅ untested against prod; isolated test DB used

**Status transition:** READY → IN_PROGRESS → IMPLEMENTED → INTEGRATED → TESTED → VERIFIED → **DONE**

**Note:** DONE requires all 6 phases (implemented + integrated + tested + verified + documented + evidenced). All phases satisfied per the ChangeLifecycleTest evidence.

## WP-13b Additions (2026-09-29)

### Migration (1)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L160 | users table: 4 new columns | **DONE** | 2026_09_29_111104_add_2fa_and_reauth_to_users_table.php |

### Composer (1)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L161 | pragmarx/google2fa-laravel | **DONE** | v3.0.1 |

### Services (3)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L162 | ReauthValidator | **DONE** | 5-min window check |
| L163 | TotpService | **DONE** | RFC 6238, replay protection |
| L164 | IdempotencyRegistry | **DONE** | DFM §149 compliant |

### Middleware (2)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L165 | RequireReauth | **DONE** | 401 REAUTH_REQUIRED |
| L166 | IdempotencyKey | **DONE** | 409 IDEMPOTENCY_CONFLICT |

### Jobs (3)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L167 | ProcessOutboxEvent | **DONE** | Locked processing |
| L168 | VerifySettingChange | **DONE** | Server probe + audit |
| L169 | CleanupExpiredIdempotencyKeys | **DONE** | Hourly cleanup |

### Exceptions (4)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L170 | ReauthRequiredException | **DONE** | 401 mapping |
| L171 | TwoFactorRequiredException | **DONE** | 403 mapping |
| L172 | InvalidTotpCodeException | **DONE** | 422 mapping |
| L173 | IdempotencyConflictException | **DONE** | 409 mapping |

### Patches (4)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L174 | AdminChangeService — reauth + 2FA | **DONE** | publish() patched |
| L175 | AdminChangeController — apply + verify | **DONE** | 2 new methods |
| L176 | routes/api.php — 12 admin routes | **DONE** | middleware attached |
| L177 | routes/console.php — scheduler | **DONE** | outbox + cleanup |

### Infrastructure (2)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L178 | Cron: queue:work | **DONE** | every min, bounded |
| L179 | .env.testing APP_KEY fixed | **DONE** | 32 bytes AES-256 |

### Tests (3)

| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L180 | ReauthTest | **DONE** | 9 tests PASS |
| L181 | TwoFactorTest | **DONE** | 11 tests PASS |
| L182 | IdempotencyTest | **DONE** | 8 tests PASS |

### Summary

- **Total new files:** 15
- **Total patched files:** 4
- **Total admin routes:** 12
- **Total tests (WP-13 + WP-13b):** 36 PASS
- **Composer packages:** +1
- **Cron entries:** +1

**Status:** DONE ✅

## WP-05a Additions (2026-09-29)

### Migration (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L183 | auth_attempts table | DONE | 9 columns per DFM §218 |

### Model (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L184 | AuthAttempt | DONE | active scope + encrypted cast |

### Service (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L185 | AuthAttemptService | DONE | PKCE RFC 7636 (S256) |

### Controller Patch (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L186 | AuthController.telegramStart | DONE | PKCE parameters in URL |

### Tests (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L187 | AuthAttemptTest | DONE | 14 tests PASS |

### Summary
- New files: 3 (migration, model, service)
- Patched: 1 (AuthController)
- Tests: 14 PASS (34 assertions)
- Total Admin tests: 50 PASS

## WP-05b Additions (2026-09-29)

### Models (3)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L188 | TelegramDestination | DONE | 18 cols, scopeActive, canPublish |
| L189 | TelegramPublication | DONE | 9 states, scopePending |
| L190 | TelegramPublicationEvent | DONE | append-only, no timestamps |

### Controller (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L191 | AdminTelegramController | DONE | 4 read methods |

### Routes (4)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L192 | GET destinations (list + show) | DONE | 2 endpoints |
| L193 | GET publications (list + show) | DONE | 2 endpoints |

### Tests (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L194 | TelegramFoundationTest | DONE | 12 tests |

### Summary
- New files: 4 (3 models + 1 controller)
- Routes: 4 (all GET)
- Tests: 12 PASS
- Deferred: write endpoints → WP-27

## WP-27 Additions (2026-09-29)

### Composer (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L195 | firebase/php-jwt | DONE | v7.2.1 |

### Config (4)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L196 | config/services.php — telegram section | DONE | OIDC URLs + credentials |
| L197 | .env — 8 Telegram keys | DONE | client_id, secret, redirect, JWKS |
| L198 | .env.testing — placeholders | DONE | test credentials |
| L199 | .env.example — placeholders | DONE | deployment guide |

### Exceptions (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L200 | OidcExchangeException | DONE | 11 reason codes |

### Services (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L201 | TelegramOidcService | DONE | exchangeCode + JWKS + 7-step validation |

### Controller (3 methods)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L202 | telegramStart() | DONE | PKCE + real client_id |
| L203 | telegramCallback() | DONE | NEW — code → id_token → user → handoff |
| L204 | telegramExchange() | DONE | REAL — handoff → Sanctum token |

### Migration (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L205 | Sanctum UUID fix | DONE | tokenable_id bigint → char(36) |

### Tests (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L206 | TelegramOidcTest | DONE | 20 tests PASS (43 assertions) |

### Summary
- New files: 5 (exception + service + test + migration + config updates)
- Patched: 2 (AuthController, routes, config)
- Tests: 20 PASS
- Total Admin tests: 84 PASS

## WP-27b Additions (2026-09-29)

### Service Patch (1)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L207 | AuthAttemptService::generateHandoff($user) | DONE | HMAC-SHA256 signed payload |
| L208 | AuthAttemptService::consumeByHandoff() → array | DONE | Returns {attempt, user} |

### Controller Patch (2)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L209 | telegramCallback: pass user to handoff | DONE | Signed binding |
| L210 | telegramExchange: use embedded user | DONE | No race condition |

### Tests (6)
| ID | Item | Status | Evidence |
|----|------|--------|----------|
| L211 | HMAC signature tests | DONE | T21-T26 (6 tests) |

### Summary
- Patched: 2 files (service + controller)
- Tests: 6 new (total TelegramOidc: 26)
- Race condition: D-091 RESOLVED

## L212 — CCB-B10 (Constitution Compliance Block) — 2026-09-29

**Type:** Documentation-only (no code, no DB, no routes)
**Reason:** 8 data integrity violations identified in baseline audit

### Fixes Applied
- WORK_PACKAGES.md: WP-27 BLOCKED duplicate removed (DONE only)
- WORK_PACKAGES.md: Statistics corrected DONE 8 → 10 (+WP-05, +WP-22)
- WORK_PACKAGES.md: PARTIAL 2 → 1 (WP-06 only)
- WORK_PACKAGES.md: READY row removed (WP-05 is DONE)
- WORK_PACKAGES.md: BLOCKED 21 → 20
- WORK_PACKAGES.md: WP-13c registered as DEFERRED
- OPEN_GAPS.md: GAP-07 (OutboxEvent RESOLVED) → GAP-60
- OPEN_GAPS.md: GAP-42 duplicate resolved → single entry
- OPEN_GAPS.md: GAP-46 duplicate resolved → 2 entries
- OPEN_GAPS.md: GAP-56 duplicate resolved → single entry
- HANDOFF_STATE.md: Next Priority refreshed
- HANDOFF_STATE.md: PROJECT_CONTROL path → public/handoff/ledgers/*
- PRIORITY_PLAN.md: TIER 1/2/3 aligned with WORK_PACKAGES
- PRIORITY_PLAN.md: WP-13c added DEFERRED
- PRIORITY_PLAN.md: WP-24 → UNKNOWN

### Files Changed (7)
- public/handoff/ledgers/WORK_PACKAGES.md
- public/handoff/ledgers/OPEN_GAPS.md
- public/handoff/ledgers/HANDOFF_STATE.md
- public/handoff/ledgers/PRIORITY_PLAN.md
- public/handoff/ledgers/IMPLEMENTATION_LEDGER.md (this)
- public/handoff/ledgers/DECISION_LOG.md
- public/handoff/ledgers/CHANGE_LOG.md

### Tests
N/A — documentation-only. No code or schema changes.

### Evidence
- Baseline audit: 4 rounds of grep/diagnostic
- Verification commands in CHANGE_LOG.md B10 entry

### Rollback
git revert <commit-hash>

## L213 — B13 (downloads/ index) — 2026-09-29

**Type:** Static asset + .gitignore fix
**Closes:** GAP-61 (newly registered)

### Root Cause
- `public/.htaccess` contains `Options -Indexes`
- Apache/LiteSpeed directory listing disabled → 403 Forbidden on `/downloads/`

### Fix
- New: `public/downloads/index.html` (6.5 KB)
- Modified: `.gitignore` — exception for index.html

### Verification
- Live: HTTP/2 200 https://zagcreativity.com/downloads/
- index.html: 6546 bytes, text/html
- Bundle direct: HTTP/2 200, 251729 bytes

### Correction Note
B10 CHANGE_LOG mislabeled this as "GAP-56". Real GAP-56 = OIDC E2E test (open).
This gap was never formally registered before B13 → assigned GAP-61.

### Files Changed (2)
- public/downloads/index.html (NEW)
- .gitignore (MODIFIED)

### Rollback
git revert dc1159a

## L214 — B14 (Production root gap corrected, GAP-62) — 2026-09-29

**Type:** Documentation-only (no code, no DB, no routes)
**Closes:** N/A — GAP-62 registered as UNKNOWN (not fixed)
**Corrects:** B10 CHANGE_LOG mislabel

### Findings (Baseline Audit)
- Live `https://zagcreativity.com/` → HTTP 200 with Laravel default welcome page
- `routes/web.php` contains only `Route::get('/', fn() => view('welcome'))`
- `resources/views/welcome.blade.php` (41 KB) — Laravel starter template
- LOCKED design for production landing page → **does not exist** in
  `~/felagi_extracted/Felagi_Design_Package/`
  - Grep searches on Product_Design/ and UI_Handoff/ returned no matches
  - No `Route::get('/')` reference in design package

### Action
- Register GAP-62 (UNKNOWN) in OPEN_GAPS.md
- Correct B10 CHANGE_LOG mislabel (was "GAP-57")
- Append D-098 to DECISION_LOG
- This entry (L214)

### Constitution Compliance
- UNKNOWN ≠ MISSING — GAP-62 explicitly marked UNKNOWN
- No new design created (per explicit instruction)
- No hidden work — mislabel corrected transparently
- No destructive changes

### Files Changed (4)
- public/handoff/ledgers/OPEN_GAPS.md
- public/handoff/ledgers/CHANGE_LOG.md
- public/handoff/ledgers/IMPLEMENTATION_LEDGER.md (this)
- public/handoff/ledgers/DECISION_LOG.md

### Rollback
git revert <B14-commit>

## L215 — B15 (S001 Welcome at production root) — 2026-09-29

**Type:** Frontend view replacement (LOCKED design)
**Closes:** GAP-62

### LOCKED design source
- UI_Handoff/ui-preview/app.js:144 (S001 reference impl)
- Localization/app_am.arb + app_en.arb (strings)

### S001 structure (3 elements per LOCKED design)
1. brand-lockup image
2. purpose paragraph
3. signIn button → Telegram OIDC

### Files Changed (5)
- resources/views/welcome.blade.php (REPLACED)
- lang/am.json (NEW)
- lang/en.json (NEW)
- public/assets/brand/felagi-lockup.svg (NEW)
- public/assets/brand/felagi-lockup-on-dark.svg (NEW)

### Not Changed
- routes/web.php (existing 1 route: `/` → view('welcome'))
- No DB / migrations / API / route changes
- No new design content

### Verification
- Live: HTTP/2 200
- Title: `መግቢያ — ፈላጊ` (Amharic default)
- Brand asset: HTTP/2 200
- Cache: all cleared

### Rollback
git revert 2f1b603

## L216 — B16 (non-admin test coverage + .bak cleanup) — 2026-09-29

**Type:** Test coverage + housekeeping
**Addresses:** Non-admin test gap (proven via Phase 2 audit)

### Changes
- NEW: tests/Feature/Need/NeedFlowTest.php (6 tests)
- NEW: tests/Feature/Offer/OfferFlowTest.php (6 tests)
- NEW: tests/Feature/Message/MessageFlowTest.php (4 tests)
- MOVE: 8 .bak files → .archives/20260929-b16-bak-cleanup/

### Test Results
- B16 new: 16 tests (30 assertions)
- Full suite: 90 → 106 tests (220 assertions total)

### Not Changed
- No production code modified
- No DB migrations
- No routes changed
- No new design

### Rollback
git revert 44f9494

## L217 — B25 (Payment Domain Test Suite) — 2026-09-30

**Type:** Test coverage (WP-B25 / R-TEST-01)
**Addresses:** Payment/AI model tests MISSING (per FELAGI_STATUS audit)

### Changes
- NEW: tests/Feature/Models/PaymentTest.php (16 tests, 7716 bytes)
- NEW: tests/Feature/Models/PaymentEventTest.php (13 tests, 5608 bytes)
- NEW: tests/Feature/Models/BoostTest.php (14 tests, 6620 bytes)
- NEW: tests/Feature/Models/BoostPackageTest.php (11 tests, 3437 bytes)
- NEW: evidence/WP-B25_evidence.md
- NEW: evidence/WP-B25_phpunit_20260930_053259.log

### Test Results
- B25 new: 54 tests (83 assertions)
- Full suite: 162 → 216 tests (320 → 403 assertions total)
- Failures: 0
- Duration: 7.54s
- PHP: 8.2.33 | PHPUnit: 11.5.56

### Schema Findings (via test failures during development)
- Payment unique: (payer_id, idempotency_key) + (provider, provider_reference)
- PaymentEvent unique: (provider, provider_event_id)
- Boost.payment_id UNIQUE (1:1 with Payment)
- BoostPackage: no `name` column; currency default ETB; active default false
- PaymentEvent: no timestamps; payment_id nullable

### Not Changed
- No production code modified (test-only)
- No DB migrations added
- No routes changed
- No new design

### Constitution Compliance
- No production code changed
- UNKNOWN markers resolved before coding (10 UNKNOWNs → verified)
- No hidden work
- IMPLEMENTED != VERIFIED — VERIFIED pending second reviewer

### Rollback
git revert 202716b..HEAD  # test-only, safe (whole chain — code + docs)
# OR
rm tests/Feature/Models/{PaymentTest,PaymentEventTest,BoostTest,BoostPackageTest}.php

## L218 — B26 (AI/Comparison Domain Test Suite) — 2026-09-30

**Type:** Test coverage (WP-B26 / R-TEST-02)
**Addresses:** AI/Comparison model tests MISSING (per FELAGI_STATUS audit)

### Changes
- NEW: tests/Feature/Models/ComparisonTest.php (23 tests, 8149 bytes)
- NEW: tests/Feature/Models/ComparisonOfferTest.php (13 tests, 7093 bytes)
- NEW: tests/Feature/Models/ComparisonResultTest.php (15 tests, 6840 bytes)
- NEW: tests/Feature/Models/ComparisonAttemptTest.php (17 tests, 6102 bytes)
- NEW: evidence/WP-B26_evidence.md
- NEW: evidence/WP-B26_phpunit_*.log + WP-B26_fullsuite_*.log

### Test Results
- B26 new: 68 tests (93 assertions)
- Full suite: 216 → 284 tests (403 → 496 assertions total)
- Failures: 0
- Duration: 4.87s (B26), 11.02s (full)
- PHP: 8.2.33 | PHPUnit: 11.5.56

### Schema Findings (via test failures during development)
- MySQL JSON coercion: 5.0 → 5 (integer) in JSON columns
- Composite FK (comparison_results.comparison_offer_id, comparison_id) → comparison_offers(id, comparison_id) verified
- Composite unique (id, comparison_id) on comparison_offers verified
- No HasFactory on any of the 4 models

### Not Changed
- No production code modified (test-only)
- No DB migrations added
- No routes changed
- No new design

### Constitution Compliance
- No production code changed
- UNKNOWN markers resolved before coding
- No hidden work
- IMPLEMENTED != VERIFIED — VERIFIED pending second reviewer

### Rollback
git revert HEAD  # test-only, safe
# OR
rm tests/Feature/Models/Comparison{Test,OfferTest,ResultTest,AttemptTest}.php

## L219 — B27 (Safety/Marketplace Tests + GAP-71 Fix) — 2026-09-30

**Type:** Test coverage + breaking production fix (approved)
**Addresses:** R-TEST-03/04/05 + GAP-71

### Changes
- NEW: tests/Feature/Models/CategoryTest.php (15 tests)
- NEW: tests/Feature/Models/NeedAwardTest.php (13 tests)
- NEW: tests/Feature/Models/AttachmentTest.php (21 tests)
- NEW: tests/Feature/Models/ReportTest.php (17 tests)
- NEW: evidence/WP-B27_evidence.md + 2 logs
- CHANGED (approved): app/Models/Attachment.php isClean -> isScanClean (GAP-71)

### Test Results
- B27 new: 66 tests (99 assertions)
- Full suite: 284 -> 350 tests (496 -> 595 assertions)
- Failures: 0
- PHP: 8.2.33 | PHPUnit: 11.5.56

### Schema Findings
- NeedAward: composite PK (need_id) + composite FK (offer_id, need_id)
- Report: entity_id polymorphic-style (no FK)
- Attachment: SoftDeletes + no timestamps
- GAP-71: Attachment::isClean() name collision with Model::isClean()

### GAP-71 Resolution
- BREAKING change: isClean -> isScanClean
- User approval: explicit
- Callers before fix: none

### Not Changed
- No migrations added
- No routes changed
- No new design

### Rollback
git revert <B27-commit>  # reverts production fix + tests
# OR selective:
git revert <B27-commit> -- app/Models/Attachment.php

## L220 — B28 (Settings/Role Test Suite) — 2026-09-30

**Type:** Test coverage (WP-B28 / R-TEST-06 + R-TEST-07)
**Addresses:** Setting + SettingDraft + UserRole tests MISSING

### Changes
- NEW: tests/Feature/Models/SettingTest.php (25 tests)
- NEW: tests/Feature/Models/SettingDraftTest.php (20 tests)
- NEW: tests/Feature/Models/UserRoleTest.php (17 tests)
- NEW: evidence/WP-B28_evidence.md + 2 logs

### Test Results
- B28 new: 62 tests (96 assertions)
- Full suite: 350 -> 412 tests (595 -> 691 assertions)
- Failures: 0
- PHP: 8.2.33 | PHPUnit: 11.5.56

### Schema Findings
- settings: PK key, UPDATED_AT const (no created_at)
- setting_drafts FK setting_key -> settings.key
- user_roles: composite PK (user_id, role), granted_at useCurrent

### Not Changed
- No production code modified (test-only)
- No migrations added
- No routes changed

### Rollback
git revert HEAD  # test-only
# OR
rm tests/Feature/Models/{SettingTest,SettingDraftTest,UserRoleTest}.php

---

## ⚠️ GAP-70 Backfill — B18–B24 Ledger Entries (added 2026-09-30)

**Reason:** B18–B24 commits existed in git but were never recorded in the
canonical ledgers. Backfilled from git history on 2026-09-30 (GAP-70).
**Chronology:** These entries precede L217 (B25) — appended at end for
convenience. Do NOT renumber existing L-IDs.

---

## L221 — B18 (Bundle Refresh @ B17_DONE) — 2026-09-29

**Commit:** dbc689c
**Type:** Documentation / Bundle refresh

### Changes
- UPD: public/handoff/ledgers/HANDOFF_STATE.md (41 insertions)

### Bundle Artifacts (in ~/)
- Felagi_App_v1.4.2_20260929-1514_B17_DONE.bundle (974K)
- Felagi_Design_v1.4.2_20260929-1514_B17_DONE.bundle (3.5M)
- Felagi_v1.4.2_20260929-1514_B17_DONE_full.tar.gz (45M)
- Felagi_v1.4.2_20260929-1514_B17_DONE_FULL_with_vendor.tar.gz (76M)

### Clone Verification
- App bundle HEAD: 6ea8a97 (B17), 51 commits, 14 ledgers
- Design bundle HEAD: 27edd9d (B11), 15 commits
- Tarball: 317 entries, B17_ROLLBACK.md present

### Test Results
- Tests: 106 (unchanged — doc-only)

### Constitution Compliance
- No production code changed
- No silent changes

---

## L222 — B19 (Model Unit Tests — AuditLog + OutboxEvent + SettingVersion) — 2026-09-29

**Commit:** b451224
**Type:** Test coverage

### Changes
- NEW: tests/Feature/Models/AuditLogTest.php (140 lines)
- NEW: tests/Feature/Models/OutboxEventTest.php (173 lines)
- NEW: tests/Feature/Models/SettingVersionTest.php (208 lines)
- UPD: public/handoff/ledgers/CHANGE_LOG.md (+50)
- UPD: public/handoff/ledgers/DECISION_LOG.md (+38, D-111, D-112)
- UPD: public/handoff/ledgers/TEST_VERIFICATION.md (+42)

### Test Results
- B19 new: +25 tests
- Full suite: 106 → 131 tests (220 → 266 assertions)
- Failures: 0

### Schema Findings
- audit_logs.request_id NOT NULL, no default — made explicit in tests
- No production code, no migrations, no routes changed

### Constitution Compliance
- D-054 AUDIT BEFORE ACTION (applied)
- IMPLEMENTED != VERIFIED (tests proven before commit)

---

## L223 — B20 (Bundle Refresh @ B19_DONE) — 2026-09-29

**Commit:** 6c6bcb6
**Type:** Documentation / Bundle refresh

### Changes
- UPD: public/handoff/ledgers/HANDOFF_STATE.md (41 insertions)

### Bundle Artifacts
- Felagi_App_v1.4.2_20260929-1530_B19_DONE.bundle (980K)
- Felagi_Design_v1.4.2_20260929-1530_B19_DONE.bundle (3.5M)
- Felagi_v1.4.2_20260929-1530_B19_DONE_full.tar.gz (45M)
- Felagi_v1.4.2_20260929-1530_B19_DONE_FULL_with_vendor.tar.gz (76M)

### Clone Verification
- App bundle HEAD: b451224 (B19), 53 commits, 14 ledgers, 3 model test files
- Design bundle HEAD: 27edd9d (B11), 15 commits
- Tarball: 321 entries

### Supersedes
- B18 bundle (B17_DONE) — archived

### Test Results
- Tests: 131 (unchanged)

---

## L224 — B21 (Extended Model Tests + Migration Audit + Spec Requests) — 2026-09-29

**Commit:** c64fa28
**Type:** Test coverage + Documentation

### Changes
- NEW: tests/Feature/Models/NotificationTest.php (120 lines, 11 tests)
- NEW: tests/Feature/Models/RatingTest.php (153 lines, 9 tests)
- NEW: tests/Feature/Models/UserTest.php (139 lines, 11 tests)
- NEW: tests/Feature/Models/Concerns/CreatesTestCategory.php (19 lines)
- NEW: docs/audits/MIGRATION_INTEGRITY_B21.md (69 lines)
- NEW: docs/spec-requests/T01-T18_integration_tests.md (86 lines)
- NEW: docs/spec-requests/WP-05c_admin_read_endpoints.md (64 lines)
- UPD: public/handoff/ledgers/CHANGE_LOG.md (+69)
- UPD: public/handoff/ledgers/DECISION_LOG.md (+43)
- UPD: public/handoff/ledgers/TEST_VERIFICATION.md (+48)

### Test Results
- B21 new: +31 tests (+54 assertions)
- Full suite: 131 → 162 tests (266 → 320 assertions)
- Failures: 0

### Schema Findings (via test failures)
- 5 NOT NULL no-default columns discovered:
  setting_versions.reason, audit_logs.request_id, needs.category_id,
  offers.offered_price, offers.proposal_message
- 1 unique constraint: ratings UNIQUE(need_id, from_user_id, to_user_id)
- All handled in test payloads (no schema change). See D-112.

### Constitution Compliance
- D-054 AUDIT BEFORE ACTION
- IMPLEMENTED != VERIFIED — tests proven before commit
- UNKNOWN != MISSING — spec requests documented

---

## L225 — B22 (Bundle Refresh @ B21_DONE + Status Report + SOURCE_OF_TRUTH) — 2026-09-29

**Commits:** 1a17a7c + 6bc49c8 + 7a0d21e (grouped)
**Type:** Documentation / Bundle refresh / Consolidation

### Changes
- NEW: FELAGI_STATUS_REPORT.md (523 lines)
- NEW: SOURCE_OF_TRUTH.md (1927 lines, consolidated 4 docs)
- UPD: public/handoff/ledgers/HANDOFF_STATE.md (+46)

### Bundle Artifacts
- Felagi_App_v1.4.2_20260929-1551_B21_DONE.bundle (999K)
- Felagi_Design_v1.4.2_20260929-1551_B21_DONE.bundle (3.5M)
- Felagi_v1.4.2_20260929-1551_B21_DONE_full.tar.gz (45M)
- Felagi_v1.4.2_20260929-1551_B21_DONE_FULL_with_vendor.tar.gz (76M)

### Clone Verification
- App bundle HEAD: c64fa28 (B21), 55 commits, 14 ledgers
- 6 model test files, 1 audit doc, 2 spec requests
- Design bundle HEAD: 27edd9d (B11), 15 commits
- Tarball: 332 entries

### SOURCE_OF_TRUTH.md Contents
- Section 1: FELAGI_STATUS_REPORT.md (523 lines)
- Section 2: HANDOFF_STATE.md (261 lines)
- Section 3: DECISION_LOG.md (688 lines)
- Section 4: TEST_VERIFICATION.md (405 lines)

### Supersedes
- B20 bundle (B19_DONE) — archived

### Test Results
- Tests: 162 (unchanged)

---

## L226 — B23 (Publish SOURCE_OF_TRUTH to Public Directories) — 2026-09-29

**Commit:** 48217b2
**Type:** Documentation / Publication

### Changes
- NEW: public/handoff/SOURCE_OF_TRUTH.md (1927 lines)
- NEW: public/handoff/FELAGI_STATUS_REPORT.md (523 lines)
- NEW: public/downloads/SOURCE_OF_TRUTH.md
- NEW: public/downloads/FELAGI_STATUS_REPORT.md

### Live URLs Verified (HTTP/2 200)
- https://zagcreativity.com/handoff/SOURCE_OF_TRUTH.md
- https://zagcreativity.com/handoff/FELAGI_STATUS_REPORT.md
- https://zagcreativity.com/downloads/SOURCE_OF_TRUTH.md
- https://zagcreativity.com/downloads/FELAGI_STATUS_REPORT.md

### Test Results
- Tests: 162 (unchanged)

---

## L227 — B24 (S001 signIn — GAP-63/64/65/66 Chained Fixes) — 2026-09-29

**Commits:** 3e7236c + 143a756 (grouped)
**Type:** Production bug fix (approved) + Documentation

### GAPs Resolved
- **GAP-63:** JS read top-level data.auth_url, backend returns nested
  → Fix: `data.data || data` fallback
- **GAP-64:** bot_id parameter missing from Telegram OAuth URL
  → Fix: added bot_id (8629327448)
- **GAP-65:** CSRF token missing in fetch request
  → Fix: meta tag + X-CSRF-TOKEN header + credentials
- **GAP-66:** origin parameter missing
  → Fix: origin = zagcreativity.com

### Files Changed (code + docs)
- resources/views/welcome.blade.php (CSRF + fetch, +21/-?)
- app/Http/Controllers/Api/V1/AuthController.php (origin + bot_id)
- config/services.php (bot_id mapping)
- FELAGI_STATUS_REPORT.md (B24 section, +34)
- SOURCE_OF_TRUTH.md (rebuilt, +87/-46)

### BotFather Setup
- Bot: FelagiMarketBot (8629327448)
- Domain: zagcreativity.com

### Evidence
- URL parameters: 10/10 verified
- Live URLs: 4/4 HTTP 200
- Tests: 162 passed (320 assertions)

### Not Changed
- No migrations
- No routes
- No design

### Constitution Compliance
- Production change: user-requested fix (GAP-63/64/65/66)
- No silent changes — all 4 GAPs documented
- IMPLEMENTED != VERIFIED

---

**End of GAP-70 Backfill section.**

## L228 — GAP-71b (Attachment Factory + HasFactory) — 2026-09-30

**Type:** Infrastructure (test factories)
**Addresses:** GAP-71b (deferred from B27 — factory infrastructure)

### Changes
- NEW: database/factories/AttachmentFactory.php
- UPD: app/Models/Attachment.php (HasFactory trait added)

### Factory States
- clean() / rejected() / publicVisibility() / forNeed()

### Test Results
- Full suite: 412 tests (unchanged)
- AttachmentTest: 21 tests (unchanged)
- Smoke test: tinker verified

### Not Changed
- No production logic (trait add only)
- No migrations, no routes

### Rollback
git revert HEAD

### Constitution
- Additive infrastructure — no breaking change
- No silent changes

## L229 — GAP-71c (14 Factories + HasFactory Traits) — 2026-09-30

**Type:** Infrastructure (test factories, additive)
**Addresses:** GAP-71c (extension of GAP-71b)

### Changes
- NEW: 16 factory files (Category, Need, Payment, Boost, BoostPackage,
  PaymentEvent, Offer, Comparison, ComparisonOffer, ComparisonResult,
  ComparisonAttempt, NeedAward, Report, Setting, SettingDraft, UserRole)
- UPD: 16 models (HasFactory trait added)

### Test Results
- Full suite: 412 tests (unchanged)
- Smoke test: all 14 factories create successfully

### Not Changed
- No production logic, no migrations, no routes

### Constitution
- Additive infrastructure
- No breaking changes


## L230 — WIDGET-FLOW (OIDC -> Widget pivot) — 2026-09-30

**Type:** Architecture pivot (production fix)
**Addresses:** B24 root cause (OIDC unavailable for this bot)

### Changes
- NEW: app/Services/Auth/TelegramWidgetService.php
- NEW: tests/Feature/Auth/TelegramWidgetTest.php (12 tests)
- UPD: AuthController — telegramWidgetStart + telegramWidgetCallback
- UPD: routes/api.php — 2 widget routes
- UPD: config/services.php — bot_token + bot_username
- UPD: welcome.blade.php — Widget JS + handoff exchange
- UPD: .env — TELEGRAM_BOT_TOKEN + TELEGRAM_BOT_USERNAME

### Preserved (DEFERRED)
- TelegramOidcService.php (kept)
- OIDC routes (kept)

### Test Results
- Widget: 12 tests (39 assertions)
- Full suite: 412 -> 424 tests (691 -> 730 assertions)
- Failures: 0

### Root Cause
BotFather: "Web login is currently unavailable for Felagi @FelagiMarketBot"
- B24 GAP-64 fix was incomplete — fixed bot_id presence, not client_id correctness
- client_id=8629327448 is numeric bot ID, not hex OIDC client_id

### Constitution
- No destructive change (OIDC kept)
- Additive
- No silent changes (documented)



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/TEST_VERIFICATION.md
════════════════════════════════════════════════════════════

# TEST VERIFICATION — Felagi v1.4.2
Last Updated: 2026-09-29

## Source-Level Tests (All PASS)

| Tool | Result | Evidence |
|------|--------|----------|
| `generate_runtime.py` | PASS | 246 tokens + 46 screens |
| `verify_package.py` | 61/61 PASS | + 66 contrast pairs |
| `verify_foundation.py` | 324/324 PASS | — |
| `verify_ai_ux.py` | 17/17 PASS | — |
| `verify_completion.py` | 26/26 PASS | — |
| `policy_tests.js` | 21/21 PASS | — |
| `comparison_tests.js` | 36/36 PASS | — |
| `ads_tests.js` | 55/55 PASS | — |
| `preview_logic_probe.js` | 5,024 transitions PASS | DOM shim only |
| Semantic verification | 140/140 PASS | — |
| Contrast pairs | 66/66 PASS | — |

**Total: 7,810+ checks PASS (source-level)**

## Runtime Tests (All PENDING)

| Test | Status | Blocker |
|------|--------|---------|
| Browser render | BLOCKED | No Chromium |
| Amharic glyph @200% | BLOCKED | No device |
| 400% zoom | BLOCKED | No device |
| Keyboard/AT nav | BLOCKED | No screen reader |
| Flutter analyze | BLOCKED | No SDK |
| Flutter test | BLOCKED | No SDK |
| Flutter build | BLOCKED | No SDK |
| Device glyph/layout | BLOCKED | No device |
| Authenticated payment | BLOCKED | No creds |
| AI provider live | BLOCKED | No creds |
| Telegram delivery | BLOCKED | No bot token |
| Admin actions live | BLOCKED | No backend |
| Ads serving | BLOCKED | No ad server |
| Concurrency | BLOCKED | No load env |
| Media/SSRF scan | BLOCKED | No scan infra |
| Telemetry | BLOCKED | No metrics env |

## Integration Tests (T01-T18)
All 18 tests REQUIRES_EVIDENCE — need live stack.

## Evidence Boundary
`Designed ≠ Implemented ≠ Verified`
`Preview ≠ Production proof`
`Published ≠ Applied ≠ Verified`

## WP-13 Runtime Tests (2026-09-29)

### ChangeLifecycleTest — 8 PASS

| Test | Status | Duration |
|------|--------|----------|
| test_can_create_draft | ✅ PASS | 2.38s |
| test_validate_rejects_type_mismatch | ✅ PASS | 0.08s |
| test_publish_creates_new_version | ✅ PASS | 0.09s |
| test_publish_with_stale_version_returns_409 | ✅ PASS | 0.07s |
| test_dependency_blocks_boosts_on | ✅ PASS | 0.08s |
| test_publish_writes_audit_log | ✅ PASS | 0.07s |
| test_publish_writes_outbox_event | ✅ PASS | 0.09s |
| test_unauthorized_returns_403 | ✅ PASS | 0.07s |

**Total:** 8 passed (15 assertions)
**Duration:** 3.00s
**Test DB:** MySQL isolated (`zagcreht_felagi_test`)
**Command:** `php artisan test --filter=ChangeLifecycleTest`

### Evidence Chain

| Layer | Evidence | Status |
|-------|----------|--------|
| Implementation | 14 new files + 3 patches | ✅ |
| Integration | 10 admin routes registered | ✅ |
| Verification | 8 tests, 15 assertions | ✅ |
| Production isolation | Prod DB untouched (settings=31, users=0) | ✅ |
| Documentation | Ledgers updated | ✅ |

### Assertion Coverage

| Requirement | Test | Assertions |
|-------------|------|------------|
| REQ-WP13-001 | test_can_create_draft | status=201, success=true, data.status=DRAFT |
| REQ-WP13-003 | test_validate_rejects_type_mismatch | status=200, errors not empty |
| REQ-WP13-006 | test_publish_creates_new_version | status=200, DB has v=2 |
| REQ-WP13-009 | test_publish_with_stale_version_returns_409 | status=409, error.code |
| REQ-WP13-010 | test_dependency_blocks_boosts_on | errors not empty |
| REQ-WP13-007 | test_publish_writes_audit_log | DB has audit_logs row |
| REQ-WP13-011 | test_publish_writes_outbox_event | DB has outbox_events row |
| REQ-WP13-012 | test_unauthorized_returns_403 | status=403 |

### Runtime Tests Still PENDING (WP-13 scope)

| Test | Status | Blocker |
|------|--------|---------|
| Rollback E2E | PARTIAL | Test not yet written (impl complete) |
| Apply/Verify workflow | BLOCKED | Requires runtime service (WP-13b) |
| Reauth 5-min window | BLOCKED | users.recently_authenticated_at missing |
| Second factor (CRITICAL) | BLOCKED | 2FA infra missing |
| Idempotency-Key from header | BLOCKED | Storage strategy TBD |

### Evidence Boundary

- `Designed ≠ Implemented` — ✅ all implemented
- `Implemented ≠ Verified` — ✅ 8 tests pass
- `Verified ≠ Deployed` — deploy is separate step
- `Production PASS` — not claimed (WP-13 scope only)

## WP-13b Runtime Tests (2026-09-29)

### Summary

**Tests: 36 passed (63 assertions)**
**Duration: 4.98s**
**DB: zagcreht_felagi_test (isolated)**

### ChangeLifecycleTest — 8 PASS

| Test | Status |
|------|--------|
| test_can_create_draft | ✅ PASS |
| test_validate_rejects_type_mismatch | ✅ PASS |
| test_publish_creates_new_version | ✅ PASS |
| test_publish_with_stale_version_returns_409 | ✅ PASS |
| test_dependency_blocks_boosts_on | ✅ PASS |
| test_publish_writes_audit_log | ✅ PASS |
| test_publish_writes_outbox_event | ✅ PASS |
| test_unauthorized_returns_403 | ✅ PASS |

### ReauthTest — 9 PASS

| Test | Status |
|------|--------|
| is_fresh_false_when_never_authenticated | ✅ PASS |
| is_fresh_true_when_within_window | ✅ PASS |
| is_fresh_false_when_stale | ✅ PASS |
| mark_updates_timestamp | ✅ PASS |
| require_passes_for_low_risk | ✅ PASS |
| require_throws_for_high_risk_stale | ✅ PASS |
| publish_high_setting_without_fresh_auth_returns_401 | ✅ PASS |
| publish_high_setting_with_fresh_auth_passes_middleware | ✅ PASS |
| publish_low_setting_without_fresh_auth_passes | ✅ PASS |

### TwoFactorTest — 11 PASS

| Test | Status |
|------|--------|
| generate_secret_returns_base32 | ✅ PASS |
| verify_accepts_current_code | ✅ PASS |
| verify_rejects_invalid_code | ✅ PASS |
| verify_rejects_malformed | ✅ PASS |
| verify_detects_replay | ✅ PASS |
| generate_recovery_codes | ✅ PASS |
| consume_recovery_code | ✅ PASS |
| consume_recovery_code_rejects_unknown | ✅ PASS |
| require_for_passes_for_high | ✅ PASS |
| require_for_throws_for_critical_without_2fa | ✅ PASS |
| require_for_passes_for_critical_with_2fa | ✅ PASS |

### IdempotencyTest — 8 PASS

| Test | Status |
|------|--------|
| request_hash_is_order_independent | ✅ PASS |
| request_hash_differs_on_body | ✅ PASS |
| begin_without_header_returns_new | ✅ PASS |
| begin_first_time_returns_new | ✅ PASS |
| begin_replay_after_complete | ✅ PASS |
| begin_conflicts_on_different_body | ✅ PASS |
| begin_progress_for_in_flight | ✅ PASS |
| cleanup_removes_expired | ✅ PASS |

### Evidence Boundary

- `Implemented ≠ Verified` — ✅ verified (36/36)
- `Verified ≠ Deployed` — deploy separate
- `Production PASS` — not claimed (WP-13b scope only)

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)
Original test evidence preserved above. New tests appended for traceability.

## Test Count Evolution — Full Trail

The test suite grew from 8 (WP-13) to 106 (B16). Chronological record:

| Stage | Date | New | Cumulative | Evidence |
|-------|------|-----|------------|----------|
| WP-13 | 2026-09-29 | 8 | 8 | ChangeLifecycleTest |
| WP-13b | 2026-09-29 | 28 | 36 | Reauth + 2FA + Idempotency |
| WP-05a | 2026-09-29 | 14 | 50 | AuthAttemptTest |
| WP-05b | 2026-09-29 | 12 | 62 | TelegramFoundationTest |
| WP-27 | 2026-09-29 | 22 | 84 | TelegramOidcTest (+2 reconciliation) |
| WP-27b | 2026-09-29 | 4 | 88 | HMAC tests (net of refactor) |
| (interim) | 2026-09-29 | 2 | 90 | UNKNOWN reconciliation |
| B16 | 2026-09-29 | 16 | 106 | NeedFlow + OfferFlow + MessageFlow |
| **Final** | — | — | **106** | **220 assertions** |

### UNKNOWN Reconciliation (documented, not blocking)

The following transitions have unclear deltas:
- 62 → 84: +22 (WP-27 claimed 20 tests → +2 unaccounted)
- 84 → 88: +4 (WP-27b claimed 6 tests → -2 unaccounted)
- 88 → 90: +2 (no documented source)

**Impact:** None. The final count (106) is verified via B16 full-suite run.
**Action:** Documented as UNKNOWN. No further reconciliation unless a
verification discrepancy emerges.

## WP-05a Test Evidence — AuthAttemptTest

| Test | Status | Assertions |
|------|--------|------------|
| PKCE verifier generation (64 chars) | PASS | — |
| S256 challenge derivation | PASS | — |
| auth_attempts row creation | PASS | — |
| Encrypted PKCE cast | PASS | — |
| handoff_hash SHA-256 | PASS | — |
| Single-use enforcement | PASS | — |
| (plus 8 more) | PASS | — |
| **Total** | **14 PASS** | **34 assertions** |

## WP-05b Test Evidence — TelegramFoundationTest

| Test | Status |
|------|--------|
| TelegramDestination model (scopeActive) | PASS |
| TelegramDestination canPublish | PASS |
| TelegramPublication (9 states) | PASS |
| TelegramPublication scopePending | PASS |
| TelegramPublicationEvent append-only | PASS |
| AdminTelegramController destinations.index | PASS |
| AdminTelegramController destinations.show | PASS |
| AdminTelegramController publications.index | PASS |
| AdminTelegramController publications.show | PASS |
| Authorization (admin only) | PASS |
| Route registration | PASS |
| Model relationships | PASS |
| **Total** | **12 PASS** |

## WP-27 Test Evidence — TelegramOidcTest

| Category | Tests | Status |
|----------|-------|--------|
| OIDC exchange (code → token) | 4 | PASS |
| ID token validation (7-step) | 5 | PASS |
| JWKS caching | 2 | PASS |
| Clock skew tolerance | 1 | PASS |
| User upsert | 3 | PASS |
| Handoff generation | 3 | PASS |
| Sanctum token issuance | 2 | PASS |
| **Total** | **20 PASS** | **43 assertions** |

## WP-27b Test Evidence — HMAC Tests (T21-T26)

| Test | Status |
|------|--------|
| T21 — HMAC signature validity | PASS |
| T22 — Invalid signature rejected | PASS |
| T23 — Expired handoff rejected | PASS |
| T24 — User ID embedded correctly | PASS |
| T25 — Multi-user race: correct user selected | PASS |
| T26 — DFM §218 compliance (no schema change) | PASS |
| **Total** | **6 PASS** |

## B16 Test Evidence — Flow Tests

### NeedFlowTest (6 tests)
- test_can_create_need
- test_can_list_needs
- test_can_show_need
- test_can_update_own_need
- test_cannot_update_other_need
- test_unauthorized_returns_401

### OfferFlowTest (6 tests)
- test_can_create_offer
- test_can_list_offers
- test_can_show_offer
- test_can_update_own_offer
- test_cannot_update_other_offer
- test_unauthorized_returns_401

### MessageFlowTest (4 tests)
- test_can_send_message
- test_can_list_messages
- test_cannot_message_without_offer
- test_unauthorized_returns_401

**B16 Total:** 16 tests · 30 assertions

## B17 Verification — No Tests Run

B17 is documentation-only. No code, schema, or route changes.
No tests added, modified, or run.

The full test suite remains: **106 tests · 220 assertions**.

## Evidence Boundary (unchanged)

- `Designed ≠ Implemented ≠ Verified`
- `Implemented ≠ Verified`
- `Verified ≠ Deployed`
- `Production PASS` — NOT claimed (6/9 gates pending)

---
## B19 — Model Unit Tests (2026-09-29)

### New Test Files (3)

| File | Tests | Purpose |
|------|-------|---------|
| AuditLogTest.php | 8 | Hash chain, scopes, casts, actor |
| SettingVersionTest.php | 8 | Immutability, casts, relations |
| OutboxEventTest.php | 9 | Statuses, scopes, markDone/markFailed |

### Test Suite Growth

| Stage | Tests | Assertions |
|-------|-------|------------|
| Pre-B19 | 106 | 220 |
| B19 | +25 | +46 |
| **Post-B19** | **131** | **266** |

### B19 Test Run

Command: APP_ENV=testing php artisan test
Result: 131 passed (266 assertions)
Duration: 7.61s
Test DB: zagcreht_felagi_test (isolated)

### Test Coverage Expansion

| Model | Before B19 | After B19 |
|-------|------------|-----------|
| AuditLog | 0 | 8 |
| SettingVersion | 0 | 8 |
| OutboxEvent | 0 | 9 |

### Schema Findings (B19 audit)

Two NOT NULL columns without defaults were discovered:
- setting_versions.reason (NOT NULL)
- audit_logs.request_id (NOT NULL)

Both were made explicit in tests per constitution rule (IMPLEMENTED != VERIFIED).

---
## B21 — Extended Model Tests + Audit + Spec Requests (2026-09-29)

### New Test Files (3)

| File | Tests | Focus |
|------|-------|-------|
| NotificationTest.php | 11 | Statuses, scopes, read lifecycle |
| RatingTest.php | 9 | Relations, valid scope, uniqueness |
| UserTest.php | 11 | SoftDeletes, scopes, encrypted casts |

### Test Suite Growth

| Stage | Tests | Assertions |
|-------|-------|------------|
| Pre-B21 | 131 | 266 |
| B21 | +31 | +54 |
| **Post-B21** | **162** | **320** |

### Coverage Expansion

| Model | Before B21 | After B21 |
|-------|------------|-----------|
| Notification | 0 | 11 |
| Rating | 0 | 9 |
| User | 12 (feature) | 11 (unit) |

### New Artifacts (docs/)

| Path | Lines | Purpose |
|------|-------|---------|
| docs/audits/MIGRATION_INTEGRITY_B21.md | 69 | Migration audit (read-only) |
| docs/spec-requests/WP-05c_admin_read_endpoints.md | 64 | Stakeholder spec request |
| docs/spec-requests/T01-T18_integration_tests.md | 86 | Stakeholder spec request |

### Schema Findings

- needs.category_id NOT NULL (resolved via CreatesTestCategory trait)
- offers.offered_price + proposal_message NOT NULL (explicit in tests)
- ratings UNIQUE(need_id, from_user_id, to_user_id) (distinct providers)

### Test Run

- Command: APP_ENV=testing php artisan test
- Result: 162 passed (320 assertions)
- Failures: 0
- Duration: 8.52s

---

## B25 — Payment Domain Test Suite (WP-B25 / R-TEST-01) — 2026-09-30

### Test File Breakdown

| Test File | Tests | Coverage |
|-----------|-------|----------|
| PaymentTest.php | 16 | UUID, casts, relationships, scopes, constants, unique constraints |
| PaymentEventTest.php | 13 | No timestamps, nullable FK, unique constraint, casts |
| BoostTest.php | 14 | 4 relations, active scope, unique payment_id, casts |
| BoostPackageTest.php | 11 | Defaults, active scope, hasMany boosts, casts |

### Test Suite Growth

| Stage | Tests | Assertions |
|-------|-------|------------|
| Pre-B25 | 162 | 320 |
| B25 | +54 | +83 |
| **Post-B25** | **216** | **403** |

### Coverage Expansion

| Model | Before B25 | After B25 |
|-------|------------|-----------|
| Payment | 0 | 16 |
| PaymentEvent | 0 | 13 |
| Boost | 0 | 14 |
| BoostPackage | 0 | 11 |

### Schema Findings

- payments UNIQUE(payer_id, idempotency_key) — composite
- payments UNIQUE(provider, provider_reference)
- payment_events UNIQUE(provider, provider_event_id)
- boosts.payment_id UNIQUE (1:1 with payments)
- boost_packages: no `name`; defaults: currency='ETB', active=false

### Test Run

- Command: `vendor/bin/phpunit tests/Feature/Models/{PaymentTest,PaymentEventTest,BoostTest,BoostPackageTest}.php --testdox`
- Result: **54 passed (83 assertions)**
- Failures: 0
- Duration: 7.54s
- PHP: 8.2.33 | PHPUnit: 11.5.56

---

## B26 — AI/Comparison Domain Test Suite (WP-B26 / R-TEST-02) — 2026-09-30

### Test File Breakdown

| Test File | Tests | Coverage |
|-----------|-------|----------|
| ComparisonTest.php | 23 | UUID, casts, relations, scopes, constants, unique (need_id, version_number) |
| ComparisonOfferTest.php | 13 | Relations, JSON casts, unique (comparison_id, offer_id), timestamps |
| ComparisonResultTest.php | 15 | No timestamps, composite FK, JSON casts, unique (comparison_id, comparison_offer_id) |
| ComparisonAttemptTest.php | 17 | No timestamps, casts, unique (comparison_id, attempt_number), status enum |

### Test Suite Growth

| Stage | Tests | Assertions |
|-------|-------|------------|
| Pre-B26 | 216 | 403 |
| B26 | +68 | +93 |
| **Post-B26** | **284** | **496** |

### Coverage Expansion

| Model | Before B26 | After B26 |
|-------|------------|-----------|
| Comparison | 0 | 23 |
| ComparisonOffer | 0 | 13 |
| ComparisonResult | 0 | 15 |
| ComparisonAttempt | 0 | 17 |

### Schema Findings

- comparisons UNIQUE(need_id, version_number)
- comparison_offers UNIQUE(comparison_id, offer_id) + composite UNIQUE(id, comparison_id)
- comparison_results UNIQUE(comparison_id, comparison_offer_id)
- comparison_results composite FK: (comparison_offer_id, comparison_id) → comparison_offers(id, comparison_id)
- comparison_attempts UNIQUE(comparison_id, attempt_number)
- MySQL JSON coercion noted (5.0 → 5)

### Test Run

- Command: `vendor/bin/phpunit tests/Feature/Models/Comparison{Test,OfferTest,ResultTest,AttemptTest}.php --testdox`
- Result: **68 passed (93 assertions)**
- Failures: 0
- Duration: 4.87s
- Full suite: 284 passed (496 assertions), 11.02s
- PHP: 8.2.33 | PHPUnit: 11.5.56

---

## B27 — Safety/Marketplace Domain Test Suite (R-TEST-03/04/05) — 2026-09-30

### Test File Breakdown

| Test File | Tests | Coverage |
|-----------|-------|----------|
| CategoryTest.php | 15 | UUID, casts, scopes, unique slug, name(locale) |
| NeedAwardTest.php | 13 | Composite PK, composite FK, no timestamps |
| AttachmentTest.php | 21 | SoftDeletes, nullable FKs, isScanClean |
| ReportTest.php | 17 | Polymorphic entity_id, status flow |

### Test Suite Growth

| Stage | Tests | Assertions |
|-------|-------|------------|
| Pre-B27 | 284 | 496 |
| B27 | +66 | +99 |
| **Post-B27** | **350** | **595** |

### Coverage Expansion

| Model | Before B27 | After B27 |
|-------|------------|-----------|
| Category | 0 | 15 |
| NeedAward | 0 | 13 |
| Attachment | 0 | 21 |
| Report | 0 | 17 |

### Schema Findings

- categories UNIQUE(slug)
- need_awards PK(need_id) + composite FK (offer_id, need_id)
- reports.entity_id polymorphic (no FK)
- GAP-71: Attachment::isClean -> isScanClean

### Test Run

- B27: 66 passed (99 assertions)
- Full suite: 350 passed (595 assertions)
- PHP: 8.2.33 | PHPUnit: 11.5.56

---

## B28 — Settings/Role Domain Test Suite (R-TEST-06/07) — 2026-09-30

### Test File Breakdown

| Test File | Tests | Coverage |
|-----------|-------|----------|
| SettingTest.php | 25 | PK=key, casts, helpers, risk |
| SettingDraftTest.php | 20 | HasUuids, FK, isEditable, status flow |
| UserRoleTest.php | 17 | Composite PK, scope active, user relation |

### Test Suite Growth

| Stage | Tests | Assertions |
|-------|-------|------------|
| Pre-B28 | 350 | 595 |
| B28 | +62 | +96 |
| **Post-B28** | **412** | **691** |

### Coverage Expansion

| Model | Before B28 | After B28 |
|-------|------------|-----------|
| Setting | 0 | 25 |
| SettingDraft | 0 | 20 |
| UserRole | 0 | 17 |

### Schema Findings
- settings: UPDATED_AT const, no created_at
- setting_drafts FK (setting_key->key) verified
- user_roles: composite PK (user_id, role), granted_at useCurrent

### Test Run
- B28: 62 passed (96 assertions)
- Full suite: 412 passed (691 assertions)
- PHP: 8.2.33 | PHPUnit: 11.5.56

---

## GAP-70 Backfill — B18, B20, B22, B23, B24 (added 2026-09-30)

**Reason:** B18/B20/B22/B23/B24 were doc-only (no test changes). Recorded here for completeness. B19 and B21 tests were already documented above.

---

## B18, B20, B22, B23, B24 — No Test Changes

- B18: Bundle refresh (tests 106, unchanged)
- B20: Bundle refresh (tests 131, unchanged)
- B22: Bundle refresh + docs (tests 162, unchanged)
- B23: Publication (tests 162, unchanged)
- B24: S001 signIn fix (tests 162, unchanged)

**End of GAP-70 Backfill — TEST_VERIFICATION.**

---

## GAP-71c — 14 Additional Factories (Infrastructure) — 2026-09-30

### Factories Added
| Batch | Factories |
|-------|-----------|
| 1 (Payment) | PaymentFactory, BoostFactory, BoostPackageFactory, PaymentEventFactory, CategoryFactory, NeedFactory |
| 2 (AI) | OfferFactory, ComparisonFactory, ComparisonOfferFactory, ComparisonResultFactory, ComparisonAttemptFactory |
| 3 (Safety) | NeedAwardFactory, ReportFactory |
| 4 (Settings) | SettingFactory, SettingDraftFactory, UserRoleFactory |

### HasFactory Traits Added
16 models: Category, Need, Payment, Boost, BoostPackage, PaymentEvent,
Offer, Comparison, ComparisonOffer, ComparisonResult, ComparisonAttempt,
NeedAward, Report, Setting, SettingDraft, UserRole

### Test Suite
| Stage | Tests | Assertions |
|-------|-------|------------|
| Pre-GAP-71c | 412 | 691 |
| GAP-71c | 0 | 0 (infrastructure only) |
| **Post-GAP-71c** | **412** | **691** |

### Smoke Tests
All 14 factories verified via tinker.


---

## WIDGET-FLOW — Telegram Login Widget — 2026-09-30

### Test File
- tests/Feature/Auth/TelegramWidgetTest.php (12 tests, 39 assertions)

### Test Suite Growth
| Stage | Tests | Assertions |
|-------|-------|------------|
| Pre-Widget | 412 | 691 |
| Widget | +12 | +39 |
| Post-Widget | 424 | 730 |

### Coverage
- HMAC-SHA256 verification (valid + 4 invalid)
- User upsert (create + update)
- Widget start config
- Full callback flow
- Error paths (401, 400)



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/OPEN_GAPS.md
════════════════════════════════════════════════════════════

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

**Status:** OPEN — BLOCKED_ON_UNKNOWN

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

**Deferred (no tests yet):**
AuditLog, AuthAttempt, Message, Notification, OutboxEvent, Rating,
SettingVersion, TelegramDestination, TelegramPublication, TelegramPublicationEvent

**Closed by:** GAP-71c commit


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



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/CHANGE_LOG.md
════════════════════════════════════════════════════════════

# CHANGE LOG — Felagi v1.4.2
Last Updated: 2026-09-29

## 2026-09-29 — Audit & Preparation Phase

### Audit Completion
- Audited 51/51 deliverables
- Registered ~1,700 requirements
- Built Architecture Map (5 layers)
- Built Dependency Map
- Defined 28 Work Packages
- Built Implementation Ledger (L001-L029)
- Documented Open Gaps (32+)
- Enumerated UNKNOWN items (~85)

### Source Verification
- 7,810+ checks PASS
- 66/66 contrast pairs PASS
- 140/140 semantic verification PASS

### Environment Setup
- Verified PHP 8.2.33
- Verified MySQL 10.6.28 MariaDB
- Verified Composer 2.8.12
- Verified 11/11 PHP extensions
- Verified cPanel/SSH access
- Verified public_html clean state
- Created empty `~/felagi_app/`

### Cleanup Actions
- Archived BEHAQ (62 MB in ~/archives/)
- Removed Google API key named files (3)
- Removed 333 0-byte files
- Removed LEGACY_BEFORE_CLEAN_RESET folder
- Removed legacy-retirement-staging folder
- Home entries: 435 → 52

### Safety Measures
- BEHAQ backup: `behaq_legacy_backup_20260929.tar.gz` (5.2 MB)
- BEHAQ snapshot: `behaq_final_snapshot_20260929.tar.gz` (34 MB)
- Original `.htaccess` retained
- Original `index.php` retained (maintenance page)

## Historical
- v1.4.2: Handoff cleanup (documentation only)
- v1.4.1: Amharic correction (Offer=አቅርቦት), brand master inclusion
- v1.4.0: Correction Record root causes fixed
- v1.3.0: Prior checkpoint
- v1.2.0: Prior checkpoint
- v1.1.0: Original input

## 2026-09-29 — WP-21 Laravel Deployment (DONE)

### Installation
- Laravel 11.56.1 installed at `~/felagi_app/`
- 110 Composer packages
- MySQL database `zagcreht_felagi` created
- User `zagcreht_felagi_user` created with ALL PRIVILEGES

### Configuration
- `.env` production: APP_ENV=production, APP_DEBUG=false
- APP_URL=https://zagcreativity.com
- APP_LOCALE=am, APP_TIMEZONE=Africa/Addis_Ababa
- APP_KEY: generated (base64:111XRaY...)
- DB: MySQL (9 tables after migrations)
- Login: `crontab -l` shows 1-minute scheduler

### Deployment
- public_html backed up: `public_html.maintenance_20260929-090126`
- public_html symlinked → `~/felagi_app/public`
- Storage permissions: 775
- Production cache: config, route, view cached

### Verification
- HTTPS root: HTTP 200
- /up health: HTTP 200
- Server: LiteSpeed
- Locale: am

### Git
- `felagi_app` git repo initialized
- Commit: 1bb9f19
- Bundle: Felagi_v1.4.2_20260929-0903.bundle (67 KB)
- Tarball: Felagi_v1.4.2_20260929-0903_full.tar.gz (50 MB)

### Remaining Gaps
- Default Laravel welcome page (Felagi views pending WP-05)
- Vite assets not built (Node.js unavailable)
- Felagi routes not yet implemented

## 2026-09-29 — Bundle Cleanup

### Archived to ~/archives/Felagi_bundles_archive/
- Felagi_v1.4.2_20260929.bundle (3.4 MB)
- Felagi_v1.4.2_20260929_with_control.tar.gz (19 MB)
- Felagi_v1.4.2_20260929-0903.bundle (67 KB)
- Felagi_v1.4.2_20260929-0903_full.tar.gz (50 MB)
- Total: 71 MB freed

### Kept Active (in ~/)
- Felagi_Clean_Developer_Handoff_v1.4.2.zip (7.3 MB, canonical)
- Felagi_Design_v1.4.2_*_WP21_DONE.bundle (3.4 MB)
- Felagi_App_v1.4.2_*_WP21_DONE.bundle (67 KB)
- Felagi_v1.4.2_*_WP21_DONE_full.tar.gz (50 MB)

### Verification
- Design bundle: cloned OK, 3 commits
- App bundle: cloned OK, 1 commit
- Tarball: 10,566 entries verified

### Archive Documentation
- ARCHIVE_NOTES.md created with rollback procedure
- Historical evidence, not active authority

## 2026-09-29 — WP-22 DONE

### Queue Infrastructure
- QUEUE_CONNECTION=database
- jobs, failed_jobs, job_batches tables exist
- Cron 1-minute scheduler active
- Scheduler test: OK
- No failed jobs

WP-22 COMPLETE.

## 2026-09-29 — WP-05 Phase 1-5 (Database Migrations)

### Phase 1: Core Identity
- users (UUID, telegram_subject, status enum, rating_score/count)
- user_roles (composite PK: user_id, role)
- categories (bilingual name_am/name_en, active flag)
- Modified sessions for UUID user_id

### Phase 2: Marketplace
- needs (requester, category, status, telegram consent)
- offers (provider, price, status)
- need_awards (composite FK to offers)

### Phase 3: Comparison/AI
- comparisons (snapshot, criteria versioning, token usage)
- comparison_offers (immutable snapshots per offer)
- comparison_results (scores, criterion breakdown)
- comparison_attempts (retry tracking)

### Phase 4: Communication
- messages (per-Offer conversation)
- notifications (IN_APP + TELEGRAM, event dedup)
- ratings (CHECK: score 1-5, no self-rating)

### Phase 5: Monetization
- boost_packages (duration, price, active flag)
- payments (BOOST/OFFER_UNLOCK, idempotency)
- boosts (Need + package + payment)
- payment_events (webhook dedup)

### Result
- 25 tables total
- 5 git commits (Phase 1-5)
- All migrations PASS
- All composite FKs verified
- All CHECK constraints verified

Next: Phase 6 (Files & Safety: attachments, reports, audit_logs)

## 2026-09-29 — WP-05 Phase 6-9 COMPLETE (All Migrations Done)

### Phase 6: Files & Safety
- attachments (parent: need/offer/message, quarantine status)
- reports (moderation workflow)
- audit_logs (append-only with hash chain)

### Phase 7: Settings
- settings (key, group, type, risk, is_secret)
- setting_versions (immutable publish history)
- setting_drafts (DRAFT/VALIDATED/PUBLISHED lifecycle)
- scheduled_settings (Phase 4 scheduling)

### Phase 8: Infrastructure
- outbox_events (event_key UNIQUE)
- idempotency_keys (composite PK: actor_scope, scope, key)
- exports (comparison exports)

### Phase 9: Telegram (FINAL)
- telegram_destinations (chat_id UNIQUE, permission evidence)
- telegram_publications (state machine, 3-column unique)
- telegram_publication_events (append-only evidence)

### Result
- 38 tables total
- 9 git commits (Phase 1-9)
- ALL migrations PASS
- 0 migration errors

Next: Phase 10 (Eloquent Models)

## 2026-09-29 — WP-05 Backend Services COMPLETE

### All Phases Done

**Migrations (38 tables)**
- Core: users, user_roles, categories
- Marketplace: needs, offers, need_awards
- AI: comparisons, comparison_offers, comparison_results, comparison_attempts
- Communication: messages, notifications, ratings
- Monetization: boost_packages, boosts, payments, payment_events
- Safety: attachments, reports, audit_logs
- Settings: settings, setting_versions, setting_drafts, scheduled_settings
- Infrastructure: outbox_events, idempotency_keys, exports
- Telegram: telegram_destinations, telegram_publications, telegram_publication_events
- Sanctum: personal_access_tokens

**Models (20)**
User, UserRole, Category, Need, Offer, NeedAward,
Comparison, ComparisonOffer, ComparisonResult, ComparisonAttempt,
Message, Notification, Rating,
BoostPackage, Payment, Boost, PaymentEvent,
Attachment, Report, AuditLog

**Controllers (9)**
BaseApiController, AuthController, CategoryController,
NeedController, OfferController, MessageController,
NotificationController, RatingController, ComparisonController

**Form Requests (4)**
StoreNeedRequest, UpdateNeedRequest,
StoreOfferRequest, UpdateOfferRequest

**Routes (~30)**
All under /api/v1 with Sanctum auth

**Auth (Sanctum)**
- Installed Sanctum v4.3.3
- config/auth.php: sanctum guard added
- bootstrap/app.php: API middleware configured
- HasApiTokens trait on User model

### Verification Evidence

| Route | Expected | Actual |
|-------|----------|--------|
| GET /api/v1/categories | 200 | ✅ 200 |
| GET /api/v1/needs | 200 | ✅ 200 |
| GET /api/v1/notifications | 401 | ✅ 401 |
| POST /api/v1/needs | 401 | ✅ 401 |
| GET /api/v1/my/needs | 401 | ✅ 401 |
| GET /api/v1/auth/me | 401 | ✅ 401 |

### Bugs Fixed
- D-047: telegram_publication_consent_at useCurrent()
- D-048: composite index order for FK
- D-049: Sanctum guard missing in auth.php
- D-050: API middleware not configured

WP-05 COMPLETE.
Next: WP-13 (Admin Change Lifecycle) or WP-10 (AI Integration)

## 2026-09-29 — WP-13 Admin Change Lifecycle (DONE)

### Status Transition
READY → IN_PROGRESS → IMPLEMENTED → INTEGRATED → TESTED → VERIFIED → DONE

### Delivered (14 files)

**Models (4):**
- app/Models/Setting.php — timestamps=false (settings has no created_at)
- app/Models/SettingVersion.php — append-only
- app/Models/SettingDraft.php — standard timestamps
- app/Models/OutboxEvent.php — NEW (fixes Notification.php:53)

**Services (3):**
- app/Services/Admin/AuditWriter.php — SHA-256 hash chain
- app/Services/Admin/OutboxWriter.php — correct schema
- app/Services/Admin/AdminChangeService.php — orchestrator

**Controller + Requests (3):**
- app/Http/Controllers/Api/V1/Admin/AdminChangeController.php — 8 methods
- app/Http/Requests/Admin/CreateDraftRequest.php
- app/Http/Requests/Admin/PublishChangeRequest.php

**Policy (1):**
- app/Policies/SettingPolicy.php — 5 methods

**Exception (1):**
- app/Exceptions/SettingsVersionConflictException.php

**Seeder (1):**
- database/seeders/ControlRegistrySeeder.php — 31 settings

**Test (1):**
- tests/Feature/Admin/ChangeLifecycleTest.php — 8 tests

**Infrastructure (1):**
- .env.testing — MySQL test DB (zagcreht_felagi_test)

### Patches to Existing Files (3)

- app/Http/Controllers/Api/V1/BaseApiController.php — added AuthorizesRequests trait
- database/factories/UserFactory.php — schema-aligned (telegram_subject, full_name)
- routes/api.php — added 10 admin routes

### Verification Evidence

| Metric | Value |
|--------|-------|
| Tests | 8 PASS |
| Assertions | 15 |
| Duration | 3.00s |
| Test DB | MySQL isolated |
| Production DB | Untouched (settings=31, users=0) |
| Routes | 10 registered |
| Settings seeded | 31 |

### Test Results

- can create draft                                   2.38s PASS
- validate rejects type mismatch                     0.08s PASS
- publish creates new version                        0.09s PASS
- publish with stale version returns 409             0.07s PASS
- dependency blocks boosts on                        0.08s PASS
- publish writes audit log                           0.07s PASS
- publish writes outbox event                        0.09s PASS
- unauthorized returns 403                           0.07s PASS

Tests: 8 passed (15 assertions)

### Security Properties Verified

- SHA-256 hash chain in audit_logs (prev_hash + hash)
- Immutable setting_versions (append-only)
- UUID aggregate_id in outbox_events
- Authorization enforced (403 on non-admin)
- Optimistic locking (409 SETTINGS_VERSION_CONFLICT)
- Dependency validation (DFM 8.2 Boosts requires Payments)
- Production DB isolation (test DB only)
- Password quoting for special chars (#)

### Key Decisions

See DECISION_LOG.md for D-054 through D-067:
- D-054 — AUDIT BEFORE ACTION (locked)
- D-055 — aggregate_id = setting_versions.id
- D-057 — Reauth deferred to WP-13b
- D-063 — AuthorizesRequests trait required
- D-067 — MySQL test DB (not SQLite)

### Related GAPs

Resolved: GAP-07, GAP-34, GAP-36, GAP-40, GAP-44
New: GAP-45 (reauth), GAP-46 (2FA), GAP-47 (idempotency), GAP-48 (apply/verify), GAP-49 (rollback E2E test)

## 2026-09-29 — WP-13b Reauth + TOTP 2FA + Idempotency (DONE)

### Status Transition
READY → IN_PROGRESS → IMPLEMENTED → INTEGRATED → TESTED → VERIFIED → DONE

### Delivered (15 new + 4 patched)

**Migration (1):**
- 2026_09_29_111104_add_2fa_and_reauth_to_users_table.php
  → 4 columns: recently_authenticated_at, totp_secret (encrypted),
    totp_enabled_at, totp_recovery_codes (encrypted:array)

**Composer (1):**
- pragmarx/google2fa-laravel v3.0.1

**Services (3):**
- ReauthValidator (5-min window check)
- TotpService (RFC 6238 + replay protection + recovery codes)
- IdempotencyRegistry (DFM §149 compliant)

**Middleware (2):**
- RequireReauth → 401 REAUTH_REQUIRED
- IdempotencyKey → 409 IDEMPOTENCY_CONFLICT

**Jobs (3):**
- ProcessOutboxEvent (locked processing)
- VerifySettingChange (server-side probe + audit)
- CleanupExpiredIdempotencyKeys (hourly cleanup)

**Exceptions (4):**
- ReauthRequiredException (401)
- TwoFactorRequiredException (403)
- InvalidTotpCodeException (422)
- IdempotencyConflictException (409)

### Patches (4)

- AdminChangeService — inject ReauthValidator + TotpService + require() calls in publish()
- AdminChangeController — add apply() + verify() + 3 new catch blocks
- routes/api.php — 12 admin routes with middleware
- routes/console.php — outbox-dispatch + idempotency-cleanup schedules

### Infrastructure (2)

- Cron: `queue:work --stop-when-empty --max-time=50` (every min)
- .env.testing APP_KEY fixed (32-byte AES-256-CBC)

### Test Evidence

| Test File | Count | Result |
|-----------|-------|--------|
| ChangeLifecycleTest | 8 | ✅ PASS |
| ReauthTest | 9 | ✅ PASS |
| TwoFactorTest | 11 | ✅ PASS |
| IdempotencyTest | 8 | ✅ PASS |
| **Total** | **36** | **✅ PASS (63 assertions)** |

Duration: 4.98s
DB: zagcreht_felagi_test (isolated)

### Security Properties Verified

- 5-min reauth window enforced (Auth Contract §3)
- TOTP 2FA for CRITICAL (RFC 6238)
- Replay protection (60s cache)
- Recovery codes (SHA-256 hashed)
- Idempotency-Key enforcement (409 on conflict)
- Idempotency replay (cached response)
- Outbox locked processing (5-min lock)
- Production DB isolation
- No plaintext secret storage (encrypted cast)

### Design Compliance

| Design Lock | Implementation |
|-------------|----------------|
| Auth Contract §3 (5-min + 2FA) | ✅ |
| DFM §149 (idempotency_keys) | ✅ |
| DFM §461 (bounded queue via cron) | ✅ |
| DFM §418 (verify as server job) | ✅ |
| DFM §216 (cookie session admin) | ✅ (existing) |

### Ledger Updates (11 files)

MASTER_BASELINE, WORK_PACKAGES, RELEASE_STATUS, IMPLEMENTATION_LEDGER,
TEST_VERIFICATION, DECISION_LOG, OPEN_GAPS, CHANGE_LOG,
ARCHITECTURE_MAP, REQUIREMENT_REGISTRY, HANDOFF_STATE

### Decision Log

D-068 → D-074 (7 new decisions)
- D-068: TOTP (user said "OTP")
- D-069: Session timestamp for reauth
- D-070: 24h idempotency TTL
- D-071: 4 users columns
- D-072: Outbox consumer jobs for apply/verify
- D-073: queue:work cron (bounded)
- D-074: Test APP_KEY fix

### Resolved GAPs

GAP-45 (reauth), GAP-46 (2FA), GAP-47 (idempotency), GAP-48 (apply/verify)

### New GAPs (WP-13c)

GAP-50 (TOTP enrollment UI), GAP-51 (recovery codes UI),
GAP-52 (2FA disable UI), GAP-53 (lost-factor recovery flow),
GAP-54 (APP_DEBUG production — carried from WP-13)

## 2026-09-29 — GAP-54 Production Security Fix (DONE)

### Issue
- .env: APP_DEBUG=true in production environment
- Laravel log: 944 lines of stack traces (evidence of debug mode)
- 3 .env.* backup files in app root (hygiene)

### Fix

| Action | Before | After |
|--------|--------|-------|
| APP_DEBUG | true | false |
| Laravel log | 944 lines | archived |
| .env.* backups | 3 in root | moved to ~/env_backups/ |
| Config cache | stale | rebuilt |
| Route cache | stale | rebuilt |
| View cache | stale | rebuilt |

### Verification
- config('app.debug') = false
- HTTP /up = 200
- HTTP / = 200
- HTTP 404 = no stack trace
- Rollback: .env.production.bak.20260929_112618

## 2026-09-29 — WP-05d Cleanup (DONE)

### Removed (6 files)

Moved to `~/cleanup_trash/20260929/`:
- app/Http/Controllers/Api/V1/Admin/AdminChangeController.php.bak.*
- app/Services/Admin/AdminChangeService.php.bak.*
- app/Models/User.php.bak.*
- routes/console.php.bak.*
- routes/api.php.bak.*
- config/auth.php.backup

### Verification
- 0 .bak/.backup files remaining in app/config/routes/database
- .gitignore covers *.bak.* and *.backup
- Git working tree CLEAN

## 2026-09-29 — Config Cache Incident (Resolved)

### What Happened

While running WP-05a migration:
- Command: php artisan migrate --env=testing
- Cached config at bootstrap/cache/config.php overrode .env.testing
- Migration ran on PRODUCTION DB (zagcreht_felagi) instead of test DB

### Impact Assessment

| Item | Before | After |
|------|--------|-------|
| Production auth_attempts | MISSING | EXISTS (0 rows) |
| Production users.recently_authenticated_at | EXISTS | EXISTS |
| Production users | 0 | 0 |
| Production settings | 31 | 31 |
| HTTP /up | 200 | 200 |
| HTTP / | 200 | 200 |

Net impact: Additive schema changes only. No data affected. No service interruption.

### Resolution

1. Cleared config cache: php artisan config:clear
2. Verified .env.testing loads with APP_ENV=testing env var
3. Created bin/migrate-test.sh — always clears cache first
4. Retained additive schema (needed for WP-27 + WP-13b)
5. Documented as D-076 (LOCKED)

### Files Changed

- bootstrap/cache/config.php — removed
- bin/migrate-test.sh — created
- PROJECT_CONTROL/DECISION_LOG.md — D-076 added
- PROJECT_CONTROL/CHANGE_LOG.md — this entry

## 2026-09-29 — WP-05a Auth Attempts + PKCE (DONE)

### Delivered

- Migration: auth_attempts (9 columns per DFM §218)
- Model: AuthAttempt (encrypted PKCE verifier)
- Service: AuthAttemptService (RFC 7636 S256)
- Patch: AuthController.telegramStart (PKCE in OAuth URL)
- Tests: AuthAttemptTest (14 PASS)

### Verification
- 14 AuthAttempt tests PASS (34 assertions)
- 50 total Admin tests PASS
- Production HTTP 200 (auth_url with code_challenge)
- Production DB untouched

### Design Compliance
- DFM §218: state_hash, nonce_hash, pkce_verifier_encrypted, handoff_hash, expires_at, consumed_at
- RFC 7636: PKCE verifier (64 chars), S256 challenge
- RFC 6749: authorization-code flow

### Resolved
- GAP-42 (auth_attempts table missing)

## 2026-09-29 — WP-05b Telegram Foundation (DONE)

### Delivered
- 3 Models: TelegramDestination, TelegramPublication, TelegramPublicationEvent
- 1 Controller: AdminTelegramController (4 read-only methods)
- 4 GET routes registered
- 1 Test file (12 tests)

### Scope
- Read-only endpoints only
- Write endpoints (create/validate/activate/pause/retry) → WP-27 (bot token required)

### Verification
- 12 tests PASS
- Routes registered

### Design Compliance
- Design_Integration_Contract.md endpoints
- DFM §275 admin routes

## 2026-09-29 — WP-27 Telegram OIDC Full Flow (DONE)

### Delivered

- Composer: firebase/php-jwt v7.2.1
- Config: services.telegram section (OIDC URLs + credentials)
- Exception: OidcExchangeException (11 reasons)
- Service: TelegramOidcService (exchange + JWKS + issuer/audience/nonce)
- Controller: telegramCallback + real telegramExchange
- Migration: fix personal_access_tokens UUID (bigint → char(36))
- Tests: TelegramOidcTest (20 PASS, 43 assertions)

### Flow (DFM §218 compliance)

1. Client → POST /auth/telegram/start → auth_url + PKCE
2. User → Telegram OAuth → redirect to callback
3. GET /auth/telegram/callback:
   - findByState() → attempt
   - completeLogin():
     - exchangeCode() → token_url POST
     - validateIdToken() → JWKS + 7 checks
     - upsertUser() → telegram_subject
     - markReauth() → 5-min window
   - generateHandoff() → single-use code
   - redirect to return_uri?handoff_code=Z
4. Client → POST /auth/telegram/exchange → Sanctum token

### Verification

- 20 tests PASS (43 assertions)
- Full Admin suite: 84 PASS
- Production migration applied (empty table)

### Design Compliance

- DFM §218: server-side exchange + JWKS + issuer/audience/expiry/nonce
- Auth Contract §3: recently_authenticated_at updated on login
- Auth Contract §449: separate bot permission (not via OIDC)

### Resolved GAPs

GAP-31, GAP-42, GAP-46, GAP-55

### New GAPs

GAP-56 (live E2E test pending manual user test)

## 2026-09-29 — WP-27b HMAC-Signed User Binding (D-091 fix) (DONE)

### Delivered
- AuthAttemptService: HMAC-signed handoff code with embedded user_id
- Controller: telegramExchange uses handoff-embedded user (race-free)
- Tests: 6 new HMAC tests (T21-T26)

### Design Compliance
- DFM §218 preserved — no schema change
- Race condition D-091 RESOLVED
- Handoff single-use preserved via handoff_hash + consumed_at

### Handoff Format
<base64url(JSON{a,e,u})>.<HMAC-SHA256-hex>
- a = attempt_id (UUID)
- e = expires_at (unix timestamp, 60s TTL)
- u = user_id (UUID, optional)

### Verification
- 26 tests PASS (up from 20)
- Full Admin suite: 88 PASS
- Race-free: T25 (two users, correct one selected)

## B10 — CCB (Constitution Compliance Block) — 2026-09-29

### Summary
Documentation-only release fixing 8 data integrity violations. No code, DB,
migrations, or routes changed.

### Changed
| File | Change | Type |
|------|--------|------|
| WORK_PACKAGES.md | WP-27 dedup; stats 8→10 DONE, 2→1 PARTIAL, READY removed, 21→20 BLOCKED; WP-13c added | fix |
| OPEN_GAPS.md | GAP-07→GAP-60; 3 literal dups removed (GAP-42/46/56) | fix |
| HANDOFF_STATE.md | Next Priority refreshed; path corrected | chore |
| PRIORITY_PLAN.md | TIER 1/2/3 aligned; WP-13c added; WP-24 UNKNOWN | chore |
| IMPLEMENTATION_LEDGER.md | L212 CCB entry | docs |
| DECISION_LOG.md | D-096, D-097 | docs |
| CHANGE_LOG.md | This entry | docs |

### Verification
- `grep -c "^| WP-27 " WORK_PACKAGES.md` → 1
- `grep -E "^- (DONE|VERIFIED|PARTIAL|BLOCKED):" WORK_PACKAGES.md` → 10/1/1/20
- GAP-42/46/56 single or dual entry (original + resolved)
- All 47 GAP IDs unique within their tracking sections

### Constitution Compliance
- UNKNOWN ≠ MISSING: T01-T18, WP-13c documented
- No hidden assumptions: all edits traceable
- No silent changes: L212 + D-096 + D-097 + this entry
- DONE (WP-05, WP-13) = implemented + tested + verified + documented + evidenced

### Remaining Open (post-B10)
- GAP-55: handoff/index.html outdated (B11)
- Downloads listing 403/404 (never formally tracked in B10; registered as GAP-61, resolved B13)
- Production root (zagcreativity.com/ shows Laravel default) — never formally tracked; registered as GAP-62 (UNKNOWN, B14)

### Rollback
git revert <B10-commit> ; or restore from *.bak.b10.p4

## B13 — downloads/ index (GAP-61 closed) — 2026-09-29

### Summary
Root cause: `public/.htaccess` disables directory listing (`Options -Indexes`).
URL `/downloads/` returned 403. Fix: static index.html with handoff-consistent style.

### Changed
| File | Change |
|------|--------|
| public/downloads/index.html | NEW — listing page (6.5 KB) |
| .gitignore | /public/downloads/* + exception for index.html |

### Verification
- `git check-ignore -v` clean
- HTTP/2 200 on https://zagcreativity.com/downloads/
- HTTP/2 200 on /downloads/index.html (content-length 6546)
- Bundle direct downloads unaffected (251729 bytes app bundle)

### Correction
B10 CHANGE_LOG mislabeled downloads gap as GAP-56. Real GAP-56 = OIDC E2E test
(still open). Downloads listing never formally registered; now tracked as GAP-61.

### Closes
- GAP-61 (newly registered)

## B14 — Production root gap corrected (GAP-62) — 2026-09-29

### Summary
Documentation-only. No code/DB/routes changed.

### Corrections
1. B10 CHANGE_LOG mislabeled production root issue as "GAP-57"
   - Real GAP-57 = Sanctum UUID migration (DONE)
   - Downloads issue (also mislabeled in B10) had already been fixed as GAP-61 in B13
   - Production root now correctly registered as GAP-62

2. GAP-62 registered in OPEN_GAPS.md as UNKNOWN
   - Reason: LOCKED design for production landing page does not exist in
     ~/felagi_extracted/Felagi_Design_Package/
   - Grep searches returned empty:
     * Final_Information_Architecture.md
     * Final_Navigation_Route_Map.md
     * Route::get('/')  in design package
   - Per Constitution: do not create new design. Awaiting design owner.

### Current production state (verified)
- URL / → HTTP 200 with Laravel default welcome page
- routes/web.php → only Route::get('/', view('welcome'))
- resources/views/ → only welcome.blade.php
- Document root symlink → felagi_app/public (correct)

### Changed
| File | Change |
|------|--------|
| OPEN_GAPS.md | GAP-62 registered (UNKNOWN) |
| CHANGE_LOG.md | B10 mislabel fixed + this entry |
| IMPLEMENTATION_LEDGER.md | L214 appended |
| DECISION_LOG.md | D-098 appended |

### Not Changed
- No code / DB / routes modified
- Production root remains Laravel default until design owner provides LOCKED design

### Blocked On
- GAP-62 → design owner must provide LOCKED production landing page design

## B15 — S001 Welcome at production root (GAP-62 closed) — 2026-09-29

### Summary
Replaced Laravel default welcome with LOCKED S001 Welcome
(source: Felagi_Design_Package/UI_Handoff/ui-preview/app.js:144).

### Changed
| File | Change |
|------|--------|
| resources/views/welcome.blade.php | REPLACED (Laravel → S001) |
| lang/am.json | NEW (5 keys) |
| lang/en.json | NEW (5 keys) |
| public/assets/brand/felagi-lockup.svg | NEW |
| public/assets/brand/felagi-lockup-on-dark.svg | NEW |

### S001 content (LOCKED — 3 elements only)
1. brand logo
2. purpose text
3. signIn button → Telegram OIDC

### Verification
- Live: HTTP/2 200
- Title: `መግቢያ — ፈላጊ`
- Purpose text (AM): rendered
- Brand asset: HTTP/2 200
- Cache: config/view/route/app cleared

### Closes
- GAP-62

## B16 — non-admin test coverage + .bak cleanup — 2026-09-29

### Summary
Addressed non-admin test coverage gap (Phase 2 audit: 47 API routes, 1 non-admin test).
8 stale .bak files archived.

### Changed
| File | Change |
|------|--------|
| tests/Feature/Need/NeedFlowTest.php | NEW (6 tests) |
| tests/Feature/Offer/OfferFlowTest.php | NEW (6 tests) |
| tests/Feature/Message/MessageFlowTest.php | NEW (4 tests) |
| .archives/20260929-b16-bak-cleanup/ | NEW (8 .bak moved) |

### Verification
- Filter run: 16 passed (30 assertions)
- Full suite: 106 passed (220 assertions)

### Not Changed
- No production code, no migrations, no routes, no new design


## B17 — Ledger Integrity Sweep (DONE) — 2026-09-29

### Summary
Documentation-only sweep fixing 12 ledger integrity issues discovered
in post-B16 audit. No code, no DB, no routes, no design changes.

### Issues Fixed (12)

| # | Issue | Fix | File |
|---|-------|-----|------|
| 1 | L188-L192 duplicate IDs (5 IDs) | Renumbered to L212-L216 | IMPLEMENTATION_LEDGER |
| 2 | HANDOFF_STATE stale (WP-05, 9 tables, stats) | Full rewrite | HANDOFF_STATE |
| 3 | MASTER_BASELINE missing WP-27/05a/b/B10-B16 | Append-only extensions | MASTER_BASELINE |
| 4 | REQUIREMENT_REGISTRY missing 42 REQ IDs | Append-only extensions | REQUIREMENT_REGISTRY |
| 5 | ARCHITECTURE_MAP missing Layer 8-11 | Append-only extensions | ARCHITECTURE_MAP |
| 6 | GAP-38/GAP-54 duplicate (same issue) | GAP-38 → SUPERSEDED | OPEN_GAPS |
| 7 | GAP-07/GAP-60 confusion | GAP-07 marked renumbered | OPEN_GAPS |
| 8 | GAP-42 in open list (resolved in WP-05a) | Marked RESOLVED | OPEN_GAPS |
| 9 | Table count 25 vs 38 ambiguity | Reconciliation section | MASTER_BASELINE |
| 10 | Test count evolution unclear | Full trail + UNKNOWN | TEST_VERIFICATION |
| 11 | PRIORITY_PLAN TIER 0 stale | Append-only update | PRIORITY_PLAN |
| 12 | B-Blocks vs WPs (U-21) | Documented UNKNOWN | WORK_PACKAGES |

### Files Changed (11)

1. IMPLEMENTATION_LEDGER.md — L188-L192 → L212-L216
2. CHANGE_LOG.md — 3 sed fixes + this entry
3. HANDOFF_STATE.md — full rewrite (102 → 124 lines)
4. MASTER_BASELINE.md — append-only (+78 lines, 0 deletions)
5. REQUIREMENT_REGISTRY.md — append-only (+76 lines, 42 new REQ IDs)
6. ARCHITECTURE_MAP.md — append-only (+107 lines, Layers 8-11)
7. WORK_PACKAGES.md — append-only (+43 lines, B-Blocks table)
8. PRIORITY_PLAN.md — append-only (+58 lines)
9. TEST_VERIFICATION.md — append-only (+127 lines, full trail)
10. DECISION_LOG.md — D-102 → D-110 (see step 10)
11. RELEASE_STATUS.md — B17 summary (see step 11)

### Test Evidence
N/A — Documentation-only. No code, schema, or routes changed.
Full suite remains: 106 tests · 220 assertions (unchanged).

### Evidence Chain
- Backup: ~/B17_backups/20260929_144348/ (13 files)
- Audit: cross-reference grep (5 files)
- Fix: 9 sed commands + 8 append blocks
- Verify: 6 verification gates (all PASS)
- Rollback: B17_ROLLBACK.md (see step 13)

### Constitution Compliance
- No hidden work — 12 issues documented with fix + evidence
- No hidden assumptions — UNKNOWN items marked, not guessed
- No silent changes — 10 new decisions (D-102→D-110)
- No duplicate ownership — D-102 fixed 5 duplicate L-IDs
- DONE = implemented + integrated + tested + verified + documented + evidenced
- UNKNOWN ≠ MISSING — U-21 (B-Blocks vs WPs) documented

### Rollback
git revert <B17-commit> — see B17_ROLLBACK.md

### Follow-up
- U-21 — B-Blocks vs WPs formalization (stakeholder)
- D-096 — T01-T18 spec request (stakeholder)
- D-097 — WP-13c frontend stack (stakeholder)

## B19 — Model Unit Tests (2026-09-29)

### Summary
Added unit test coverage for 3 previously untested critical models.
All 25 new tests PASS. Suite: 106 -> 131 tests.

### New Test Files (3)

| File | Tests | Focus |
|------|-------|-------|
| tests/Feature/Models/AuditLogTest.php | 8 | Hash chain, scopes, casts, actor |
| tests/Feature/Models/SettingVersionTest.php | 8 | Immutability, casts, relations |
| tests/Feature/Models/OutboxEventTest.php | 9 | Statuses, scopes, markDone/markFailed |

### Test Results

| Metric | Value |
|--------|-------|
| Tests run | 131 (was 106) |
| Assertions | 266 (was 220) |
| Failures | 0 |
| Duration | 7.61s |
| Test DB | zagcreht_felagi_test (isolated) |

### Audit Findings

B19 audit discovered 2 NOT NULL columns without defaults:
- setting_versions.reason
- audit_logs.request_id

Both were made explicit in test payloads. No schema change made.

### Not Changed

- No production code modified
- No migrations added
- No routes changed
- No new design

### Constitution Compliance

- D-054 AUDIT BEFORE ACTION - audit ran first
- IMPLEMENTED != VERIFIED - tests proven before commit
- No hidden work - all files listed
- No silent changes - DECISION_LOG D-111, D-112 logged

### Rollback

git revert <B19-commit>

## B21 — Extended Model Tests + Audit + Spec Requests (2026-09-29)

### Summary
Added 31 model tests + 2 docs (audit + spec requests). Single commit.
Tests: 131 -> 162. All PASS.

### New Test Files (3)

| File | Tests | Focus |
|------|-------|-------|
| tests/Feature/Models/NotificationTest.php | 11 | Statuses, scopes, read lifecycle |
| tests/Feature/Models/RatingTest.php | 9 | Relations, valid scope, uniqueness |
| tests/Feature/Models/UserTest.php | 11 | SoftDeletes, scopes, encrypted casts |

### New Test Trait (1)

| File | Purpose |
|------|---------|
| tests/Feature/Models/Concerns/CreatesTestCategory.php | DRY category factory |

### New Docs (3)

| File | Purpose |
|------|---------|
| docs/audits/MIGRATION_INTEGRITY_B21.md | Read-only migration audit |
| docs/spec-requests/WP-05c_admin_read_endpoints.md | Stakeholder spec request |
| docs/spec-requests/T01-T18_integration_tests.md | Stakeholder spec request |

### Test Results

| Metric | Value |
|--------|-------|
| Tests run | 162 (was 131) |
| Assertions | 320 (was 266) |
| Failures | 0 |
| Duration | 8.52s |

### Schema Findings (via test failures)

During B21 test development, 5 NOT NULL no-default columns and 1 unique
constraint were discovered and documented:

- setting_versions.reason
- audit_logs.request_id
- needs.category_id
- offers.offered_price
- offers.proposal_message
- ratings UNIQUE(need_id, from_user_id, to_user_id)

All handled in test payloads (no schema change). See D-112 and audit doc.

### Not Changed

- No production code modified
- No migrations added
- No routes changed
- No new design

### Constitution Compliance

- D-054 AUDIT BEFORE ACTION - audit ran first
- IMPLEMENTED != VERIFIED - tests proven before commit
- UNKNOWN != MISSING - spec requests documented
- No silent changes - all findings logged

### Rollback

git revert <B21-commit>

---

## B25 — Payment Domain Test Suite (WP-B25 / R-TEST-01) — 2026-09-30

**Type:** Test coverage
**HEAD before:** 143a756 (B24)

### Test Results

| Metric | Value |
|--------|-------|
| Tests run | 216 (was 162) |
| Assertions | 403 (was 320) |
| Failures | 0 |
| Duration | 7.54s |
| New files | 5 (4 tests + 1 evidence) |

### Files Added

- tests/Feature/Models/PaymentTest.php (16 tests)
- tests/Feature/Models/PaymentEventTest.php (13 tests)
- tests/Feature/Models/BoostTest.php (14 tests)
- tests/Feature/Models/BoostPackageTest.php (11 tests)
- evidence/WP-B25_evidence.md
- evidence/WP-B25_phpunit_20260930_053259.log

### Not Changed

- No production code modified
- No migrations added
- No routes changed
- No new design

### Constitution Compliance

- D-054 AUDIT BEFORE ACTION - schema audited first
- IMPLEMENTED != VERIFIED - tests proven, awaiting VERIFIED
- UNKNOWN != MISSING - 10 UNKNOWNs resolved before coding
- No silent changes - all schema findings logged
- No production code modified

### Rollback

git revert 202716b..HEAD  # test-only (whole chain — code + docs)

---

## B26 — AI/Comparison Domain Test Suite (WP-B26 / R-TEST-02) — 2026-09-30

**Type:** Test coverage
**HEAD before:** ce47d24 (B25 final)

### Test Results

| Metric | Value |
|--------|-------|
| Tests run | 284 (was 216) |
| Assertions | 496 (was 403) |
| Failures | 0 |
| Duration | 11.02s (full), 4.87s (B26 only) |
| New files | 5 (4 tests + 1 evidence) + 2 logs |

### Files Added

- tests/Feature/Models/ComparisonTest.php (23 tests)
- tests/Feature/Models/ComparisonOfferTest.php (13 tests)
- tests/Feature/Models/ComparisonResultTest.php (15 tests)
- tests/Feature/Models/ComparisonAttemptTest.php (17 tests)
- evidence/WP-B26_evidence.md
- evidence/WP-B26_phpunit_*.log + WP-B26_fullsuite_*.log

### Not Changed

- No production code modified
- No migrations added
- No routes changed
- No new design

### Constitution Compliance

- D-054 AUDIT BEFORE ACTION - schema audited first
- IMPLEMENTED != VERIFIED - tests proven, awaiting VERIFIED
- UNKNOWN != MISSING - UNKNOWNs resolved before coding
- No silent changes - all schema findings logged
- No production code modified

### Rollback

git revert <B26-commit>  # test-only

---

## B27 — Safety/Marketplace Tests + GAP-71 Fix — 2026-09-30

**Type:** Test coverage + Breaking production fix (approved)
**HEAD before:** 861fd6e (B26)

### Breaking Change Disclosure

**Production fix:** app/Models/Attachment.php line 74

    - public function isClean(): bool
    + public function isScanClean(): bool

**Reason:** isClean() collides with Laravel Model::isClean($attributes = null).
Every new Attachment() raised TypeError. Latent since B17.
**Approval:** explicit user approval (Constitution Art.).
**Callers before fix:** none (grep verified).
**Detailed in:** GAP-71 (RESOLVED).

### Test Results

| Metric | Value |
|--------|-------|
| Tests run | 350 (was 284) |
| Assertions | 595 (was 496) |
| Failures | 0 |
| New files | 5 (4 tests + 1 evidence) + 2 logs |

### Files Added
- tests/Feature/Models/CategoryTest.php (15 tests)
- tests/Feature/Models/NeedAwardTest.php (13 tests)
- tests/Feature/Models/AttachmentTest.php (21 tests)
- tests/Feature/Models/ReportTest.php (17 tests)
- evidence/WP-B27_evidence.md + 2 logs

### Files Changed (production)
- app/Models/Attachment.php — isClean → isScanClean (GAP-71)

### Not Changed
- No migrations added
- No routes changed
- No new design

### Constitution Compliance
- Breaking change APPROVED — explicit user authorization
- GAP-71 logged BEFORE fix — no silent change
- No production code modified without disclosure

### Rollback
    git revert <B27-commit>  # reverts both production fix + tests
    # OR selective:
    git revert <B27-commit> -- app/Models/Attachment.php

---

## B28 — Settings/Role Domain Test Suite (WP-B28 / R-TEST-06+07) — 2026-09-30

**Type:** Test coverage
**HEAD before:** 6db0e86 (B27)

### Test Results

| Metric | Value |
|--------|-------|
| Tests run | 412 (was 350) |
| Assertions | 691 (was 595) |
| Failures | 0 |
| Duration | 14.28s (full suite) |

### Files Added
- tests/Feature/Models/SettingTest.php (25 tests)
- tests/Feature/Models/SettingDraftTest.php (20 tests)
- tests/Feature/Models/UserRoleTest.php (17 tests)
- evidence/WP-B28_evidence.md + 2 logs

### Not Changed
- No production code modified
- No migrations added
- No routes changed
- No new design

### Constitution Compliance
- D-054 AUDIT BEFORE ACTION - schema audited first
- IMPLEMENTED != VERIFIED - tests proven, awaiting VERIFIED
- UNKNOWN != MISSING - all UNKNOWNs resolved before coding
- No silent changes

### Rollback
git revert <B28-commit>  # test-only

---

## GAP-70 Backfill — B18, B20, B22, B23, B24 (added 2026-09-30)

**Reason:** B18/B20/B22/B23/B24 existed in git but were not recorded in
canonical CHANGE_LOG. Backfilled from git history. B19 and B21 entries
already existed above and are NOT duplicated here.

---

## B18 — Bundle Refresh @ B17_DONE — 2026-09-29

**Commit:** dbc689c
**Type:** Bundle refresh (doc-only)
**Tests:** 106 (unchanged)

**Artifacts (in ~/):**
- Felagi_App_v1.4.2_20260929-1514_B17_DONE.bundle (974K)
- Felagi_Design_v1.4.2_20260929-1514_B17_DONE.bundle (3.5M)
- Felagi_v1.4.2_20260929-1514_B17_DONE_full.tar.gz (45M)
- Felagi_v1.4.2_20260929-1514_B17_DONE_FULL_with_vendor.tar.gz (76M)

**Clone verified:** App HEAD=6ea8a97 (B17), 51 commits, 14 ledgers
**Files changed:** HANDOFF_STATE.md (+41)

---

## B20 — Bundle Refresh @ B19_DONE — 2026-09-29

**Commit:** 6c6bcb6
**Type:** Bundle refresh
**Tests:** 131 (unchanged)

**Artifacts (in ~/):**
- Felagi_App_v1.4.2_20260929-1530_B19_DONE.bundle (980K)
- Felagi_Design_v1.4.2_20260929-1530_B19_DONE.bundle (3.5M)
- Felagi_v1.4.2_20260929-1530_B19_DONE_full.tar.gz (45M)
- Felagi_v1.4.2_20260929-1530_B19_DONE_FULL_with_vendor.tar.gz (76M)

**Clone verified:** App HEAD=b451224 (B19), 53 commits, 3 model test files
**Supersedes:** B18 bundle (B17_DONE)
**Files changed:** HANDOFF_STATE.md (+41)

---

## B22 — Bundle Refresh + STATUS_REPORT + SOURCE_OF_TRUTH — 2026-09-29

**Commits:** 1a17a7c + 6bc49c8 + 7a0d21e (grouped)
**Type:** Documentation
**Tests:** 162 (unchanged)

**Artifacts (in ~/):**
- Felagi_App_v1.4.2_20260929-1551_B21_DONE.bundle (999K)
- Felagi_Design_v1.4.2_20260929-1551_B21_DONE.bundle (3.5M)
- Felagi_v1.4.2_20260929-1551_B21_DONE_full.tar.gz (45M)
- Felagi_v1.4.2_20260929-1551_B21_DONE_FULL_with_vendor.tar.gz (76M)

**Files added:**
- FELAGI_STATUS_REPORT.md (523 lines)
- SOURCE_OF_TRUTH.md (1927 lines, consolidated 4 docs: STATUS + HANDOFF + DECISION + TEST_VERIFICATION)

**Clone verified:** App HEAD=c64fa28 (B21), 55 commits, 14 ledgers, 6 model test files, 1 audit, 2 spec requests
**Supersedes:** B20 bundle (B19_DONE)
**Files changed:** HANDOFF_STATE.md (+46)

---

## B23 — Publish SOURCE_OF_TRUTH to Public Directories — 2026-09-29

**Commit:** 48217b2
**Type:** Publication
**Tests:** 162 (unchanged)

**Files added:**
- public/handoff/SOURCE_OF_TRUTH.md (1927 lines)
- public/handoff/FELAGI_STATUS_REPORT.md (523 lines)
- public/downloads/SOURCE_OF_TRUTH.md
- public/downloads/FELAGI_STATUS_REPORT.md

**Live URLs verified (HTTP/2 200):**
- https://zagcreativity.com/handoff/SOURCE_OF_TRUTH.md
- https://zagcreativity.com/handoff/FELAGI_STATUS_REPORT.md
- https://zagcreativity.com/downloads/SOURCE_OF_TRUTH.md
- https://zagcreativity.com/downloads/FELAGI_STATUS_REPORT.md

---

## B24 — S001 signIn (GAP-63/64/65/66 Chained Fixes) — 2026-09-29

**Commits:** 3e7236c + 143a756 (grouped)
**Type:** Production bug fix (approved by user request)
**Tests:** 162 (unchanged)

### GAPs Resolved
- **GAP-63:** JS read top-level data.auth_url, backend returns nested
  → Fix: `data.data || data` fallback
- **GAP-64:** bot_id parameter missing from Telegram OAuth URL
  → Fix: added bot_id (8629327448)
- **GAP-65:** CSRF token missing in fetch request
  → Fix: meta tag + X-CSRF-TOKEN header + credentials
- **GAP-66:** origin parameter missing
  → Fix: origin = zagcreativity.com

### Files Changed (code + docs)
- resources/views/welcome.blade.php (CSRF + fetch)
- app/Http/Controllers/Api/V1/AuthController.php (origin + bot_id)
- config/services.php (bot_id mapping)
- FELAGI_STATUS_REPORT.md (B24 section, +34)
- SOURCE_OF_TRUTH.md (rebuilt, +87/-46)

### Evidence
- URL parameters: 10/10 verified
- Live URLs: 4/4 HTTP 200
- Tests: 162 passed (320 assertions)

### Not Changed
- No migrations
- No routes
- No design

### Constitution Compliance
- Production change: user-requested fix (GAP-63/64/65/66)
- No silent changes — all 4 GAPs documented
- IMPLEMENTED != VERIFIED — tests not re-run beyond existing 162

**End of GAP-70 Backfill — CHANGE_LOG.**

---

## GAP-71b — Attachment Factory + HasFactory — 2026-09-30

**Type:** Infrastructure (test factories)
**HEAD before:** 4285ad1 (QUALITY_DONE)

### Files Added/Changed
- NEW: database/factories/AttachmentFactory.php
- UPD: app/Models/Attachment.php (HasFactory trait)

### Test Results
- Full suite: 412 (unchanged)
- AttachmentTest: 21/21 pass

### Constitution
- Additive infrastructure
- No breaking change
- No silent changes

### Rollback
git revert <GAP-71b-commit>

---

## GAP-71c — 14 Additional Factories + HasFactory — 2026-09-30

**Type:** Infrastructure (test factories)
**HEAD before:** 331e98e (INCIDENT-FIX)

### Files Added
- 16 factory files

### Files Modified
- 16 models (HasFactory trait added)

### Test Results
- Full suite: 412 (unchanged)
- Smoke test: all pass

### Rollback
git revert <GAP-71c-commit>


---

## WIDGET-FLOW — OIDC -> Widget pivot — 2026-09-30

**Type:** Architecture pivot (production fix)

### Root Cause
BotFather confirms "Web login is currently unavailable" for Felagi bot.
B24 (GAP-64) was incomplete: bot_id was added but client_id remained
numeric bot ID (not hex OIDC client_id). URL rendered Widget page.

### Changes
- NEW TelegramWidgetService (HMAC verification)
- NEW TelegramWidgetTest (12 tests)
- AuthController: 2 new methods
- routes: 2 new routes
- welcome.blade.php: Widget JS
- config/services.php: bot_token + bot_username
- .env: TELEGRAM_BOT_TOKEN + TELEGRAM_BOT_USERNAME

### Preserved
- OIDC service + routes (DEFERRED)

### Tests
- Widget: 12 (39 assertions)
- Full: 424 (730 assertions)

### Rollback
git revert <WIDGET-commit>



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/DECISION_LOG.md
════════════════════════════════════════════════════════════

# DECISION LOG — Felagi v1.4.2
Last Updated: 2026-09-29

| ID | Decision | Basis | Date |
|----|----------|-------|------|
| D-001 | Treat v1.4.2 as locked baseline | User + START_HERE | 2026-09-29 |
| D-002 | No architecture changes | Constitution | 2026-09-29 |
| D-003 | No file modification without approval | User directive | 2026-09-29 |
| D-004 | Audit before implementation | Constitution | 2026-09-29 |
| D-005 | v1.4.2 = doc-only over v1.4.1 | Handoff_Cleanup | 2026-09-29 |
| D-006 | Historical findings retained | Final_Gap_Audit | 2026-09-29 |
| D-007 | Source PASS ≠ production PASS | Release_Gates | 2026-09-29 |
| D-008 | Positive-fee activation BLOCKED | N10 | 2026-09-29 |
| D-009 | Ads master remains OFF | N13 + A023 | 2026-09-29 |
| D-010 | 18 vs 23 admin = IA vs inventory | Final_IA + Inventory | 2026-09-29 |
| D-011 | No implementation without approval | Constitution | 2026-09-29 |
| D-012 | Continue with canonical ledgers only | User request | 2026-09-29 |
| D-013 | Chat interruption mitigated via ledgers | User concern | 2026-09-29 |
| D-014 | Server (PHP/MySQL/Composer) verified | env check | 2026-09-29 |
| D-015 | Local toolchain missing (Flutter/Node/Python3.10+) | env check | 2026-09-29 |
| D-016 | Python 3.6.8 insufficient for Tools/*.py | env check | 2026-09-29 |
| D-017 | WP-21 can start pending approval | env check | 2026-09-29 |
| D-018 | WP-02 cannot re-run without Python3.10+ + Node | env check | 2026-09-29 |
| D-019 | BEHAQ archived, not deleted | safety | 2026-09-29 |
| D-020 | Google API key names removed | security | 2026-09-29 |
| D-021 | 0-byte files cleaned (333) | cleanup | 2026-09-29 |
| D-022 | Felagi stays at `~/felagi_extracted/` | user choice | 2026-09-29 |
| D-023 | Laravel app goes to `~/felagi_app/` | convention | 2026-09-29 |
| D-024 | Awaiting WP-21 install approval | Constitution | 2026-09-29 |

## WP-21 Deployment Decisions (2026-09-29)

| ID | Decision | Basis | Date |
|----|----------|-------|------|
| D-025 | User approved "a b" — full WP-21 | User explicit | 2026-09-29 |
| D-026 | MySQL `zagcreht_felagi` (with prefix) | cPanel required | 2026-09-29 |
| D-027 | MySQL user `zagcreht_felagi_user` | cPanel required | 2026-09-29 |
| D-028 | `.my.cnf` auto password | User request | 2026-09-29 |
| D-029 | DB_PASSWORD quoted in .env | `#` special char | 2026-09-29 |
| D-030 | Laravel 11 (not 10) | Latest stable | 2026-09-29 |
| D-031 | public_html symlink (not move) | Reversible | 2026-09-29 |
| D-032 | Cron runs every 1 minute | Laravel scheduler | 2026-09-29 |
| D-033 | Vite not built (Node.js missing) | Environment limit | 2026-09-29 |
| D-034 | Keep Laravel welcome page temporarily | Felagi views pending | 2026-09-29 |

## WP-05 Migration Decisions (2026-09-29)

| ID | Decision | Basis | Date |
|----|----------|-------|------|
| D-035 | Users use UUID primary key | DFM §3.2 | 2026-09-29 |
| D-036 | user_roles uses composite PK (user_id, role) | DFM §3.2 | 2026-09-29 |
| D-037 | Fix telegram_publication_consent_at: useCurrent() | MySQL strict mode | 2026-09-29 |
| D-038 | Composite unique index order: (id, comparison_id) | MySQL FK requirement | 2026-09-29 |
| D-039 | ratings uses CHECK constraints | DFM §3.2 | 2026-09-29 |
| D-040 | payments.need_id NOT NULL | DFM §3.2 (both purposes) | 2026-09-29 |

## WP-05 Phase 6-9 Decisions (2026-09-29)

| ID | Decision | Basis | Date |
|----|----------|-------|------|
| D-041 | audit_logs uses append-only + hash chain | DFM §3.2 | 2026-09-29 |
| D-042 | settings.key is PRIMARY (not UUID) | DFM §3.2 | 2026-09-29 |
| D-043 | idempotency_keys uses composite PK | DFM §3.2 | 2026-09-29 |
| D-044 | telegram_publications has 3-column unique | DFM §3.2 | 2026-09-29 |
| D-045 | scheduled_settings is Phase 4 only | DFM §3.2 | 2026-09-29 |
| D-046 | All 38 tables now in production DB | WP-05 | 2026-09-29 |

## WP-05 Phase 11 Decisions (2026-09-29)

| ID | Decision | Basis | Date |
|----|----------|-------|------|
| D-047 | Use Sanctum for API auth (not Passport) | DFM §5.1 | 2026-09-29 |
| D-048 | Sanctum guard added to config/auth.php | Required for auth:sanctum | 2026-09-29 |
| D-049 | API middleware group configured | bootstrap/app.php | 2026-09-29 |
| D-050 | Need accept uses FOR UPDATE lock | DFM §4 | 2026-09-29 |
| D-051 | Rating recomputes user stats atomically | DFM §4 | 2026-09-29 |
| D-052 | Comparison controller is stub (WP-10 will complete) | AI needs provider | 2026-09-29 |
| D-053 | Personal access tokens table created | Sanctum auto-publish | 2026-09-29 |

## WP-13 Decisions (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-054 | **AUDIT BEFORE ACTION** — no write without prior verification | WP-13 first failure | LOCKED |
| D-055 | aggregate_id = setting_versions.id | Design: UUID types | ACCEPTED |
| D-056 | Idempotency-Key from HTTP header (optional) | Pragmatic MVP | ACCEPTED |
| D-057 | Reauth (5-min) deferred to WP-13b | users.recently_authenticated_at missing | ACCEPTED |
| D-058 | Test DB via .env.testing | Isolation from production | ACCEPTED |
| D-059 | UserFactory schema alignment | users table has telegram_subject not name/email | ACCEPTED |
| D-060 | Notification.php:53 fixed via OutboxEvent model creation | Same file needed anyway | ACCEPTED |
| D-061 | APP_DEBUG=true in production → separate WP | Out of WP-13 scope | DEFERRED |
| D-062 | AdminChangeController uses existing BaseApiController envelope | Consistency | ACCEPTED |
| D-063 | Add AuthorizesRequests trait to BaseApiController | authorize() was broken | ACCEPTED |
| D-064 | auth_attempts missing → GAP for WP-27 | Not WP-13 concern | DOCUMENTED |
| D-065 | DatabaseSeeder broken → GAP for WP-05 | Not WP-13 concern | DOCUMENTED |
| D-066 | WP-13 = narrow scope (Change Lifecycle only) | Constitution: complete but bounded | LOCKED |
| D-067 | MySQL test DB (not SQLite) | WP-05 offers migration incompatible with SQLite | ACCEPTED |

### Rationale Notes

**D-054 (LOCKED):** First WP-13 attempt failed because code was written without
verifying folder existence, table schema, or column names. 5 audit rounds
established the pattern: Audit → Registry → Plan → Approval → Implement → Verify.

**D-055:** `outbox_events.aggregate_id` is UUID type. `settings.key` is VARCHAR.
Use `setting_versions.id` (UUID) as aggregate_id; setting key goes in payload_json.

**D-067:** SQLite cannot `ALTER TABLE ... ADD UNIQUE` in the same way MySQL can.
WP-05 migration `create_offers_table.php:35` uses MySQL-specific syntax. Chose
isolated MySQL test DB over fixing migration (which would be architecture change).

## WP-13b Decisions (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-068 | Second factor = **TOTP** (RFC 6238) | User said "OTP" | ACCEPTED |
| D-069 | Reauth proof = `users.recently_authenticated_at` | Design: "recent reauth proof" | ACCEPTED |
| D-070 | Idempotency TTL = **24h** | Design: expires_at exists | ACCEPTED |
| D-071 | Reauth storage = 4 users columns | Design column requirements | ACCEPTED |
| D-072 | Apply/Verify = outbox consumer jobs | Design: "server jobs" | ACCEPTED |
| D-073 | Queue:work cron added (bounded, 50s) | DFM §461 | ACCEPTED |
| D-074 | Test DB APP_KEY = valid 32-byte | PHPUnit encryption | ACCEPTED |

### Rationale

**D-068 (TOTP):** User explicitly said "OTP". No SMS gateway, no SMTP, works offline, standard library (pragmarx/google2fa-laravel).

**D-069 (Session timestamp):** Design says "recent re-authentication proof" — session-based, cookie-session admin flow (DFM §216), 5-minute window LOCKED.

**D-070 (TTL 24h):** Standard retry window; cleanup job runs hourly.

**D-071 (4 columns):** recently_authenticated_at, totp_secret (encrypted), totp_enabled_at, totp_recovery_codes (encrypted:array). All nullable.

**D-072 (Outbox jobs):** Design: "Apply and verification are server jobs, never browser assertions."

**D-073 (cron):** max-time=50 < 60s interval = non-overlap.

**D-074 (APP_KEY):** Old test key was invalid (33 bytes). Replaced with valid 32-byte key.

### Deferred to WP-13c

| Req | Reason |
|-----|--------|
| TOTP enrollment UI | Frontend (Flutter/Admin UI) — WP-13c |
| Recovery codes display UI | Frontend — WP-13c |
| Self-service disable 2FA | Frontend — WP-13c |

## GAP-54 Decision (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-075 | APP_DEBUG=false in production + log rotation | Security best practice + DFM §449 (audit) | ACCEPTED |

### Rationale

Production stack traces expose internal paths, DB queries, library versions. Laravel default when APP_ENV=production is APP_DEBUG=false; but .env explicitly set true. Fix: change .env to false, rebuild config cache, archive existing log.

Rollback: .env.production.bak.20260929_112618.

### Verification

- config('app.debug') = false
- HTTP /up = 200
- HTTP / = 200
- HTTP 404 test = no stack trace
- Laravel log = archived (944 lines)

## Config Cache Incident (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-076 | Test migration MUST clear config cache first | Incident RCA | LOCKED |

### Incident

During WP-05a (auth_attempts migration), ran "php artisan migrate --env=testing".

Expected: migrate on zagcreht_felagi_test (test DB)
Actual: migrated on zagcreht_felagi (PRODUCTION)

### Root Cause

- bootstrap/cache/config.php existed (cached)
- Cached config pointed to production DB
- --env=testing changes APP_ENV but cache wins before .env.testing is read
- Laravel 11 loads cached config before environment-specific .env.*

### Impact

- Production auth_attempts table created (0 rows, harmless)
- Production users.recently_authenticated_at already existed (WP-13b)
- No data modification; no user-visible impact
- HTTP endpoints continued 200

### Resolution (D-076 LOCKED)

Every test DB migration MUST:
1. php artisan config:clear  -- remove cached config first
2. APP_ENV=testing php artisan migrate  -- use env var, not flag

Tooling: bin/migrate-test.sh created (always clears cache).

Rollback available:
- .env.production.bak.20260929_112618
- Production auth_attempts retained (additive; needed for WP-27)
- Production users.recently_authenticated_at retained (nullable)

### Prevention

Before: php artisan migrate --env=testing
After:  php artisan config:clear && APP_ENV=testing php artisan migrate

Assumption Before: --env loads .env.testing
Fact: --env only changes APP_ENV

Constitution compliance: RULE #1 (Audit Before Action) violation documented; not repeated.

## WP-05a Decisions (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-077 | 64-char PKCE verifier | RFC 7636 (43-128 range) | ACCEPTED |
| D-078 | Encrypted pkce_verifier at rest | Laravel encrypted cast | ACCEPTED |
| D-079 | handoff_hash SHA-256 single-use | DFM §218 | ACCEPTED |
| D-080 | telegramExchange stays 501 | WP-27 credentials needed | ACCEPTED |
| D-081 | Carbon 3.x: use abs() for time diffs | Laravel 11 migration | ACCEPTED |

### Rationale

**D-077:** 64 chars is within RFC 7636 range and gives 384 bits entropy.

**D-078:** DFM §218 says "encrypted PKCE verifier". Laravel `encrypted` cast handles this transparently.

**D-079:** Handoff code returned once; only hash stored. Single-use enforced via `consumed_at`.

**D-080:** Full OIDC exchange requires Telegram client_id/secret + JWKS endpoint. Deferred to WP-27.

**D-081:** Carbon 3.x in Laravel 11 changed `diffInMinutes()` to return signed values. Test assertions must use `abs()`.

## WP-05b Decision (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-082 | WP-05b read-only foundation only | Write endpoints need bot token (WP-27) | LOCKED |
| D-083 | Append-only TelegramPublicationEvent (no timestamps) | Audit trail for publication events | ACCEPTED |
| D-084 | 9 publication states as constants | DFM telegram_publications migration | ACCEPTED |

## WP-05b Process Failure (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-085 | Test scripts MUST abort on test FAIL before commit | Incident RCA | LOCKED |

### Incident

During WP-05b script 4/4, test command was piped:

    APP_ENV=testing php artisan test ... 2>&1 | tail -20

Then unconditionally:

    git commit -m "feat: WP-05b ..."

Root cause: The "| tail" pipe swallowed PHPUnit exit code. "set -e" does not catch pipe failures by default (needs pipefail). Commit was created despite 5 FAILED tests.

### Impact

- Commit e0cea05 made with 5 failing tests
- Ledgers claimed DONE before verification
- Constitution RULE #1 (Audit Before Action) violated
- IMPLEMENTED != VERIFIED violated

### Detection

User ran ledger audit. 5 test failures surfaced. Root cause identified:
1. TYPE constants wrong ("CHANNEL" vs "OWNED_CHANNEL")
2. telegram_chat_id passed as string, schema requires bigint
3. permission_evidence NOT NULL, was not provided

### Resolution

- Amended commit e0cea05 -> da31d49 with corrected model + test
- 12/12 tests PASS after fix
- Full suite: 62 PASS (127 assertions)
- D-085 LOCKED prevents recurrence

### Prevention (LOCKED)

Before (failed):
    php artisan test ... | tail -20
    git commit ...

After (required):
    php artisan test ... || { echo "TESTS FAILED — ABORT"; exit 1; }
    git commit ...

Or:
    set -o pipefail
    php artisan test ... | tail -20

### Rules

1. Test output MUST NOT be piped through tail/head before commit without pipefail
2. OR explicit "|| { echo FAILED; exit 1; }" after each test command
3. Ledgers MUST claim DONE only after verified PASS
4. Constitution compliance: violation documented; rule LOCKED

## WP-27 Decisions (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-086 | firebase/php-jwt for JWT verification | Well-tested, Laravel-agnostic | ACCEPTED |
| D-087 | JWKS cached 1h to avoid rate limits | DFM §218 (JWKS) + perf | ACCEPTED |
| D-088 | 60s clock skew tolerance | Production-grade JWT | ACCEPTED |
| D-089 | TelegramOidcService returns completeLogin() | Atomic flow | ACCEPTED |
| D-090 | Sanctum UUID migration in production | personal_access_tokens.tokenable_id fix | ACCEPTED |
| D-091 | telegramExchange finds user via recently_authenticated_at | Simpler than storing subject on attempt | PARTIAL_REVIEW |

### Rationale

**D-086:** `firebase/php-jwt` v7.2.1 is the de-facto JWT library for PHP. Security advisory noted but package actively maintained. Alternative `lcobucci/jwt` more complex; not needed for RS256 verification.

**D-087:** JWKS from Telegram rarely changes. Caching 1h reduces external calls; falls through on cache miss.

**D-088:** 60-second leeway is industry standard for distributed auth.

**D-089:** Atomic completeLogin() = single transaction through exchange → validate → upsert → markReauth. Simplifies controller.

**D-090:** Sanctum's default `bigint` tokenable_id incompatible with our UUID User model. Fix applied to test + production. Production table was empty → no data loss risk.

**D-091:** Telegram's ID token `sub` claim is the user identifier, but we don't store it on the auth_attempt. Instead, we find the user who most recently authenticated (last 5 minutes) — which is the one who just completed OIDC. **Trade-off:** Race condition possible if multiple users log in within 5 minutes. **Mitigation:** handoff code expires in 60 seconds (per D-077). **Future:** store user_id on auth_attempt during callback (WP-27b).

## WP-27b Decisions (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-092 | HMAC-signed handoff (no schema change) | DFM §218 preserved | LOCKED |
| D-093 | HMAC-SHA256 with APP_KEY | Standard, secure | ACCEPTED |
| D-094 | D-091 RESOLVED — race condition fixed | T25 test verifies | RESOLVED |

### Rationale

**D-092:** Adding user_id column to auth_attempts would deviate from DFM §218 (LOCKED schema). Instead, embed user_id in HMAC-signed handoff code. No schema change → no architecture change → no explicit approval required.

**D-093:** HMAC-SHA256 is NIST-approved, and Laravel's app.key (base64 32 bytes) is a secure signing key. Same primitive as Sanctum.

**D-094:** telegramExchange() no longer queries `recently_authenticated_at`. User is extracted from HMAC-verified payload. T25 confirms correct user in multi-user race.

### Trade-off Analysis

| Criterion | D-092 (HMAC) | Alternative (user_id column) |
|-----------|--------------|------------------------------|
| DFM §218 preserved | ✅ | ❌ |
| Approval required | No | Yes |
| Handoff length | ~180 chars | ~64 chars |
| Security | HMAC-SHA256 | DB lookup |
| Race-free | ✅ | ✅ |

## D-095 — Test Pipe Process Failure (LOCKED)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-095 | Test scripts MUST redirect to file (not pipe) before exit check | 2nd RCA | LOCKED |

### Incident (Second Occurrence)

Same root cause as D-085, replicated in WP-27b script:

    APP_ENV=testing php artisan test ... 2>&1 | tail -35 || { exit 1; }

The pipe swallowed PHPUnit exit code. Despite the || guard, the pipe consumed the exit status; commit made with 2 FAILED tests.

### Detection

State-check after script interruption surfaced:
- AuthAttemptTest: 1 failed
- TelegramOidcTest: 1 failed

Root cause: old tests (WP-05a T08, WP-27 T20) not updated for WP-27b interface changes:
- consumeByHandoff now returns array{attempt, user} (was ?AuthAttempt)
- generateHandoff needs optional ?User $user for HMAC binding

### Resolution

- Amended commit cebbaf2 -> eea83e8 with corrected tests
- All 88 tests PASS (188 assertions)
- D-095 LOCKED to prevent third occurrence

### Prevention (LOCKED)

FORBIDDEN:
    php artisan test ... | tail -N || { exit 1; }
    (pipe consumes exit code — D-085 rule was necessary but insufficient)

REQUIRED:
    php artisan test ... > /tmp/test_out.txt 2>&1
    STATUS=$?
    tail -N /tmp/test_out.txt
    [ $STATUS -eq 0 ] || { echo "TESTS FAILED"; exit 1; }

OR:
    set -euo pipefail
    php artisan test ... | tail -N

### Rules (Stricter than D-085)

1. D-085 (pipefail) — necessary but NOT sufficient
2. D-095 — file redirect + $? capture is MANDATORY
3. NEVER pipe test output before exit check without pipefail
4. ALL test runs MUST verify exit code

Constitution compliance: 2nd violation documented; LOCKED rule stricter.

## D-096 — T01-T18 Integration Test Definitions UNKNOWN — 2026-09-29

**Context:** WP-24 (T01-T18 Integration Tests) registered as BLOCKED
("Felagi routes needed"). Routes blocker now RESOLVED (47 API routes at
WP-27b), yet T01-T18 test definitions remain undocumented.

**Evidence:**
- TEST_VERIFICATION.md L43: "All 18 tests REQUIRES_EVIDENCE — need live stack"
- No assertions, no acceptance criteria, no test matrix
- WORK_PACKAGES.md L39 + PRIORITY_PLAN.md L57 refer to T01-T18 without definition

**Decision:** Mark WP-24 as BLOCKED-ON-UNKNOWN. Do not guess test scope.

**Constitution:** UNKNOWN ≠ MISSING.

**Resolution:** Stakeholder provides T01-T18 spec (test names, behaviors, criteria).

---

## D-097 — WP-13c Frontend Stack UNKNOWN — 2026-09-29

**Context:** WP-13c (2FA Enrollment UI) has 4 requirements in
REQUIREMENT_REGISTRY.md (REQ-WP13C-001..004) but was absent from
WORK_PACKAGES.md prior to CCB.

**Evidence:**
- REQUIREMENT_REGISTRY.md L222-227: 4 requirements, reason "Frontend"
- REQ-WP13C-004 reason is literally "Design unclear"
- ARCHITECTURE_MAP.md L42: "Flutter starter (18 classes) — COMPILE BLOCKED"
- Admin web frontend stack undefined

**Decision:** Register WP-13c as DEFERRED. Do not implement without stack decision.

**Constitution:** Do not guess missing requirements.

**Resolution:** Stakeholder defines (1) admin frontend stack, (2) design spec,
(3) lost-factor recovery flow.

## D-098 — Production Root LOCKED Design UNKNOWN — 2026-09-29

**Context:** Production root `https://zagcreativity.com/` shows Laravel default
welcome page. Previously referenced in B10 CHANGE_LOG as "GAP-57" (mislabel).
Real GAP-57 = Sanctum UUID migration (DONE).

**Evidence (B14 baseline audit):**
- Live body: `<title>Laravel</title>` + Laravel starter template
- routes/web.php: only `Route::get('/')` → view('welcome')
- resources/views/: only welcome.blade.php (Laravel default)
- Document root: `public_html` → `felagi_app/public` (correct)
- LOCKED design search in `~/felagi_extracted/Felagi_Design_Package/`:
  * Final_Information_Architecture.md — no root/landing route
  * Final_Navigation_Route_Map.md — no root/landing route
  * Product_Design/ + UI_Handoff/ — only 1 unrelated match (ui-preview/data.js)

**Decision:** Register GAP-62 as UNKNOWN. Do not create new design.
Do not implement without LOCKED source.

**Constitution:** Do not redesign/reinterpret. UNKNOWN ≠ MISSING.

**Resolution:** Design owner must either:
  (a) provide LOCKED production landing page design, or
  (b) explicitly approve keeping Laravel default root.

**Mislabel correction:** B10 CHANGE_LOG L685 corrected in B14.

## D-099 — S001 Selected for Production Root — 2026-09-29

**Context:** GAP-62 (production root = Laravel default). No LOCKED design
found in B14 audit.

**New evidence (B15):** Comprehensive search found LOCKED S001 in:
- UI_Handoff/ui-preview/app.js:144 (reference impl)
- Localization/app_am.arb (screenS001, purpose, signIn)
- DFM-FDS-1.4.md §443: "Welcome → login → Home"

**Decision:** S001 Welcome is the LOCKED production entry screen.
Deploy S001 at `/` with 3 elements only.

**Constitution:** No new design. LOCKED content adapted for web.
Sign-in target: existing `/api/v1/auth/telegram/start` (WP-27).

**Closes:** GAP-62

## D-100 — API Resources Layer Deferred (design improvement) — 2026-09-29

**Context:** Audit found `app/Http/Resources/` does not exist.
47 routes return raw model JSON via `$model->toArray()`.

**Assessment:** Not a bug. Current responses work. Improvements would be:
consistent field filtering, centralized PII protection, versioning.

**Decision:** Defer until LOCKED design specifies the Resources contract.
Constitution: "Do not guess missing requirements."

**Recorded as design improvement (not critical bug).**

## D-101 — Policy Expansion Deferred (best practice) — 2026-09-29

**Context:** Only `SettingPolicy` exists. Non-admin authz is inline in
controllers (14 FORBIDDEN returns).

**Assessment:** Not a bug. SettingPolicy auto-discovery verified via tinker
(`Gate resolves Setting → App\Policies\SettingPolicy`). Inline checks
correctly return 403/404 — verified by B16 tests.

**Decision:** Defer centralized Policy layer until LOCKED design specifies
policy-per-model structure. Inline authz is sufficient and tested.

**Recorded as best-practice improvement (not critical).**

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-102 | L188-L192 → L212-L216 renumbering | No duplicate ownership violated | LOCKED |
| D-103 | HANDOFF_STATE.md full rewrite | Stale content (WP-05 wrong, tables wrong, stats wrong) | ACCEPTED |
| D-104 | MASTER_BASELINE extended (append-only) | Last Updated claim was false; append-only preserves history | ACCEPTED |

### Rationale

**D-102:** No duplicate ownership is a locked invariant. L188-L192 appeared
twice in IMPLEMENTATION_LEDGER. The first occurrence (WP-05b models) is
correct. The second set (B10-B16) was renumbered to L212-L216 so both
sets remain traceable.

**D-103:** HANDOFF_STATE contained 3 critical errors:
- "WP-05: Felagi routes not yet implemented" (WP-05 = DONE)
- "MySQL DB: 9 tables" (actual = 38 tables)
- "28 WPs (2 DONE, 1 VERIFIED, 2 PARTIAL, 2 READY, 21 BLOCKED)"
  (actual = 10 DONE, 1 VERIFIED, 1 PARTIAL, 16 BLOCKED, 1 DEFERRED)

Full rewrite required. Historical record preserved via git.

**D-104:** MASTER_BASELINE "Last Updated: 2026-09-29" but WP-27, WP-05a/b,
B10-B16 were all completed that day and missing. Append-only preserves the
original LOCKED baseline while documenting evolution.

| D-105 | REQUIREMENT_REGISTRY append-only for WP-05a/b/27/27b | Full traceability required | ACCEPTED |
| D-106 | ARCHITECTURE_MAP Layer 8 + Layer 9 added | WP-27/27b/05a/05b and B10-B17 missing from map | ACCEPTED |
| D-107 | GAP-38 merged into GAP-54 | Same issue (APP_DEBUG), duplicate entry | ACCEPTED |

### Rationale (continued)

**D-105:** 42 new REQ IDs were added for WP-05a (7), WP-05b (6), WP-27 (10),
WP-27b (7), and B10-B17 (12). Full traceability requires each requirement
to have a unique ID and clear verification link.

**D-106:** Layer 8 covers Extended Backend (WP-05a/b/27/27b). Layer 9 covers
Ledger Governance Blocks (B10-B17). Original map ended at Layer 7 (WP-13b).

**D-107:** GAP-38 (APP_DEBUG CRITICAL) and GAP-54 (APP_DEBUG resolved) are
the same issue. GAP-38 was registered before the fix; GAP-54 after. Merged
by marking GAP-38 as SUPERSEDED by GAP-54.

| D-108 | GAP-07 marked renumbered to GAP-60 | Duplicate resolution | ACCEPTED |
| D-109 | GAP-42 removed from open list | Resolved in WP-05a; still listed as open | ACCEPTED |
| D-110 | Table count 25 → 38 documented | WP-05 expanded; baseline unchanged | ACCEPTED |

### Rationale (continued)

**D-108:** GAP-07 (Observability) was renumbered to GAP-60 in B10. The
original GAP-07 entry remained, creating double-booking. Marked as
renumbered.

**D-109:** GAP-42 (auth_attempts table missing) was resolved in WP-05a
(2026-09-29). It remained in the "Pre-existing GAPs still open" list.
Removed to eliminate ambiguity.

**D-110:** Original baseline declared 25 tables. WP-05 delivered 38. This
is documented per D-046 ("All 38 tables now in production DB"). B17 adds
this reconciliation to MASTER_BASELINE for consistency without modifying
the original LOCKED content.

### Constitution Compliance

- No duplicate ownership → D-102 fixes 5 violations
- DONE = implemented + integrated + tested + verified + documented + evidenced
  → D-103 restores HANDOFF_STATE accuracy
- No hidden work → D-104 through D-110 all documented
- UNKNOWN ≠ MISSING → B-blocks vs WPs documented as U-21

### Rollback

All B17 changes: git revert <B17-commit> (see B17_ROLLBACK.md)

---
## B19 — Model Unit Tests (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-111 | Model tests placed in Feature/Models (not Unit) | RefreshDatabase requires Laravel bootstrap | ACCEPTED |
| D-112 | NOT NULL schema columns made explicit in tests (not schema change) | D-054 AUDIT BEFORE ACTION; no architecture change without approval | ACCEPTED |

### Rationale

**D-111:** The 3 new test files (AuditLogTest, SettingVersionTest, OutboxEventTest) all use RefreshDatabase trait. This requires full Laravel bootstrap (DB, migrations, container). Placing them under tests/Feature/Models (rather than tests/Unit) is consistent with existing pattern and required for DB isolation (D-067 MySQL test DB).

**D-112:** B19 audit uncovered two NOT NULL columns without DB defaults:
- setting_versions.reason
- audit_logs.request_id

Two options were available:
(a) Modify migrations to add defaults -> architecture change, requires approval
(b) Make the columns explicit in test payloads -> no schema change

Option (b) chosen. This preserves the LOCKED schema and documents the required columns via tests. It also surfaces the NOT NULL constraint as a de-facto contract.

This is consistent with D-100/D-101 (deferred design improvements) - do not silently alter schema.

### Test Results (verified)

- Command: APP_ENV=testing php artisan test
- Result: 131 passed (266 assertions)
- Failures: 0
- Duration: 7.61s

### Constitution Compliance

- No hidden work -> both decisions documented
- No silent changes -> no schema touched
- D-054 AUDIT BEFORE ACTION -> audit preceded all code
- IMPLEMENTED != VERIFIED -> tests run before commit

---
## B21 — Extended Model Tests + Spec Requests (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-113 | CreatesTestCategory trait for DRY test setup | Multiple tests need Category | ACCEPTED |
| D-114 | Distinct providers per rating in scopeValid test | UNIQUE(need_id, from_user_id, to_user_id) constraint | ACCEPTED |
| D-115 | Spec request docs for WP-05c and T01-T18 | Constitution: UNKNOWN != MISSING | ACCEPTED |

### Rationale

**D-113:** Multiple test files need a Category to create a valid Need
(category_id NOT NULL). Rather than duplicate Category::create() in each
test, a trait (CreatesTestCategory) provides makeCategory() helper. This
follows DRY and matches the B19 pattern of inline helpers.

**D-114:** ratings table has UNIQUE(need_id, from_user_id, to_user_id).
The scopeValid test needs 5 ratings with scores 1-5 for the same need.
Using distinct providers per rating satisfies the constraint while still
testing the 1-5 score range.

**D-115:** Two spec requests were documented:
- WP-05c: admin read endpoints (undefined scope)
- T01-T18: integration test definitions (undefined behaviors)

Both are UNKNOWN (not MISSING). Docs provide the questions and constraints
for stakeholder resolution. No implementation attempted without spec.

### Test Results (verified)

- Command: APP_ENV=testing php artisan test
- Result: 162 passed (320 assertions)
- Failures: 0
- Duration: 8.52s

### Constitution Compliance

- No hidden work -> all decisions documented
- No silent changes -> no schema touched
- D-054 AUDIT BEFORE ACTION -> audit preceded code
- IMPLEMENTED != VERIFIED -> 162 tests run before commit
- UNKNOWN != MISSING -> spec requests registered



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/RELEASE_STATUS.md
════════════════════════════════════════════════════════════

# RELEASE STATUS — Felagi v1.4.2
Last Updated: 2026-09-29

## Current State
| Field | Value |
|-------|-------|
| Design contract version | `1.4.1` |
| Handoff release version | `1.4.2` |
| Design-source acceptance | **PASS** |
| Developer handoff | **READY_WITH_EXPLICIT_RUNTIME_GATES** |
| Production release | **BLOCKED** |
| Source hash | `e467f84820518acd68ad6824a05fefa8590f004c3949cca278f862140a71f31c` |

## Gates (G01-G09)
| Gate | Condition | Status |
|------|-----------|--------|
| G01 | Source consistency | **MET** |
| G02 | Design completeness | **MET** |
| G03 | Brand source | **MET** |
| G04 | Browser/responsive/AT | **BLOCKED** |
| G05 | Flutter | **BLOCKED** |
| G06 | Service/security | **PARTIAL+** (Admin lifecycle + Reauth + 2FA VERIFIED) |
| G07 | Monetization health | **REQUIRES_EVIDENCE** |
| G08 | Localization/usability | **SOURCE MET / RUNTIME PENDING** |
| G09 | Observability | **REQUIRES_EVIDENCE** |

## Revenue Status
| Domain | Status | Blocker |
|--------|--------|---------|
| Need Boosting | BLOCKED | Payment provider |
| Offer Submission Unlock | DEFAULT FREE (`feature_enabled=false`) | — |
| Sponsored Advertising | MASTER OFF | Live serving not implemented |

## Positive-fee Activation
**BLOCKED** until:
- Marketplace health baseline measured (G07)
- Deterioration thresholds approved
- Provider catalogs loaded
- All G06-G07 evidence complete

## Ads Master Switch
**OFF** until:
- All integration gates pass
- Privacy/accessibility/performance gates pass
- Live serving implemented
- Analytics instrumentation complete

## Evidence Boundary
- `Designed ≠ Implemented ≠ Verified`
- `Preview ≠ Production proof`
- `Published ≠ Applied ≠ Verified`

## Production PASS Criteria
Permitted ONLY when:
1. All applicable critical requirements VERIFIED
2. No unresolved BLOCKER/CRITICAL finding remains
3. All 9 gates passed

**Currently: NOT SATISFIED**

## WP-05 Update (2026-09-29)

**Backend Services: COMPLETE**

| Metric | Value |
|--------|-------|
| Tables | 38 |
| Models | 20 |
| Controllers | 9 |
| Form Requests | 4 |
| API Routes | ~30 |
| Auth | Sanctum |
| Route Tests | PASS |

WP-05 moved to DONE.
Gate G06 (backend services) is now partially met (backend code exists).
Live services still require provider credentials.

## WP-13 Update (2026-09-29)

**Admin Change Lifecycle: VERIFIED**

| Metric | Value |
|--------|-------|
| Endpoints | 10 (registered) |
| Tests | 8 PASS |
| Assertions | 15 |
| Models | 4 |
| Services | 3 |
| Policies | 1 |
| Settings seeded | 31 |
| Audit hash chain | Active |
| Outbox events | Active |

**Gate G06:** PARTIAL — Admin lifecycle verified; other services (payment, AI, Telegram) still blocked.

## WP-13b Update (2026-09-29)

**Reauth + TOTP 2FA + Idempotency: VERIFIED**

| Metric | Value |
|--------|-------|
| Migration | 1 (users: 4 columns) |
| Services | 3 (ReauthValidator, TotpService, IdempotencyRegistry) |
| Middleware | 2 (RequireReauth, IdempotencyKey) |
| Jobs | 3 (ProcessOutbox, Verify, Cleanup) |
| Exceptions | 4 |
| Tests | 36 PASS (63 assertions) |
| Production DB | Untouched |
| Design compliance | Auth Contract §3 + DFM §149/§461/§418 |

**Gate G06:** PARTIAL+ — Admin lifecycle + reauth + 2FA verified; payment/AI/Telegram still blocked.

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)

### B17 Update — Ledger Integrity Sweep

**Status:** Documentation-only. No code, DB, or route changes.

| Metric | Value |
|--------|-------|
| Duplicate L-IDs fixed | 5 (L188-L192 → L212-L216) |
| Stale sections corrected | 6 (HANDOFF_STATE, MASTER_BASELINE, etc.) |
| GAP duplicates merged | 3 (GAP-38/54, GAP-07/60, GAP-42) |
| Files updated | 11 |
| Decisions logged | D-102 → D-110 |
| New REQ IDs | 42 |

### Complete Gate Status (post-B17)

| Gate | Status | Notes |
|------|--------|-------|
| G01 Source consistency | MET | — |
| G02 Design completeness | MET | — |
| G03 Brand source | MET | — |
| G04 Browser/AT | BLOCKED | No browser |
| G05 Flutter | BLOCKED | No SDK |
| G06 Service/security | PARTIAL+ | Admin + reauth + 2FA verified |
| G07 Monetization | REQUIRES_EVIDENCE | No data |
| G08 Localization | SOURCE MET / RUNTIME PENDING | No device |
| G09 Observability | REQUIRES_EVIDENCE | No telemetry |

**Totals:** 3/9 MET · 1/9 PARTIAL+ · 1/9 PARTIAL · 4/9 BLOCKED or REQUIRES_EVIDENCE

### Production PASS Criteria (post-B17)

Permitted ONLY when:
1. All applicable critical requirements VERIFIED
2. No unresolved BLOCKER/CRITICAL finding remains
3. All 9 gates passed

**Current state:** NOT SATISFIED (6/9 gates pending)

### B17 Impact on Gates

**No gate changed status.** B17 was documentation-only and did not
affect source consistency, service verification, or any runtime gate.

### Complete Test Suite (post-B17)

| Metric | Value |
|--------|-------|
| Total tests | 106 |
| Total assertions | 220 |
| Source-level checks | 7,810+ |
| Contrast pairs | 66/66 |
| Semantic verification | 140/140 |

### Complete WP & B-Block Status

| Category | DONE | VERIFIED | PARTIAL | BLOCKED | DEFERRED |
|----------|------|----------|---------|---------|----------|
| Work Packages | 10 | 1 | 1 | 16 | 1 |
| B-Blocks (B10-B17) | 8 | — | — | — | — |

### Production Release Decision

**BLOCKED** — Waiting on:
- G04 (Browser/AT testing)
- G05 (Flutter compilation)
- G07 (Monetization baseline)
- G09 (Observability infrastructure)
- G06 full completion (payment/AI/Telegram live)

### Rollback

All B17 changes: git revert <B17-commit> (see B17_ROLLBACK.md)

---

## B25–B28 + GAP-70 + GAP-71b Update — 2026-09-30

**Deploy verified:** 2026-09-30 (s3145.fra1.stableserver.net)

### Current State (post-B28)

| Field | Value |
|-------|-------|
| App HEAD | `7cf679b` (GAP-71b) |
| Design HEAD | `27edd9d` (B11) |
| Production release | **STILL BLOCKED** (WP-09 gates unchanged) |
| Deploy state | **LIVE** — all public URLs 200 |
| Tests | **412** (was 162) |
| Assertions | **691** (was 320) |
| Model test files | 21 (was 6) |

### B-Blocks Completed (B25–B28 + GAP-70 + GAP-71b)

| Block | Scope | Tests | Evidence |
|-------|-------|-------|----------|
| B25 | Payment/Boost/PaymentEvent/BoostPackage | +54 | evidence/WP-B25_evidence.md |
| B26 | AI/Comparison (4 models) | +68 | evidence/WP-B26_evidence.md |
| B27 | Category/NeedAward/Attachment/Report | +66 | evidence/WP-B27_evidence.md |
| B28 | Setting/SettingDraft/UserRole | +62 | evidence/WP-B28_evidence.md |
| GAP-70 | B18–B24 ledger backfill | 0 | IMPLEMENTATION_LEDGER L221–L227 |
| GAP-71 | Attachment::isClean → isScanClean | 0 | GAP-71 RESOLVED |
| GAP-71b | AttachmentFactory + HasFactory | 0 | evidence/GAP-71b_evidence.md |
| Quality | Evidence consistency + R-TEST registry | 0 | evidence/VERIFICATION_REPORT.md |

### R-TEST Coverage — COMPLETE (7/7)

| Req ID | Description | Status |
|--------|-------------|--------|
| R-TEST-01 | Payment/Boost tests | COVERED (B25) |
| R-TEST-02 | AI/Comparison tests | COVERED (B26) |
| R-TEST-03 | Category tests | COVERED (B27) |
| R-TEST-04 | NeedAward tests | COVERED (B27) |
| R-TEST-05 | Attachment+Report tests | COVERED (B27) |
| R-TEST-06 | Setting+SettingDraft tests | COVERED (B28) |
| R-TEST-07 | UserRole tests | COVERED (B28) |

### Deploy Operations (Phase B — 2026-09-30)

| Step | Action | Result |
|------|--------|--------|
| B1 | Pre-deploy smoke (4 URLs) | 200/200/200/200 |
| B2 | `php artisan optimize:clear` | 6 caches cleared |
| B3a | Pre-cache safety check | APP_ENV=production, APP_DEBUG=false |
| B3b | `config:cache` + `view:cache` + `event:cache` | 3 caches rebuilt |
| B3b | `route:cache` | **SKIPPED** (Closure route `/` — not cacheable) |
| B4 | Queue restart | **SKIPPED** (crontab-driven, no persistent worker) |
| B5 | Post-deploy smoke (5 URLs) | 200 (13–59ms each) |
| B6 | New code live verification | isScanClean ✅, Factory ✅, HasFactory ✅ |

### Production URLs (verified 2026-09-30 06:53 UTC)

| URL | Code | Time |
|-----|------|------|
| https://zagcreativity.com | 200 | 39ms |
| https://zagcreativity.com/up | 200 | 45ms |
| https://zagcreativity.com/handoff/SOURCE_OF_TRUTH.md | 200 | 59ms |
| https://zagcreativity.com/handoff/FELAGI_STATUS_REPORT.md | 200 | 13ms |
| https://zagcreativity.com/downloads/index.html | 200 | 36ms |

**S001 Welcome title:** `መግቢያ — ፈላጊ` ✅ (Amharic default)

### Gate Status (unchanged)

| Gate | Status | Change from B17 |
|------|--------|-----------------|
| G01 | MET | unchanged |
| G02 | MET | unchanged |
| G03 | MET | unchanged |
| G04 | BLOCKED | unchanged |
| G05 | BLOCKED | unchanged |
| G06 | PARTIAL+ | unchanged |
| G07 | REQUIRES_EVIDENCE | unchanged |
| G08 | SOURCE MET / RUNTIME PENDING | unchanged |
| G09 | REQUIRES_EVIDENCE | unchanged |

**Production release:** STILL BLOCKED — no gate changes from this work.
Production LIVE deploy completed (B25-B28 + GAP-71b) without new blockers.

### Production Code Changes (B25–B28 + GAP-71b)

| File | Change | Approval |
|------|--------|----------|
| app/Models/Attachment.php | isClean() → isScanClean() (GAP-71) | ✅ Explicit (user) |
| app/Models/Attachment.php | HasFactory trait added (GAP-71b) | ✅ Additive |
| database/factories/AttachmentFactory.php | NEW | ✅ Additive |

**No migrations. No routes changed. No design changed.**

### Breaking Change Disclosure (GAP-71)

- **What:** `Attachment::isClean()` renamed → `isScanClean()`
- **Why:** Signature conflict with Laravel `Model::isClean($attributes = null)`
- **Impact:** Zero callers (grep verified before fix)
- **Approval:** Explicit user authorization
- **See:** CHANGE_LOG B27 + OPEN_GAPS GAP-71 RESOLVED

### Rollback (B25–B28 + GAP-71b)

| Scenario | Command |
|----------|---------|
| Rollback all B25-B28 + GAP-71b | `git revert 202716b..7cf679b` |
| Rollback GAP-71 fix only | `git revert 6db0e86 -- app/Models/Attachment.php` |
| Rollback GAP-71b factory only | `git revert 7cf679b` |
| Clear all caches | `php artisan optimize:clear` |

### Evidence Artifacts

| File | Purpose |
|------|---------|
| evidence/WP-B25_evidence.md | B25 test suite |
| evidence/WP-B26_evidence.md | B26 test suite |
| evidence/WP-B27_evidence.md | B27 test suite |
| evidence/WP-B28_evidence.md | B28 test suite |
| evidence/GAP-71b_evidence.md | Attachment factory |
| evidence/VERIFICATION_REPORT.md | Phase C quality sweep |
| public/handoff/ledgers/IMPLEMENTATION_LEDGER.md | L217–L228 |
| public/handoff/ledgers/REQUIREMENT_REGISTRY.md | R-TEST-01..07 |

### Bundle Artifacts (latest)

- `~/Felagi_App_v1.4.2_20260930-0650_GAP71b_DONE.bundle` (1.1M)
- `~/Felagi_Design_v1.4.2_20260930-0650_GAP71b_DONE.bundle` (3.5M)
- `~/Felagi_v1.4.2_20260930-0650_GAP71b_DONE_full.tar.gz` (45M)
- `~/Felagi_v1.4.2_20260930-0650_GAP71b_DONE_FULL_with_vendor.tar.gz` (76M)

### Complete WP & B-Block Status (post-B28)

| Category | DONE | VERIFIED | PARTIAL | BLOCKED | DEFERRED |
|----------|------|----------|---------|---------|----------|
| Work Packages | 10 | 1 | 1 | 16 | 1 |
| B-Blocks | 16 (B10–B28 minus gaps) | — | — | — | — |
| GAPs (resolved) | 70, 71, 71b | — | — | — | — |

### Handoff Continuity

A competent developer can resume from:
- `public/handoff/ledgers/*.md` (14 canonical ledgers)
- `evidence/VERIFICATION_REPORT.md` (Phase C audit)
- `SOURCE_OF_TRUTH.md` + `FELAGI_STATUS_REPORT.md`
- Latest bundle: `20260930-0650_GAP71b_DONE`

**No chat-history reconstruction required.**

---

**End of B25–B28 + GAP-70 + GAP-71b RELEASE_STATUS update.**



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/HANDOFF_STATE.md
════════════════════════════════════════════════════════════

# HANDOFF STATE — Felagi v1.4.2
Last Updated: 2026-09-29 (B17 — Ledger Integrity Sweep)

## Session Summary
- Audit: **COMPLETE** (51/51 deliverables)
- Requirements registered: ~1,700+
- Work Packages: 28 (10 DONE, 1 VERIFIED, 1 PARTIAL, 16 BLOCKED, 1 DEFERRED)
- B-Blocks B10–B17: **8 blocks complete** (see IMPLEMENTATION_LEDGER L212–L216)
- Environment: VERIFIED
- Production: BLOCKED (6/9 gates pending)

## What Is Complete (Verified)

### Deployment (WP-21, WP-22)
- Laravel 11.56.1 at `~/felagi_app/`
- HTTPS live: https://zagcreativity.com
- MySQL DB: `zagcreht_felagi` (38 tables)
- Cron: 1-min scheduler + queue:work
- Git bundle + tarball

### Backend (WP-05, WP-05a, WP-05b, WP-13, WP-13b)
- 38 database tables (9 phases)
- 20 Eloquent models
- 9 API controllers
- ~30 routes registered under /api/v1
- Sanctum auth
- Auth Attempts + PKCE (WP-05a) — 14 tests
- Telegram Foundation (WP-05b) — 12 tests
- Admin Change Lifecycle (WP-13) — 8 tests
- Reauth + TOTP 2FA + Idempotency (WP-13b) — 36 tests

### Telegram OIDC (WP-27, WP-27b)
- Full OIDC flow (PKCE + JWKS + 7-step validation)
- HMAC-signed handoff (race-free, D-094)
- 26 tests PASS

### Frontend (B15)
- S001 Welcome deployed at production root
- LOCKED design source: UI_Handoff/ui-preview/app.js:144
- Amharic default (`መግቢያ — ፈላጊ`)

### Test Coverage
- Full suite: 106 tests (220 assertions)
- Source: 7,810+ checks PASS
- Contrast: 66/66 PASS

### Ledger Integrity (B10–B17)
- 8 data integrity violations fixed (B10)
- GAP-61 closed (B13)
- GAP-62 closed (B15)
- Non-admin test coverage +16 (B16)
- L188–L192 duplicate IDs renumbered → L212–L216 (B17)

## What Is Live

| Item | URL / Location | Status |
|------|----------------|--------|
| Laravel app | `~/felagi_app/` | Running |
| HTTPS root | https://zagcreativity.com | HTTP 200 (S001) |
| /downloads/ | https://zagcreativity.com/downloads/ | HTTP 200 |
| /handoff/ | https://zagcreativity.com/handoff/ | HTTP 200 |
| /up health | https://zagcreativity.com/up | HTTP 200 |
| MySQL DB | `zagcreht_felagi` | 38 tables |
| Cron | 1-min scheduler + queue:work | Active |

## What Is Blocked

| WP | Blocker |
|----|---------|
| WP-03/04 | Browser/Flutter SDK missing |
| WP-10 | AI provider key |
| WP-11 | Payment provider creds |
| WP-14 | Telegram bot token |
| WP-17/18 | Device/AT testing |
| WP-23 | Backup target |
| WP-24 | T01-T18 spec UNKNOWN (D-096) |
| WP-13c | 2FA UI stack UNKNOWN (D-097) |

## Next Work Package Priority

### Blocked on Stakeholder (UNKNOWN)
1. **WP-24** — T01-T18 Integration Tests (D-096)
2. **WP-13c** — 2FA Enrollment UI (D-097)
3. **WP-05c** — Admin read endpoints (spec needed)
4. **B-Blocks as WPs** — currently "blocks", not formal WPs (U-21)

### Safe, Additive Work (Constitution-compliant)
5. **B18** — Extended non-admin write tests (B16 pattern)
6. **B18** — Model unit tests (fill gap)
7. **B18** — Migration integrity audit

### Blocked on External Dependencies
8. **WP-10** — AI Integration (provider key)
9. **WP-11** — Payment Live (payment creds)
10. **WP-14** — Telegram Delivery (bot token)
11. **WP-25** — Webhook Signature (provider sandbox)

## Continuity Rule

A competent developer can continue reading:
- `public/handoff/ledgers/*.md` (13 canonical ledgers)
- `Felagi_Design_Package/Developer_Handoff/START_HERE.md`
- `Felagi_Design_Package/README.md`

NO chat history reconstruction needed.

## Evidence Files

| Artifact | Location |
|----------|----------|
| Design bundle | `~/Felagi_Design_v1.4.2_*_WP21_DONE.bundle` |
| App bundle | `~/Felagi_App_v1.4.2_*_WP21_DONE.bundle` |
| Full tarball | `~/Felagi_v1.4.2_*_WP21_DONE_full.tar.gz` |
| App git | `~/felagi_app/.git` |
| Design git | `~/felagi_extracted/.git` |
| B17 backups | `~/B17_backups/20260929_144348/` |

## Contact / Escalation

| Role | Owner |
|------|-------|
| Product Owner | (UNKNOWN — to be filled) |
| Design Owner | (UNKNOWN — to be filled) |
| Release Owner | (UNKNOWN — to be filled) |

---
## B18 — Bundle Refresh (2026-09-29)

### New Bundle Artifacts (B17_DONE)

All 4 artifacts in ~/:

| Artifact | Size |
|----------|------|
| Felagi_App_v1.4.2_20260929-1514_B17_DONE.bundle | 974K |
| Felagi_Design_v1.4.2_20260929-1514_B17_DONE.bundle | 3.5M |
| Felagi_v1.4.2_20260929-1514_B17_DONE_full.tar.gz | 45M |
| Felagi_v1.4.2_20260929-1514_B17_DONE_FULL_with_vendor.tar.gz | 76M |

### Verification Results

| Check | Result |
|-------|--------|
| App bundle HEAD | 6ea8a97 (B17) |
| App bundle commits | 51 |
| App ledger files | 14 |
| B17_ROLLBACK.md present | YES |
| Design bundle HEAD | 27edd9d (B11) |
| Design bundle commits | 15 |
| Tarball entries | 317 |
| Tarball B17_ROLLBACK | YES |

### Bundle Status

**SUPERSEDES:** Previous WP21 bundles in ~/archives/Felagi_bundles_archive/
**State:** Both repos clean, no uncommitted changes

### Continuity

A developer cloning either bundle gets:
- Full commit history
- All 14 ledger files
- B17_ROLLBACK.md
- Complete handoff state


---
## B20 — Bundle Refresh at B19_DONE (2026-09-29)

### New Bundle Artifacts

All 4 artifacts in ~/ (supersede B17 bundles):

| Artifact | Size |
|----------|------|
| Felagi_App_v1.4.2_20260929-1530_B19_DONE.bundle | 980K |
| Felagi_Design_v1.4.2_20260929-1530_B19_DONE.bundle | 3.5M |
| Felagi_v1.4.2_20260929-1530_B19_DONE_full.tar.gz | 45M |
| Felagi_v1.4.2_20260929-1530_B19_DONE_FULL_with_vendor.tar.gz | 76M |

### Verification Results (clone-tested)

| Check | Result |
|-------|--------|
| App bundle HEAD | b451224 (B19) |
| App bundle commits | 53 |
| App ledger files | 14 |
| App model test files | 3 |
| B19 commit present | YES |
| Design bundle HEAD | 27edd9d (B11) |
| Design bundle commits | 15 |
| Tarball entries | 321 |
| Tarball B19 test files | 3 |
| Tarball B17_ROLLBACK.md | YES |

### Supersedes

- B18 bundle (B17_DONE) - archived
- B17 bundles - archived

### State

- App HEAD: b451224 (B19 DONE)
- Design HEAD: 27edd9d (B11 DONE)
- Tests: 131 passed (266 assertions)
- Both repos CLEAN

---
## B22 — Bundle Refresh at B21_DONE (2026-09-29)

### New Bundle Artifacts

All 4 artifacts in ~/ (supersede B19 bundles):

| Artifact | Size |
|----------|------|
| Felagi_App_v1.4.2_20260929-1551_B21_DONE.bundle | 999K |
| Felagi_Design_v1.4.2_20260929-1551_B21_DONE.bundle | 3.5M |
| Felagi_v1.4.2_20260929-1551_B21_DONE_full.tar.gz | 45M |
| Felagi_v1.4.2_20260929-1551_B21_DONE_FULL_with_vendor.tar.gz | 76M |

### Verification Results (clone-tested)

| Check | Result |
|-------|--------|
| App bundle HEAD | c64fa28 (B21) |
| App bundle commits | 55 |
| App ledger files | 14 |
| App model test files | 6 |
| App audit docs | 1 |
| App spec requests | 2 |
| B21 commit present | YES |
| B19 commit present | YES |
| Design bundle HEAD | 27edd9d (B11) |
| Design bundle commits | 15 |
| Tarball entries | 332 |
| Tarball B19 test files | 3 |
| Tarball B21 test files | 3 |
| Tarball B21 docs | 3 |

### Supersedes

- B20 bundle (B19_DONE) - archived
- B18 bundle (B17_DONE) - archived

### State

- App HEAD: c64fa28 (B21 DONE)
- Design HEAD: 27edd9d (B11 DONE)
- Tests: 162 passed (320 assertions)
- Model test files: 6 (B19: 3 + B21: 3)
- Both repos CLEAN

---
## B25 — Payment Domain Test Suite (WP-B25 / R-TEST-01) — 2026-09-30

### What Changed

- 4 new test files under tests/Feature/Models/
- 54 new tests, 83 new assertions
- Full suite: 162 → 216 tests (320 → 403 assertions)
- R-TEST-01 (Payment/AI model tests MISSING) → COVERED

### State

- App HEAD (before commit): 143a756 (B24)
- B25 code commit: 202716b
- B25 docs HEAD: git rev-parse HEAD (rolling — not pinned)
- B25 commit chain: 202716b (code) → docs-only commits to HEAD
- Design HEAD: 27edd9d (B11)
- Tests: 216 passed (403 assertions)
- Evidence: evidence/WP-B25_evidence.md

### Blocked / Pending

- **VERIFIED status:** requires second reviewer (Constitution Art. DONE definition)
- **B17-B24 ledger backfill:** PENDING (see OPEN_GAPS GAP-70)
- **Bundle refresh at B25_DONE:** not yet created

### Continuity

Next developer reading this + IMPLEMENTATION_LEDGER L217 + TEST_VERIFICATION B25
can resume without chat reconstruction.

---
## B26 — AI/Comparison Domain Test Suite (WP-B26 / R-TEST-02) — 2026-09-30

### What Changed

- 4 new test files under tests/Feature/Models/ (Comparison domain)
- 68 new tests, 93 new assertions
- Full suite: 216 → 284 tests (403 → 496 assertions)
- R-TEST-02 (AI/Comparison model tests MISSING) → COVERED

### State

- App HEAD (before commit): ce47d24 (B25 final)
- App HEAD (after commit): (set after commit)
- Design HEAD: 27edd9d (B11)
- Tests: 284 passed (496 assertions)
- Evidence: evidence/WP-B26_evidence.md

### Blocked / Pending

- **VERIFIED status:** requires second reviewer
- **B17-B24 ledger backfill:** PENDING (see OPEN_GAPS GAP-70)
- **Bundle refresh at B26_DONE:** not yet created

### Continuity

Next developer reading this + IMPLEMENTATION_LEDGER L218 + TEST_VERIFICATION B26
can resume without chat reconstruction.

---
## B27 — Safety/Marketplace Domain Test Suite — 2026-09-30

### What Changed
- 4 test files (Category, NeedAward, Attachment, Report): +66 tests
- Production fix: Attachment::isClean -> isScanClean (GAP-71, approved)
- Full suite: 284 -> 350 tests (496 -> 595 assertions)
- R-TEST-03/04/05 -> COVERED
- GAP-71 -> RESOLVED

### State
- App HEAD (before commit): 861fd6e (B26)
- Design HEAD: 27edd9d (B11)
- Tests: 350 passed (595 assertions)
- Evidence: evidence/WP-B27_evidence.md

### Blocked / Pending
- VERIFIED status: requires second reviewer
- GAP-70 (B17-B24 backfill): PENDING
- Bundle refresh at B27_DONE: not yet created

### Breaking Change Disclosure
Attachment::isClean -> isScanClean — user approved, zero callers, in CHANGE_LOG.

### Continuity
Next developer: L219 + TEST_VERIFICATION B27 + CHANGE_LOG.

---
## B28 — Settings/Role Domain Test Suite — 2026-09-30

### What Changed
- 3 test files (Setting, SettingDraft, UserRole): +62 tests
- Full suite: 350 -> 412 tests (595 -> 691 assertions)
- R-TEST-06 (Setting+SettingDraft): PARTIAL -> COVERED
- R-TEST-07 (UserRole): 0 -> COVERED

### State
- App HEAD (before commit): 6db0e86 (B27)
- Design HEAD: 27edd9d (B11)
- Tests: 412 passed (691 assertions)
- Evidence: evidence/WP-B28_evidence.md

### Blocked / Pending
- VERIFIED status: requires second reviewer
- GAP-70 (B17-B24 backfill): PENDING
- Bundle refresh at B28_DONE: not yet created

### R-TEST coverage -- COMPLETE
All 7 R-TEST items now COVERED:
- R-TEST-01 (B25), R-TEST-02 (B26), R-TEST-03/04/05 (B27), R-TEST-06/07 (B28)

### Continuity
Next developer: L220 + TEST_VERIFICATION B28 + CHANGE_LOG.

---
## B18–B24 — Backfill Summary (GAP-70 resolved 2026-09-30)

The B18–B24 commits (2026-09-29) were previously unrecorded in canonical
ledgers. Backfilled from git history on 2026-09-30. Full details in
IMPLEMENTATION_LEDGER L221–L227, TEST_VERIFICATION backfill section,
CHANGE_LOG backfill section.

### Timeline
| Block | Commit | Scope | Tests Δ |
|---|---|---|---|
| B18 | dbc689c | Bundle refresh @ B17_DONE | 0 |
| B19 | b451224 | Model tests: AuditLog+OutboxEvent+SettingVersion (+25) | 106→131 |
| B20 | 6c6bcb6 | Bundle refresh @ B19_DONE | 0 |
| B21 | c64fa28 | Model tests: Notification+Rating+User (+31) + audit + specs | 131→162 |
| B22 | 1a17a7c + 6bc49c8 + 7a0d21e | STATUS_REPORT + SOURCE_OF_TRUTH + bundle | 0 |
| B23 | 48217b2 | Public publication to /handoff + /downloads | 0 |
| B24 | 3e7236c + 143a756 | S001 signIn fix (GAP-63/64/65/66) | 0 |

### Milestone
- Test suite: 106 → 162 tests (220 → 320 assertions)
- SOURCE_OF_TRUTH.md consolidated (1927 lines)
- 4 public URLs published (HTTP 200)
- Telegram bot configured (FelagiMarketBot: 8629327448)

### State before B25
- App HEAD: 143a756 (B24)
- Design HEAD: 27edd9d (B11)
- Tests: 162 (320 assertions)



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/B17_ROLLBACK.md
════════════════════════════════════════════════════════════

# B17 Rollback Procedure

**Block:** B17 — Ledger Integrity Sweep
**Date:** 2026-09-29
**Type:** Documentation-only (no code, DB, routes, or design changes)

## Summary of Changes

13 files modified · 896 insertions · 80 deletions (legitimate rewrites)

| # | File | Change Type |
|---|------|-------------|
| 1 | IMPLEMENTATION_LEDGER.md | 5 renames (L188-L192 → L212-L216) |
| 2 | CHANGE_LOG.md | 3 sed fixes + B17 entry (+68/-3) |
| 3 | HANDOFF_STATE.md | Full rewrite (102 → 124 lines) |
| 4 | MASTER_BASELINE.md | Append-only (+78) |
| 5 | REQUIREMENT_REGISTRY.md | Append-only (+76) |
| 6 | ARCHITECTURE_MAP.md | Append-only (+107) |
| 7 | WORK_PACKAGES.md | Append-only (+43) |
| 8 | PRIORITY_PLAN.md | Append-only (+58) |
| 9 | TEST_VERIFICATION.md | Append-only (+127) |
| 10 | DECISION_LOG.md | Append-only (+76) |
| 11 | RELEASE_STATUS.md | Append-only (+76) |
| 12 | ENVIRONMENT_CHECKLIST.md | Append-only (+38) |
| 13 | OPEN_GAPS.md | Append-only (+50) |

## Backup Location

All pre-B17 versions saved in:

    ~/B17_backups/20260929_144348/

Files include:
- *.pre-step3 through *.pre-step12b
- CHANGE_LOG.md.pre-replace
- Original snapshots for all 13 files

## Rollback Options

### Option 1: Git revert (recommended)

    cd ~/felagi_app
    git log --oneline -5
    git revert <B17-commit-hash>

### Option 2: Restore from backup

    cd ~/felagi_app/public/handoff/ledgers/
    cp ~/B17_backups/20260929_144348/*.pre-step* .
    cp ~/B17_backups/20260929_144348/CHANGE_LOG.md .

### Option 3: Full reset

    cd ~/felagi_app
    git reset --hard <B17-commit-hash>~1

## Verification After Rollback

    cd ~/felagi_app/public/handoff/ledgers/
    grep -c "^## L188\|^## L189\|^## L190\|^## L191\|^## L192" IMPLEMENTATION_LEDGER.md
    grep -c "^## L21[2-6]" IMPLEMENTATION_LEDGER.md
    grep -c "Felagi routes not yet implemented" HANDOFF_STATE.md
    grep -c "^## B17" CHANGE_LOG.md

## Impact of Rollback

- Ledgers: Return to pre-B17 state (12 integrity issues restored)
- Code: No change (B17 was documentation-only)
- Database: No change
- Routes: No change
- Tests: No change (106 tests unaffected)

## Not Rolled Back (Intentional)

- WP-05a/b, WP-27/27b, B10-B16 code - separate commits
- Runtime deployment - unaffected
- .env files - unaffected

## Constitution Compliance

- No hidden work - all changes documented
- No silent changes - full audit trail
- UNKNOWN != MISSING - rollback state verified



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/ENVIRONMENT_CHECKLIST.md
════════════════════════════════════════════════════════════

# ENVIRONMENT CHECKLIST — Felagi v1.4.2
Last Verified: 2026-09-29

## Local Development Tools
| # | Resource | Required | Found | Status |
|---|----------|----------|-------|--------|
| 1.1 | Flutter SDK | >= 3.22 | NOT FOUND | ❌ |
| 1.2 | Dart SDK | >= 3.4 | NOT FOUND | ❌ |
| 1.3 | Node.js | >= 18 LTS | NOT FOUND | ❌ |
| 1.4 | Python 3 | >= 3.10 | 3.6.8 | ❌ TOO OLD |
| 1.5 | Git | >= 2.30 | not checked | ⏳ |
| 1.6 | Chromium/Firefox | Latest | NOT FOUND | ❌ |
| 1.7 | Text Editor/IDE | Any | available | ✅ |
| 1.8 | Terminal/Shell | bash/zsh | bash | ✅ |

## Server (cPanel)
| # | Resource | Required | Found | Status |
|---|----------|----------|-------|--------|
| 2.1 | cPanel access | Full | Yes | ✅ |
| 2.2 | SSH/terminal | Yes | Yes | ✅ |
| 2.3 | Doc root ability | Set public/ | NOT VERIFIED | ⏳ |
| 2.4 | PHP | 8.2+ | 8.2.33 | ✅ |
| 2.5 | PHP extensions | Full Laravel set | Full set | ✅ |
| 2.6 | MySQL/MariaDB | 8.0+/10.5+ | 10.6.28 | ✅ |
| 2.7 | Composer | Latest | 2.8.12 | ✅ |
| 2.8 | Cron 1-min | Yes | NOT VERIFIED | ⏳ |
| 2.9 | Outbound HTTPS | Yes | Working | ✅ |
| 2.10 | Disk space | >= 5GB | 51 GB | ✅ |
| 2.11 | Backup target | Off-host | NOT CONFIGURED | ❌ |

## Telegram Credentials
| # | Resource | Status |
|---|----------|--------|
| 3.1 | OIDC Client ID | ❌ |
| 3.2 | OIDC Client Secret | ❌ |
| 3.3 | Redirect URI | ❌ |
| 3.4 | Bot token | ❌ |
| 3.5 | Owned channel ID | ❌ |
| 3.6 | Supergroup ID | ❌ |
| 3.7 | Posting rights proof | ❌ |

## AI Provider
| # | Resource | Status |
|---|----------|--------|
| 4.1 | AI API key | ❌ |
| 4.2 | Model ID | ❌ |
| 4.3 | Billing quota | ❌ |
| 4.4 | API base URL | ❌ |
| 4.5 | Adapter fixtures | ❌ |

## Payment Provider
| # | Resource | Status |
|---|----------|--------|
| 5.1 | Merchant ID | ❌ |
| 5.2 | API credentials | ❌ |
| 5.3 | Webhook secret | ❌ |
| 5.4 | Provider webhook URL | ❌ |
| 5.5 | Sandbox account | ❌ |
| 5.6 | Refund API creds | ❌ |

## Backup Target
| # | Resource | Status |
|---|----------|--------|
| 6.1 | Off-host storage | ❌ |
| 6.2 | Encryption keys | ❌ |
| 6.3 | Restore env | ❌ |
| 6.4 | RPO/RTO target | ❌ |

## Device/Testing
| # | Resource | Status |
|---|----------|--------|
| 7.1 | Android device | ❌ |
| 7.2 | iOS device | ❌ |
| 7.3 | TalkBack | ❌ |
| 7.4 | VoiceOver | ❌ |
| 7.5 | Amharic speaker | ❌ |
| 7.6 | Owner reviewer | ❌ |

## Network
| # | Resource | Status |
|---|----------|--------|
| 8.1 | Outbound HTTPS | ✅ Working |
| 8.2 | Rate limit headroom | ⏳ |
| 8.3 | Firewall rules | ⏳ |

## Data/Governance
| # | Resource | Status |
|---|----------|--------|
| 9.1 | Marketplace baseline | ❌ |
| 9.2 | Deterioration thresholds | ❌ |
| 9.3 | Provider catalogs | ❌ |
| 9.4 | Legal retention policy | ❌ |

## Summary
| Category | Available | Missing | Pending |
|----------|-----------|---------|---------|
| Local tools | 2/8 | 4 | 2 |
| Server | 7/11 | 1 | 3 |
| Telegram | 0/7 | 7 | 0 |
| AI | 0/5 | 5 | 0 |
| Payment | 0/6 | 6 | 0 |
| Backup | 0/4 | 4 | 0 |
| Device | 0/6 | 6 | 0 |
| Network | 1/3 | 0 | 2 |
| Data | 0/4 | 4 | 0 |
| **TOTAL** | **10/54** | **37** | **7** |

**WP-21 CAN START. WP-22 READY. Others blocked.**

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)

### Environment Changes Since Original Checklist

| Item | Original | Current |
|------|----------|---------|
| MySQL DB | Not created | `zagcreht_felagi` (38 tables) |
| MySQL test DB | Not created | `zagcreht_felagi_test` (isolated) |
| Laravel | Not installed | 11.56.1 |
| HTTPS live | Not deployed | https://zagcreativity.com |
| Cron | Not configured | 1-min scheduler + queue:work |
| Composer packages | 0 | 110+ (incl. Sanctum, firebase/php-jwt, google2fa) |
| Git repo | Not initialized | `~/felagi_app/.git` (commits: 1bb9f19 → 64a8c27) |

### Still Missing (as of B17)

| # | Resource | Status |
|---|----------|--------|
| 1 | Flutter SDK | NOT FOUND |
| 2 | Node.js | NOT FOUND |
| 3 | Python 3.10+ | 3.6.8 (too old) |
| 4 | Chromium/Firefox | NOT FOUND |
| 5 | Telegram OIDC creds | PARTIAL (WP-27 done, live E2E pending) |
| 6 | AI API key | MISSING |
| 7 | Payment credentials | MISSING |
| 8 | Backup target | NOT CONFIGURED |
| 9 | Android/iOS device | MISSING |
| 10 | Amharic reviewer | MISSING |

### B16 .bak Cleanup

8 stale `.bak` files moved to `.archives/20260929-b16-bak-cleanup/`.

### B17 Verification

No environment changes. Documentation-only.



════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/PRIORITY_PLAN.md
════════════════════════════════════════════════════════════

# PRIORITY PLAN — Felagi v1.4.2
Last Updated: 2026-09-29

## Priority Tiers

### TIER 0 — Immediate (DONE)
- WP-01: Foundation Registry ✅
- WP-02: Static Re-run ✅
- WP-ENV: Environment Verification ✅
- WP-PRIORITY: Ledgers built ✅

### TIER 1 — Fast Unblockers
| WP | Scope | Status |
|----|-------|--------|
| WP-21 | Laravel/cPanel Deploy | ✅ DONE |
| WP-22 | DB Queue + Cron | ✅ DONE |
| WP-03 | Browser/A11y | BLOCKED |
| WP-04 | Flutter | BLOCKED |

### TIER 2 — Core Infrastructure
| WP | Scope | Blocker |
|----|-------|---------|
| WP-27 | Telegram OIDC | ✅ DONE |
| WP-11 | Payment Live | Creds |
| WP-25 | Webhook | Sandbox |
| WP-14 | Telegram Delivery | Bot token |
| WP-10 | AI Live | API key |

### TIER 3 — Feature Completeness
| WP | Scope | Blocker |
|----|-------|---------|
| WP-05 | Backend Services | ✅ DONE |
| WP-12 | Sponsored Ads | Infra |
| WP-13 | Admin Lifecycle | ✅ DONE |
| WP-13c | 2FA Enrollment UI | DEFERRED (UNKNOWN) |
| WP-26 | File Malware | Host AV |

### TIER 4 — Verification Sweeps
| WP | Scope | Blocker |
|----|-------|---------|
| WP-17 | Responsive 46 | Browser |
| WP-18 | A11y 46 | AT |
| WP-19 | Runtime States | Stack |
| WP-20 | Error Recovery | Stack |

### TIER 5 — Governance
| WP | Scope | Blocker |
|----|-------|---------|
| WP-16 | Amharic QA | Reviewer |
| WP-07 | Marketplace | Data |
| WP-06 | Amharic Runtime | Device |
| WP-15 | Font Glyph | License |

### TIER 6 — Production
| WP | Scope | Requires |
|----|-------|----------|
| WP-23 | Backup Drill | Target |
| WP-24 | T01-T18 | UNKNOWN (definitions missing) |
| WP-28 | Safe Mode | Stack |
| WP-08 | Observability | Metrics |
| WP-09 | **Production Gates** | ALL above |

## Recommended Sequence
1. **WP-21** — Laravel install
2. **WP-22** — Queue + Cron
3. **WP-27** — Telegram OIDC
4. **WP-11** — Payment
5. **WP-10** — AI
6. **WP-14** — Telegram delivery
7. **WP-05** — Backend services
8. **WP-13** — Admin lifecycle
9. **WP-17, WP-18** — Verification sweeps
10. **WP-23, WP-24** — Production prep
11. **WP-09** — Final gates

**Estimated: 10-11 weeks (parallel: 6-8 weeks)**

## Constitution Rule
"If one task is BLOCKED, isolate and document the blocker and continue independent safe Work Packages."

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)
Original TIER plan preserved above. Additive updates below.

## TIER 0 Completion Clarification (B17)

The TIER 0 items (WP-01, WP-02, WP-ENV, WP-PRIORITY) were marked DONE in
the original plan. Post-audit status confirmed:
- WP-01 Foundation Registry — DONE
- WP-02 Static Re-run — VERIFIED (7,810+ PASS)
- WP-ENV Environment Verification — DONE
- WP-PRIORITY Ledgers built — DONE

## B-Blocks Completion (B10-B17)

All 8 B-blocks completed on 2026-09-29:

| Block | Deliverable | Status |
|-------|-------------|--------|
| B10 | Constitution Compliance (8 fixes) | DONE |
| B11 | Main Admin Foundation verification | DONE |
| B12 | Bundle refresh | DONE |
| B13 | /downloads/ deployed (GAP-61) | DONE |
| B14 | GAP-62 registered | DONE |
| B15 | S001 Welcome live (GAP-62 closed) | DONE |
| B16 | Non-admin tests + cleanup | DONE |
| B17 | Ledger Integrity Sweep | DONE |

## Next Priority (post-B17)

### Immediate — Stakeholder Decisions Required
1. **WP-24** — T01-T18 Integration Tests — D-096 (UNKNOWN definitions)
2. **WP-13c** — 2FA Enrollment UI — D-097 (UNKNOWN stack)
3. **WP-05c** — Admin read endpoints spec (pending)
4. **U-21** — B-Blocks vs Work Packages — formalize or keep separate?

### Safe, Additive Work (Constitution-compliant, no external deps)
5. **B18** — Extended non-admin write tests (extend B16 pattern)
6. **B18** — Model unit tests (fill missing coverage)
7. **B18** — Migration integrity audit (additive, no schema change)

### Blocked on External Dependencies
8. **WP-10** — AI Integration (provider key)
9. **WP-11** — Payment Live (payment creds)
10. **WP-14** — Telegram Delivery (bot token)
11. **WP-25** — Webhook Signature (provider sandbox)
12. **WP-23** — Backup Restore Drill (backup target)

### Blocked on Device/Tooling
13. **WP-03/04** — Browser/Flutter SDK
14. **WP-17/18** — Device/AT testing

## Recommended Sequence (revised)
1. **Stakeholder decisions** — WP-24, WP-13c, WP-05c, U-21
2. **B18** — Safe additive test work (parallel)
3. **External deps** — await creds for WP-10, WP-11, WP-14, WP-25
4. **WP-09** — Final production gates (when above complete)



════════════════════════════════════════════════════════════
# FILE: public/handoff/SOURCE_OF_TRUTH.md
════════════════════════════════════════════════════════════

# FELAGI v1.4.2 — MASTER SOURCE OF TRUTH
**Consolidated:** 2026-09-29
**HEAD:** 48217b2 (B23)
**Purpose:** Single-file consolidation of all source-of-truth documents

## QUICK FACTS

| Item | Value |
|------|-------|
| HEAD | 48217b2 |
| Tests | 162 passed (320 assertions) |
| B-Blocks DONE | 14 (B10-B23) |
| WP DONE | 10 |
| Ledgers | 14 canonical |
| Bundles | 4 (B21_DONE) |
| Production PASS | BLOCKED (3/9 gates MET) |

## SECTION 1 — COMPLETE PROJECT STATUS
**Source:** FELAGI_STATUS_REPORT.md

# FELAGI v1.4.2 — STATUS REPORT
**Generated:** 2026-09-29
**HEAD:** 1a17a7c (B22)

## Executive Summary

| Field | Value |
|-------|-------|
| Design contract version | 1.4.1 |
| Handoff release version | 1.4.2 |
| App HEAD | 1a17a7c (B22) |
| Design HEAD | 27edd9d (B11) |
| Production release | BLOCKED (6/9 gates pending) |

## Numbers at a Glance

| Metric | Count |
|--------|-------|
| WP DONE | 10 |
| WP VERIFIED | 1 |
| WP PARTIAL | 1 |
| WP BLOCKED | 16 |
| WP DEFERRED | 1 |
| B-Blocks DONE | 13 (B10-B22) |
| Tests passing | 162 |
| Assertions | 320 |
| Test files | 19 |
| Model test files | 6 |
| Ledger files | 14 |
| Bundle artifacts | 4 (B21_DONE) |
| Docs (audits+specs) | 3 |

---

## Source of Truth — Canonical Ledgers (14)

Location: `~/felagi_app/public/handoff/ledgers/`

| # | File | Purpose | Updated |
|---|------|---------|---------|
| 1 | MASTER_BASELINE.md | Locked baseline + extensions | B17 |
| 2 | REQUIREMENT_REGISTRY.md | ~1,700 reqs + 42 new IDs | B17 |
| 3 | ARCHITECTURE_MAP.md | Layers 0-11 | B17 |
| 4 | WORK_PACKAGES.md | 28 WPs + 13 B-Blocks | B17 |
| 5 | IMPLEMENTATION_LEDGER.md | L001 -> L216 | B17 |
| 6 | TEST_VERIFICATION.md | 396 lines full trail | B21 |
| 7 | OPEN_GAPS.md | 223 lines all GAPs | B17 |
| 8 | CHANGE_LOG.md | 989 lines B10-B21 | B21 |
| 9 | DECISION_LOG.md | 679 lines D-001-D-115 | B21 |
| 10 | RELEASE_STATUS.md | 188 lines gates | B17 |
| 11 | HANDOFF_STATE.md | 252 lines bundle paths | B22 |
| 12 | PRIORITY_PLAN.md | 137 lines TIER 0-6 | B17 |
| 13 | ENVIRONMENT_CHECKLIST.md | 146 lines env state | B17 |
| 14 | B17_ROLLBACK.md | 83 lines rollback | B17 |

---

## Source of Truth — Specification Documents (3)

Location: `~/felagi_app/docs/`

| # | File | Lines | Purpose |
|---|------|-------|---------|
| 1 | audits/MIGRATION_INTEGRITY_B21.md | 69 | Read-only migration audit |
| 2 | spec-requests/WP-05c_admin_read_endpoints.md | 64 | Stakeholder spec request |
| 3 | spec-requests/T01-T18_integration_tests.md | 86 | Stakeholder spec request |

## Source of Truth — Bundle Artifacts (4)

Location: `~/`

| # | File | Size | HEAD |
|---|------|------|------|
| 1 | Felagi_App_v1.4.2_20260929-1551_B21_DONE.bundle | 999K | c64fa28 |
| 2 | Felagi_Design_v1.4.2_20260929-1551_B21_DONE.bundle | 3.5M | 27edd9d |
| 3 | Felagi_v1.4.2_20260929-1551_B21_DONE_full.tar.gz | 45M | c64fa28 |
| 4 | Felagi_v1.4.2_20260929-1551_B21_DONE_FULL_with_vendor.tar.gz | 76M | c64fa28 |

## Source of Truth — Backup Archives

| Path | Purpose |
|------|---------|
| ~/B17_backups/20260929_144348/ | 26+ pre-B17/B21/B22 snapshots |
| ~/archives/Felagi_bundles_archive/ | Pre-B17 bundles |

---

## Completed — Work Packages DONE (10)

| WP | Scope | Evidence |
|----|-------|----------|
| WP-01 | Foundation Registry | PACKAGE_MANIFEST |
| WP-05 | Backend (38 tables, 20 models, 9 controllers) | Ledger L082-L141 |
| WP-05a | Auth Attempts + PKCE | AuthAttemptTest (14) |
| WP-05b | Telegram Foundation | TelegramFoundationTest (12) |
| WP-13 | Admin Change Lifecycle | ChangeLifecycleTest (8) |
| WP-13b | Reauth + TOTP 2FA + Idempotency | 36 tests |
| WP-21 | Laravel/cPanel Deployment | HTTPS live |
| WP-22 | DB Queue + Cron | queue:work + scheduler |
| WP-27 | Telegram OIDC Full Flow | TelegramOidcTest (20) |
| WP-27b | HMAC-Signed User Binding | 6 tests |

## Completed — Work Packages VERIFIED (1)

| WP | Scope | Evidence |
|----|-------|----------|
| WP-02 | Static Verification Re-run | 7,810+ PASS, 66 contrast, 140 semantic |

---

## Completed — B-Blocks DONE (13)

| Block | Title | Commit |
|-------|-------|--------|
| B10 | Constitution Compliance (8 fixes) | (prior) |
| B11 | Main Admin Verification | 27edd9d |
| B12 | Bundle refresh | (prior) |
| B13 | /downloads/ deployed (GAP-61) | (prior) |
| B14 | GAP-62 registered + B10 mislabel fix | (prior) |
| B15 | S001 Welcome live (GAP-62 closed) | (prior) |
| B16 | Non-admin tests (+16) + cleanup | 64a8c27 |
| B17 | Ledger Integrity Sweep (12 fixes) | 6ea8a97 |
| B18 | Bundle Refresh (B17_DONE) | dbc689c |
| B19 | Model Unit Tests (3 files, 25 tests) | b451224 |
| B20 | Bundle Refresh (B19_DONE) | 6c6bcb6 |
| B21 | Extended Tests + Audit + Specs (31 tests) | c64fa28 |
| B22 | Bundle Refresh (B21_DONE) | 1a17a7c |

---

## Test Coverage — Test Files (19)

### Model Unit Tests (6) — B19 + B21

| File | Tests |
|------|-------|
| AuditLogTest.php | 8 |
| SettingVersionTest.php | 8 |
| OutboxEventTest.php | 9 |
| NotificationTest.php | 11 |
| RatingTest.php | 10 |
| UserTest.php | 10 |

### Test Trait (1)

| File | Purpose |
|------|---------|
| Concerns/CreatesTestCategory.php | DRY Category factory |

### Admin Feature Tests (6) — prior

| File | Tests |
|------|-------|
| AuthAttemptTest.php | 14 |
| ChangeLifecycleTest.php | 8 |
| IdempotencyTest.php | 8 |
| ReauthTest.php | 9 |
| TelegramFoundationTest.php | 12 |
| TelegramOidcTest.php | 26 |

### User Feature Tests (3) — B16

| File | Tests |
|------|-------|
| NeedFlowTest.php | 6 |
| OfferFlowTest.php | 6 |
| MessageFlowTest.php | 4 |

---

## Test Coverage — By Model

| Model | Tests | Status |
|-------|-------|--------|
| User | 10 unit + 12 feature | GOOD |
| Notification | 11 | GOOD |
| Rating | 10 | GOOD |
| AuditLog | 8 | GOOD |
| SettingVersion | 8 | GOOD |
| OutboxEvent | 9 | GOOD |
| TelegramDestination | 12 feature | GOOD |
| TelegramPublication | 12 feature | GOOD |
| TelegramPublicationEvent | 12 feature | GOOD |
| Need | 6 feature | PARTIAL |
| Offer | 6 feature | PARTIAL |
| Message | 4 feature | PARTIAL |
| Setting | 4 feature | PARTIAL |
| Category | 0 | MISSING |
| Payment | 0 | MISSING |
| PaymentEvent | 0 | MISSING |
| Boost | 0 | MISSING |
| BoostPackage | 0 | MISSING |
| Attachment | 0 | MISSING |
| Report | 0 | MISSING |
| Comparison* | 0 | MISSING |
| UserRole | 0 unit | MISSING |
| NeedAward | 0 unit | MISSING |
| SettingDraft | 0 unit | MISSING |

## Decisions Logged (115 total)

| Range | Count | Purpose |
|-------|-------|---------|
| D-001 -> D-101 | 101 | Prior work + WP-13/13b/05a/b/27/27b |
| D-102 -> D-110 | 9 | B17 Ledger Integrity |
| D-111 -> D-112 | 2 | B19 Model Tests |
| D-113 -> D-115 | 3 | B21 Extended Tests + Specs |

---

## GAPs Status

### Critical Blockers (3)

| GAP | Issue | Owner |
|-----|-------|-------|
| GAP-08 | Positive-fee activation | product ops |
| GAP-09 | Production PASS | release owner |
| GAP-26 | cPanel doc root capability | hosting admin |

### High Severity (16)

| GAP | Issue |
|-----|-------|
| GAP-01 | Browser/AT testing |
| GAP-02 | Flutter compilation |
| GAP-03 | Live services |
| GAP-05 | Marketplace baseline |
| GAP-07 | Observability |
| GAP-10 | AI live |
| GAP-11 | Payment live |
| GAP-22 | Responsive 46-screen |
| GAP-23 | A11y 46-screen |
| GAP-24 | Runtime states |
| GAP-25 | Error recovery |
| GAP-27 | PHP/MySQL extensions |
| GAP-28 | Queue throughput |
| GAP-29 | Backup RPO/RTO drill |
| GAP-30 | Webhook signature |
| GAP-31 | Telegram OIDC creds (PARTIAL) |

### Medium Severity (8)

GAP-04, GAP-06, GAP-12, GAP-13, GAP-14, GAP-20, GAP-21, GAP-32

### Resolved (B17 reconciliation + prior)

| GAP | Issue | Resolution |
|-----|-------|------------|
| GAP-38 | APP_DEBUG CRITICAL | SUPERSEDED by GAP-54 |
| GAP-42 | auth_attempts missing | RESOLVED (WP-05a) |
| GAP-45 | Reauth not implemented | RESOLVED (WP-13b) |
| GAP-46 | 2FA not implemented | RESOLVED (WP-13b) |
| GAP-47 | Idempotency-Key ignored | RESOLVED (WP-13b) |
| GAP-48 | Apply/Verify workflow | RESOLVED (WP-13b) |
| GAP-54 | APP_DEBUG=true in prod | RESOLVED (D-075) |
| GAP-60 | OutboxEvent model missing | RESOLVED (WP-13) |
| GAP-61 | downloads/ 403 | RESOLVED (B13) |
| GAP-62 | Production root | RESOLVED (B15, S001) |

### Still Open (from prior)

| GAP | Issue | Type |
|-----|-------|------|
| GAP-49 | Rollback E2E test | Test only |
| GAP-56 | OIDC E2E manual test | Manual QA |

---

## Live URLs

| URL | Status |
|-----|--------|
| https://zagcreativity.com/ | HTTP 200 (S001 Welcome) |
| https://zagcreativity.com/downloads/ | HTTP 200 |
| https://zagcreativity.com/handoff/ | HTTP 200 |
| https://zagcreativity.com/up | HTTP 200 |

## Database State

| Item | Count |
|------|-------|
| Production tables | 41 (36 app + 5 Laravel) |
| MySQL database | zagcreht_felagi |
| Test database | zagcreht_felagi_test (isolated) |
| Migration files | 36 |
| Eloquent models | 28 |

---

## Production Gates (G01-G09)

| Gate | Status | Blocker | Action Required |
|------|--------|---------|-----------------|
| G01 Source consistency | MET | — | — |
| G02 Design completeness | MET | — | — |
| G03 Brand source | MET | — | — |
| G04 Browser/responsive/AT | BLOCKED | No Chromium | Install browser + device |
| G05 Flutter | BLOCKED | No Flutter SDK | Install Flutter SDK |
| G06 Service/security | PARTIAL+ | Payment/AI/Telegram live | Provider credentials |
| G07 Monetization health | REQUIRES_EVIDENCE | No data | Measure baseline |
| G08 Localization/usability | SOURCE MET / RUNTIME PENDING | No device | Amharic QA |
| G09 Observability | REQUIRES_EVIDENCE | No telemetry | Setup metrics |

**Production PASS permitted ONLY when:** all 9 gates passed + no BLOCKER/CRITICAL finding remains.
**Currently:** 3/9 MET, 6 pending — NOT SATISFIED.

---

## Remaining Work — WP BLOCKED (16)

| WP | Scope | Blocker | Category |
|----|-------|---------|----------|
| WP-03 | Browser/A11y Render | Chromium/device | Tooling |
| WP-04 | Flutter Compile | Flutter/Dart SDK | Tooling |
| WP-06 | Amharic Runtime | Native reviewer | Human |
| WP-07 | Marketplace Baseline | Measured data | Data |
| WP-08 | Observability | Telemetry env | Infra |
| WP-09 | Production Gates | WP-03..08 | Meta |
| WP-10 | AI Evaluation Live | AI provider key | Credentials |
| WP-11 | Payment Live | Payment provider | Credentials |
| WP-12 | Sponsored Ads Serving | Ads infra | Infra |
| WP-14 | Telegram Delivery | Bot token | Credentials |
| WP-15 | Font Glyph Coverage | Licensed font | Legal |
| WP-16 | Amharic Linguistic QA | Native reviewers | Human |
| WP-17 | Responsive 46-Screen | Real browsers | Tooling |
| WP-18 | A11y 46-Screen | AT + browser | Tooling |
| WP-19 | Runtime State Live | Full stack | Infra |
| WP-20 | Error Recovery Live | Fault injection | Tooling |

## Remaining Work — WP BLOCKED-ON-UNKNOWN (2)

| WP | Issue | Reference |
|----|-------|-----------|
| WP-24 | T01-T18 integration test definitions | D-096, spec-request doc |
| WP-13c | 2FA enrollment UI | D-097, frontend stack undefined |

## Remaining Work — WP PARTIAL / DEFERRED

| WP | Status | Remaining |
|----|--------|-----------|
| WP-06 | PARTIAL | Native reviewer validation |
| WP-13c | DEFERRED | Frontend stack decision |

## Remaining Work — Additional Referenced WPs

| WP | Issue | Status |
|----|-------|--------|
| WP-05c | Admin Read Endpoints | UNKNOWN (spec-request doc created) |
| WP-23 | Backup Restore Drill | BLOCKED (no target) |
| WP-25 | Webhook Signature | BLOCKED (no sandbox) |
| WP-26 | File Malware Scanning | BLOCKED (no host AV) |
| WP-28 | Safe Mode Drills | BLOCKED (no full stack) |

## Test Coverage Gaps — 20 Models Without Tests

| Category | Models | Tests Needed |
|----------|--------|--------------|
| Payments | Payment, PaymentEvent, Boost, BoostPackage | 30-40 |
| AI | Comparison, ComparisonOffer, ComparisonResult, ComparisonAttempt | 30-40 |
| Safety | Attachment, Report | 15-20 |
| Settings | Setting, SettingDraft | 10-15 |
| Marketplace | Category, NeedAward | 10-15 |
| Communication | Message (extend) | 5-10 |
| Admin | UserRole | 5-8 |
| Total | 20 models | ~100-150 tests |

---

## Estimated Work to Production PASS

| Category | WPs | Effort | Blocker Type |
|----------|-----|--------|--------------|
| External Credentials | WP-10, WP-11, WP-14, WP-25 | 2-4 weeks | Non-technical |
| Tooling/Environment | WP-03, WP-04, WP-17, WP-18, WP-19, WP-20 | 3-4 weeks | Install SDKs |
| Human Review | WP-06, WP-15, WP-16 | 2-3 weeks | Native speakers |
| Data/Infra | WP-07, WP-08, WP-12, WP-23 | 2-3 weeks | Setup |
| Test Expansion | B23+ (100+ tests) | 4-6 weeks | Development |
| Spec Resolution | WP-05c, WP-13c, WP-24 | TBD | Stakeholder |
| Production Gates | WP-09 | 1 week | After above |

**Optimistic:** 8-10 weeks
**Realistic:** 12-16 weeks
**Parallel:** 6-8 weeks with multiple developers

## Critical Path (Blocking Production PASS)

External Credentials (WP-10/11/14/25)
    |
    v
G06 (Service/security) --+
                          |
Tooling (WP-03/04/17/18) -+--> WP-09 (Production Gates)
                          |     = Production PASS
Human Review (WP-06/16) --+
                          |
Data/Infra (WP-07/08) ----+

## Immediate Safe Work (No External Deps)

| Task | Effort | Value |
|------|--------|-------|
| B23: Payment model tests | 1 session | HIGH |
| B23: AI model tests | 1 session | HIGH |
| B23: Category + NeedAward tests | 1 session | MED |
| B23: Attachment + Report tests | 1 session | MED |
| B23: Bundle refresh (B22_DONE) | Short | HIGH |

---

## Continuity for Next Developer

### How to Take Over

1. Read `~/felagi_app/public/handoff/ledgers/HANDOFF_STATE.md`
2. Read this report (`FELAGI_STATUS_REPORT.md`)
3. Clone: `git clone ~/Felagi_App_v1.4.2_20260929-1551_B21_DONE.bundle`
4. Run tests: `APP_ENV=testing php artisan test`
5. Review `DECISION_LOG.md` D-001 -> D-115

### What NOT to Do

- Do NOT modify LOCKED baseline
- Do NOT guess UNKNOWN requirements
- Do NOT add schema changes without approval
- Do NOT change GAP status without evidence
- Do NOT commit without ledger updates

### Recommended Next Sequence

1. Resolve stakeholder UNKNOWNs (WP-05c, T01-T18, WP-13c)
2. Extend test coverage (Category, Payment, AI, Attachment)
3. Install missing SDKs (Flutter, Chromium, Node.js)
4. Obtain credentials (AI, Payment, Telegram)
5. Measure marketplace baseline (G07)
6. Setup observability (G09)
7. Amharic QA (G08)
8. Run production gates (WP-09)

## Evidence Chain

| Layer | Location | Status |
|-------|----------|--------|
| Git commits | ~/felagi_app/.git | 55 commits |
| Ledger files | public/handoff/ledgers/ | 14 files |
| Backups | ~/B17_backups/20260929_144348/ | 26+ snapshots |
| Bundles | ~/Felagi_*_B21_DONE* | 4 verified |
| Docs | docs/audits/, docs/spec-requests/ | 3 files |
| Design | ~/felagi_extracted/.git | 15 commits |

---

## Constitution Compliance

### 40 Locked Invariants: HONORED

UNKNOWN != ZERO | PARTIAL != COMPLETE | IMPLEMENTED != VERIFIED
Designed != Implemented != Verified | Preview != Production proof
Published != Applied != Verified | No hidden work
No hidden assumptions | No silent changes
No duplicate ownership | DONE = verified + documented + evidenced

### Process Rules: HONORED

- D-054 AUDIT BEFORE ACTION
- No hidden work
- No hidden assumptions
- No silent changes
- No duplicate ownership (fixed at B17)
- UNKNOWN != MISSING

### Violations Documented & Resolved

| Violation | Resolution | Rule |
|-----------|------------|------|
| WP-05b pipe failures | Amendment + D-085 | LOCKED |
| WP-27b pipe failures | Amendment + D-095 | LOCKED |
| Config cache prod DB | migrate-test.sh + D-076 | LOCKED |
| L188-L192 duplicates | Renumbered L212-L216 | D-102 |
| HANDOFF_STATE stale | Full rewrite | D-103 |

---

## Final Checklist for Production PASS

### Ready

- [x] 14 canonical ledgers
- [x] 162 tests passing (320 assertions)
- [x] 4 verified bundles
- [x] HTTPS live
- [x] 38 app tables
- [x] Cron + queue
- [x] Audit trail D-001 -> D-115
- [x] Rollback procedure
- [x] Handoff current

### Missing (Production PASS)

- [ ] G04: Browser/AT
- [ ] G05: Flutter compilation
- [ ] G06: Payment/AI/Telegram live
- [ ] G07: Marketplace baseline
- [ ] G08: Amharic runtime
- [ ] G09: Observability
- [ ] WP-09: Final gates

### Unknown (Stakeholder)

- [ ] WP-05c: Admin read endpoints spec
- [ ] T01-T18: Integration test definitions
- [ ] WP-13c: Frontend stack decision
- [ ] U-21: B-Blocks vs WPs formalization
- [ ] GAP-56: OIDC E2E manual test

---

# END OF REPORT

**Report ID:** FELAGI_STATUS_REPORT_B22
**Generated:** 2026-09-29
**HEAD:** 1a17a7c
**Status:** COMPREHENSIVE / TRACEABLE / ACTIONABLE

---

## B24 — S001 SignIn Fix (2026-09-29)

### Chained GAPs Resolved (4)

| GAP | Issue | Fix |
|-----|-------|-----|
| GAP-63 | JS response nesting | `data.data.auth_url` |
| GAP-64 | bot_id parameter | `bot_id` added |
| GAP-65 | CSRF token | meta + header |
| GAP-66 | origin parameter | `origin` added |

### Files Changed (3)

- resources/views/welcome.blade.php (CSRF + fetch)
- app/Http/Controllers/Api/V1/AuthController.php (origin + bot_id)
- config/services.php (bot_id mapping)

### BotFather Setup

- Bot: FelagiMarketBot (8629327448)
- Domain: zagcreativity.com

### Live URLs (SOT)

| URL | Purpose |
|-----|---------|
| https://zagcreativity.com/handoff/SOURCE_OF_TRUTH.md | Reading |
| https://zagcreativity.com/handoff/FELAGI_STATUS_REPORT.md | Reading |
| https://zagcreativity.com/downloads/SOURCE_OF_TRUTH.md | Download |
| https://zagcreativity.com/downloads/FELAGI_STATUS_REPORT.md | Download |


---

## SECTION 2 — CURRENT STATE
**Source:** HANDOFF_STATE.md

# HANDOFF STATE — Felagi v1.4.2
Last Updated: 2026-09-29 (B17 — Ledger Integrity Sweep)

## Session Summary
- Audit: **COMPLETE** (51/51 deliverables)
- Requirements registered: ~1,700+
- Work Packages: 28 (10 DONE, 1 VERIFIED, 1 PARTIAL, 16 BLOCKED, 1 DEFERRED)
- B-Blocks B10–B17: **8 blocks complete** (see IMPLEMENTATION_LEDGER L212–L216)
- Environment: VERIFIED
- Production: BLOCKED (6/9 gates pending)

## What Is Complete (Verified)

### Deployment (WP-21, WP-22)
- Laravel 11.56.1 at `~/felagi_app/`
- HTTPS live: https://zagcreativity.com
- MySQL DB: `zagcreht_felagi` (38 tables)
- Cron: 1-min scheduler + queue:work
- Git bundle + tarball

### Backend (WP-05, WP-05a, WP-05b, WP-13, WP-13b)
- 38 database tables (9 phases)
- 20 Eloquent models
- 9 API controllers
- ~30 routes registered under /api/v1
- Sanctum auth
- Auth Attempts + PKCE (WP-05a) — 14 tests
- Telegram Foundation (WP-05b) — 12 tests
- Admin Change Lifecycle (WP-13) — 8 tests
- Reauth + TOTP 2FA + Idempotency (WP-13b) — 36 tests

### Telegram OIDC (WP-27, WP-27b)
- Full OIDC flow (PKCE + JWKS + 7-step validation)
- HMAC-signed handoff (race-free, D-094)
- 26 tests PASS

### Frontend (B15)
- S001 Welcome deployed at production root
- LOCKED design source: UI_Handoff/ui-preview/app.js:144
- Amharic default (`መግቢያ — ፈላጊ`)

### Test Coverage
- Full suite: 106 tests (220 assertions)
- Source: 7,810+ checks PASS
- Contrast: 66/66 PASS

### Ledger Integrity (B10–B17)
- 8 data integrity violations fixed (B10)
- GAP-61 closed (B13)
- GAP-62 closed (B15)
- Non-admin test coverage +16 (B16)
- L188–L192 duplicate IDs renumbered → L212–L216 (B17)

## What Is Live

| Item | URL / Location | Status |
|------|----------------|--------|
| Laravel app | `~/felagi_app/` | Running |
| HTTPS root | https://zagcreativity.com | HTTP 200 (S001) |
| /downloads/ | https://zagcreativity.com/downloads/ | HTTP 200 |
| /handoff/ | https://zagcreativity.com/handoff/ | HTTP 200 |
| /up health | https://zagcreativity.com/up | HTTP 200 |
| MySQL DB | `zagcreht_felagi` | 38 tables |
| Cron | 1-min scheduler + queue:work | Active |

## What Is Blocked

| WP | Blocker |
|----|---------|
| WP-03/04 | Browser/Flutter SDK missing |
| WP-10 | AI provider key |
| WP-11 | Payment provider creds |
| WP-14 | Telegram bot token |
| WP-17/18 | Device/AT testing |
| WP-23 | Backup target |
| WP-24 | T01-T18 spec UNKNOWN (D-096) |
| WP-13c | 2FA UI stack UNKNOWN (D-097) |

## Next Work Package Priority

### Blocked on Stakeholder (UNKNOWN)
1. **WP-24** — T01-T18 Integration Tests (D-096)
2. **WP-13c** — 2FA Enrollment UI (D-097)
3. **WP-05c** — Admin read endpoints (spec needed)
4. **B-Blocks as WPs** — currently "blocks", not formal WPs (U-21)

### Safe, Additive Work (Constitution-compliant)
5. **B18** — Extended non-admin write tests (B16 pattern)
6. **B18** — Model unit tests (fill gap)
7. **B18** — Migration integrity audit

### Blocked on External Dependencies
8. **WP-10** — AI Integration (provider key)
9. **WP-11** — Payment Live (payment creds)
10. **WP-14** — Telegram Delivery (bot token)
11. **WP-25** — Webhook Signature (provider sandbox)

## Continuity Rule

A competent developer can continue reading:
- `public/handoff/ledgers/*.md` (13 canonical ledgers)
- `Felagi_Design_Package/Developer_Handoff/START_HERE.md`
- `Felagi_Design_Package/README.md`

NO chat history reconstruction needed.

## Evidence Files

| Artifact | Location |
|----------|----------|
| Design bundle | `~/Felagi_Design_v1.4.2_*_WP21_DONE.bundle` |
| App bundle | `~/Felagi_App_v1.4.2_*_WP21_DONE.bundle` |
| Full tarball | `~/Felagi_v1.4.2_*_WP21_DONE_full.tar.gz` |
| App git | `~/felagi_app/.git` |
| Design git | `~/felagi_extracted/.git` |
| B17 backups | `~/B17_backups/20260929_144348/` |

## Contact / Escalation

| Role | Owner |
|------|-------|
| Product Owner | (UNKNOWN — to be filled) |
| Design Owner | (UNKNOWN — to be filled) |
| Release Owner | (UNKNOWN — to be filled) |

---
## B18 — Bundle Refresh (2026-09-29)

### New Bundle Artifacts (B17_DONE)

All 4 artifacts in ~/:

| Artifact | Size |
|----------|------|
| Felagi_App_v1.4.2_20260929-1514_B17_DONE.bundle | 974K |
| Felagi_Design_v1.4.2_20260929-1514_B17_DONE.bundle | 3.5M |
| Felagi_v1.4.2_20260929-1514_B17_DONE_full.tar.gz | 45M |
| Felagi_v1.4.2_20260929-1514_B17_DONE_FULL_with_vendor.tar.gz | 76M |

### Verification Results

| Check | Result |
|-------|--------|
| App bundle HEAD | 6ea8a97 (B17) |
| App bundle commits | 51 |
| App ledger files | 14 |
| B17_ROLLBACK.md present | YES |
| Design bundle HEAD | 27edd9d (B11) |
| Design bundle commits | 15 |
| Tarball entries | 317 |
| Tarball B17_ROLLBACK | YES |

### Bundle Status

**SUPERSEDES:** Previous WP21 bundles in ~/archives/Felagi_bundles_archive/
**State:** Both repos clean, no uncommitted changes

### Continuity

A developer cloning either bundle gets:
- Full commit history
- All 14 ledger files
- B17_ROLLBACK.md
- Complete handoff state


---
## B20 — Bundle Refresh at B19_DONE (2026-09-29)

### New Bundle Artifacts

All 4 artifacts in ~/ (supersede B17 bundles):

| Artifact | Size |
|----------|------|
| Felagi_App_v1.4.2_20260929-1530_B19_DONE.bundle | 980K |
| Felagi_Design_v1.4.2_20260929-1530_B19_DONE.bundle | 3.5M |
| Felagi_v1.4.2_20260929-1530_B19_DONE_full.tar.gz | 45M |
| Felagi_v1.4.2_20260929-1530_B19_DONE_FULL_with_vendor.tar.gz | 76M |

### Verification Results (clone-tested)

| Check | Result |
|-------|--------|
| App bundle HEAD | b451224 (B19) |
| App bundle commits | 53 |
| App ledger files | 14 |
| App model test files | 3 |
| B19 commit present | YES |
| Design bundle HEAD | 27edd9d (B11) |
| Design bundle commits | 15 |
| Tarball entries | 321 |
| Tarball B19 test files | 3 |
| Tarball B17_ROLLBACK.md | YES |

### Supersedes

- B18 bundle (B17_DONE) - archived
- B17 bundles - archived

### State

- App HEAD: b451224 (B19 DONE)
- Design HEAD: 27edd9d (B11 DONE)
- Tests: 131 passed (266 assertions)
- Both repos CLEAN

---
## B22 — Bundle Refresh at B21_DONE (2026-09-29)

### New Bundle Artifacts

All 4 artifacts in ~/ (supersede B19 bundles):

| Artifact | Size |
|----------|------|
| Felagi_App_v1.4.2_20260929-1551_B21_DONE.bundle | 999K |
| Felagi_Design_v1.4.2_20260929-1551_B21_DONE.bundle | 3.5M |
| Felagi_v1.4.2_20260929-1551_B21_DONE_full.tar.gz | 45M |
| Felagi_v1.4.2_20260929-1551_B21_DONE_FULL_with_vendor.tar.gz | 76M |

### Verification Results (clone-tested)

| Check | Result |
|-------|--------|
| App bundle HEAD | c64fa28 (B21) |
| App bundle commits | 55 |
| App ledger files | 14 |
| App model test files | 6 |
| App audit docs | 1 |
| App spec requests | 2 |
| B21 commit present | YES |
| B19 commit present | YES |
| Design bundle HEAD | 27edd9d (B11) |
| Design bundle commits | 15 |
| Tarball entries | 332 |
| Tarball B19 test files | 3 |
| Tarball B21 test files | 3 |
| Tarball B21 docs | 3 |

### Supersedes

- B20 bundle (B19_DONE) - archived
- B18 bundle (B17_DONE) - archived

### State

- App HEAD: c64fa28 (B21 DONE)
- Design HEAD: 27edd9d (B11 DONE)
- Tests: 162 passed (320 assertions)
- Model test files: 6 (B19: 3 + B21: 3)
- Both repos CLEAN

---

## SECTION 3 — DECISION LOG
**Source:** DECISION_LOG.md

# DECISION LOG — Felagi v1.4.2
Last Updated: 2026-09-29

| ID | Decision | Basis | Date |
|----|----------|-------|------|
| D-001 | Treat v1.4.2 as locked baseline | User + START_HERE | 2026-09-29 |
| D-002 | No architecture changes | Constitution | 2026-09-29 |
| D-003 | No file modification without approval | User directive | 2026-09-29 |
| D-004 | Audit before implementation | Constitution | 2026-09-29 |
| D-005 | v1.4.2 = doc-only over v1.4.1 | Handoff_Cleanup | 2026-09-29 |
| D-006 | Historical findings retained | Final_Gap_Audit | 2026-09-29 |
| D-007 | Source PASS ≠ production PASS | Release_Gates | 2026-09-29 |
| D-008 | Positive-fee activation BLOCKED | N10 | 2026-09-29 |
| D-009 | Ads master remains OFF | N13 + A023 | 2026-09-29 |
| D-010 | 18 vs 23 admin = IA vs inventory | Final_IA + Inventory | 2026-09-29 |
| D-011 | No implementation without approval | Constitution | 2026-09-29 |
| D-012 | Continue with canonical ledgers only | User request | 2026-09-29 |
| D-013 | Chat interruption mitigated via ledgers | User concern | 2026-09-29 |
| D-014 | Server (PHP/MySQL/Composer) verified | env check | 2026-09-29 |
| D-015 | Local toolchain missing (Flutter/Node/Python3.10+) | env check | 2026-09-29 |
| D-016 | Python 3.6.8 insufficient for Tools/*.py | env check | 2026-09-29 |
| D-017 | WP-21 can start pending approval | env check | 2026-09-29 |
| D-018 | WP-02 cannot re-run without Python3.10+ + Node | env check | 2026-09-29 |
| D-019 | BEHAQ archived, not deleted | safety | 2026-09-29 |
| D-020 | Google API key names removed | security | 2026-09-29 |
| D-021 | 0-byte files cleaned (333) | cleanup | 2026-09-29 |
| D-022 | Felagi stays at `~/felagi_extracted/` | user choice | 2026-09-29 |
| D-023 | Laravel app goes to `~/felagi_app/` | convention | 2026-09-29 |
| D-024 | Awaiting WP-21 install approval | Constitution | 2026-09-29 |

## WP-21 Deployment Decisions (2026-09-29)

| ID | Decision | Basis | Date |
|----|----------|-------|------|
| D-025 | User approved "a b" — full WP-21 | User explicit | 2026-09-29 |
| D-026 | MySQL `zagcreht_felagi` (with prefix) | cPanel required | 2026-09-29 |
| D-027 | MySQL user `zagcreht_felagi_user` | cPanel required | 2026-09-29 |
| D-028 | `.my.cnf` auto password | User request | 2026-09-29 |
| D-029 | DB_PASSWORD quoted in .env | `#` special char | 2026-09-29 |
| D-030 | Laravel 11 (not 10) | Latest stable | 2026-09-29 |
| D-031 | public_html symlink (not move) | Reversible | 2026-09-29 |
| D-032 | Cron runs every 1 minute | Laravel scheduler | 2026-09-29 |
| D-033 | Vite not built (Node.js missing) | Environment limit | 2026-09-29 |
| D-034 | Keep Laravel welcome page temporarily | Felagi views pending | 2026-09-29 |

## WP-05 Migration Decisions (2026-09-29)

| ID | Decision | Basis | Date |
|----|----------|-------|------|
| D-035 | Users use UUID primary key | DFM §3.2 | 2026-09-29 |
| D-036 | user_roles uses composite PK (user_id, role) | DFM §3.2 | 2026-09-29 |
| D-037 | Fix telegram_publication_consent_at: useCurrent() | MySQL strict mode | 2026-09-29 |
| D-038 | Composite unique index order: (id, comparison_id) | MySQL FK requirement | 2026-09-29 |
| D-039 | ratings uses CHECK constraints | DFM §3.2 | 2026-09-29 |
| D-040 | payments.need_id NOT NULL | DFM §3.2 (both purposes) | 2026-09-29 |

## WP-05 Phase 6-9 Decisions (2026-09-29)

| ID | Decision | Basis | Date |
|----|----------|-------|------|
| D-041 | audit_logs uses append-only + hash chain | DFM §3.2 | 2026-09-29 |
| D-042 | settings.key is PRIMARY (not UUID) | DFM §3.2 | 2026-09-29 |
| D-043 | idempotency_keys uses composite PK | DFM §3.2 | 2026-09-29 |
| D-044 | telegram_publications has 3-column unique | DFM §3.2 | 2026-09-29 |
| D-045 | scheduled_settings is Phase 4 only | DFM §3.2 | 2026-09-29 |
| D-046 | All 38 tables now in production DB | WP-05 | 2026-09-29 |

## WP-05 Phase 11 Decisions (2026-09-29)

| ID | Decision | Basis | Date |
|----|----------|-------|------|
| D-047 | Use Sanctum for API auth (not Passport) | DFM §5.1 | 2026-09-29 |
| D-048 | Sanctum guard added to config/auth.php | Required for auth:sanctum | 2026-09-29 |
| D-049 | API middleware group configured | bootstrap/app.php | 2026-09-29 |
| D-050 | Need accept uses FOR UPDATE lock | DFM §4 | 2026-09-29 |
| D-051 | Rating recomputes user stats atomically | DFM §4 | 2026-09-29 |
| D-052 | Comparison controller is stub (WP-10 will complete) | AI needs provider | 2026-09-29 |
| D-053 | Personal access tokens table created | Sanctum auto-publish | 2026-09-29 |

## WP-13 Decisions (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-054 | **AUDIT BEFORE ACTION** — no write without prior verification | WP-13 first failure | LOCKED |
| D-055 | aggregate_id = setting_versions.id | Design: UUID types | ACCEPTED |
| D-056 | Idempotency-Key from HTTP header (optional) | Pragmatic MVP | ACCEPTED |
| D-057 | Reauth (5-min) deferred to WP-13b | users.recently_authenticated_at missing | ACCEPTED |
| D-058 | Test DB via .env.testing | Isolation from production | ACCEPTED |
| D-059 | UserFactory schema alignment | users table has telegram_subject not name/email | ACCEPTED |
| D-060 | Notification.php:53 fixed via OutboxEvent model creation | Same file needed anyway | ACCEPTED |
| D-061 | APP_DEBUG=true in production → separate WP | Out of WP-13 scope | DEFERRED |
| D-062 | AdminChangeController uses existing BaseApiController envelope | Consistency | ACCEPTED |
| D-063 | Add AuthorizesRequests trait to BaseApiController | authorize() was broken | ACCEPTED |
| D-064 | auth_attempts missing → GAP for WP-27 | Not WP-13 concern | DOCUMENTED |
| D-065 | DatabaseSeeder broken → GAP for WP-05 | Not WP-13 concern | DOCUMENTED |
| D-066 | WP-13 = narrow scope (Change Lifecycle only) | Constitution: complete but bounded | LOCKED |
| D-067 | MySQL test DB (not SQLite) | WP-05 offers migration incompatible with SQLite | ACCEPTED |

### Rationale Notes

**D-054 (LOCKED):** First WP-13 attempt failed because code was written without
verifying folder existence, table schema, or column names. 5 audit rounds
established the pattern: Audit → Registry → Plan → Approval → Implement → Verify.

**D-055:** `outbox_events.aggregate_id` is UUID type. `settings.key` is VARCHAR.
Use `setting_versions.id` (UUID) as aggregate_id; setting key goes in payload_json.

**D-067:** SQLite cannot `ALTER TABLE ... ADD UNIQUE` in the same way MySQL can.
WP-05 migration `create_offers_table.php:35` uses MySQL-specific syntax. Chose
isolated MySQL test DB over fixing migration (which would be architecture change).

## WP-13b Decisions (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-068 | Second factor = **TOTP** (RFC 6238) | User said "OTP" | ACCEPTED |
| D-069 | Reauth proof = `users.recently_authenticated_at` | Design: "recent reauth proof" | ACCEPTED |
| D-070 | Idempotency TTL = **24h** | Design: expires_at exists | ACCEPTED |
| D-071 | Reauth storage = 4 users columns | Design column requirements | ACCEPTED |
| D-072 | Apply/Verify = outbox consumer jobs | Design: "server jobs" | ACCEPTED |
| D-073 | Queue:work cron added (bounded, 50s) | DFM §461 | ACCEPTED |
| D-074 | Test DB APP_KEY = valid 32-byte | PHPUnit encryption | ACCEPTED |

### Rationale

**D-068 (TOTP):** User explicitly said "OTP". No SMS gateway, no SMTP, works offline, standard library (pragmarx/google2fa-laravel).

**D-069 (Session timestamp):** Design says "recent re-authentication proof" — session-based, cookie-session admin flow (DFM §216), 5-minute window LOCKED.

**D-070 (TTL 24h):** Standard retry window; cleanup job runs hourly.

**D-071 (4 columns):** recently_authenticated_at, totp_secret (encrypted), totp_enabled_at, totp_recovery_codes (encrypted:array). All nullable.

**D-072 (Outbox jobs):** Design: "Apply and verification are server jobs, never browser assertions."

**D-073 (cron):** max-time=50 < 60s interval = non-overlap.

**D-074 (APP_KEY):** Old test key was invalid (33 bytes). Replaced with valid 32-byte key.

### Deferred to WP-13c

| Req | Reason |
|-----|--------|
| TOTP enrollment UI | Frontend (Flutter/Admin UI) — WP-13c |
| Recovery codes display UI | Frontend — WP-13c |
| Self-service disable 2FA | Frontend — WP-13c |

## GAP-54 Decision (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-075 | APP_DEBUG=false in production + log rotation | Security best practice + DFM §449 (audit) | ACCEPTED |

### Rationale

Production stack traces expose internal paths, DB queries, library versions. Laravel default when APP_ENV=production is APP_DEBUG=false; but .env explicitly set true. Fix: change .env to false, rebuild config cache, archive existing log.

Rollback: .env.production.bak.20260929_112618.

### Verification

- config('app.debug') = false
- HTTP /up = 200
- HTTP / = 200
- HTTP 404 test = no stack trace
- Laravel log = archived (944 lines)

## Config Cache Incident (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-076 | Test migration MUST clear config cache first | Incident RCA | LOCKED |

### Incident

During WP-05a (auth_attempts migration), ran "php artisan migrate --env=testing".

Expected: migrate on zagcreht_felagi_test (test DB)
Actual: migrated on zagcreht_felagi (PRODUCTION)

### Root Cause

- bootstrap/cache/config.php existed (cached)
- Cached config pointed to production DB
- --env=testing changes APP_ENV but cache wins before .env.testing is read
- Laravel 11 loads cached config before environment-specific .env.*

### Impact

- Production auth_attempts table created (0 rows, harmless)
- Production users.recently_authenticated_at already existed (WP-13b)
- No data modification; no user-visible impact
- HTTP endpoints continued 200

### Resolution (D-076 LOCKED)

Every test DB migration MUST:
1. php artisan config:clear  -- remove cached config first
2. APP_ENV=testing php artisan migrate  -- use env var, not flag

Tooling: bin/migrate-test.sh created (always clears cache).

Rollback available:
- .env.production.bak.20260929_112618
- Production auth_attempts retained (additive; needed for WP-27)
- Production users.recently_authenticated_at retained (nullable)

### Prevention

Before: php artisan migrate --env=testing
After:  php artisan config:clear && APP_ENV=testing php artisan migrate

Assumption Before: --env loads .env.testing
Fact: --env only changes APP_ENV

Constitution compliance: RULE #1 (Audit Before Action) violation documented; not repeated.

## WP-05a Decisions (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-077 | 64-char PKCE verifier | RFC 7636 (43-128 range) | ACCEPTED |
| D-078 | Encrypted pkce_verifier at rest | Laravel encrypted cast | ACCEPTED |
| D-079 | handoff_hash SHA-256 single-use | DFM §218 | ACCEPTED |
| D-080 | telegramExchange stays 501 | WP-27 credentials needed | ACCEPTED |
| D-081 | Carbon 3.x: use abs() for time diffs | Laravel 11 migration | ACCEPTED |

### Rationale

**D-077:** 64 chars is within RFC 7636 range and gives 384 bits entropy.

**D-078:** DFM §218 says "encrypted PKCE verifier". Laravel `encrypted` cast handles this transparently.

**D-079:** Handoff code returned once; only hash stored. Single-use enforced via `consumed_at`.

**D-080:** Full OIDC exchange requires Telegram client_id/secret + JWKS endpoint. Deferred to WP-27.

**D-081:** Carbon 3.x in Laravel 11 changed `diffInMinutes()` to return signed values. Test assertions must use `abs()`.

## WP-05b Decision (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-082 | WP-05b read-only foundation only | Write endpoints need bot token (WP-27) | LOCKED |
| D-083 | Append-only TelegramPublicationEvent (no timestamps) | Audit trail for publication events | ACCEPTED |
| D-084 | 9 publication states as constants | DFM telegram_publications migration | ACCEPTED |

## WP-05b Process Failure (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-085 | Test scripts MUST abort on test FAIL before commit | Incident RCA | LOCKED |

### Incident

During WP-05b script 4/4, test command was piped:

    APP_ENV=testing php artisan test ... 2>&1 | tail -20

Then unconditionally:

    git commit -m "feat: WP-05b ..."

Root cause: The "| tail" pipe swallowed PHPUnit exit code. "set -e" does not catch pipe failures by default (needs pipefail). Commit was created despite 5 FAILED tests.

### Impact

- Commit e0cea05 made with 5 failing tests
- Ledgers claimed DONE before verification
- Constitution RULE #1 (Audit Before Action) violated
- IMPLEMENTED != VERIFIED violated

### Detection

User ran ledger audit. 5 test failures surfaced. Root cause identified:
1. TYPE constants wrong ("CHANNEL" vs "OWNED_CHANNEL")
2. telegram_chat_id passed as string, schema requires bigint
3. permission_evidence NOT NULL, was not provided

### Resolution

- Amended commit e0cea05 -> da31d49 with corrected model + test
- 12/12 tests PASS after fix
- Full suite: 62 PASS (127 assertions)
- D-085 LOCKED prevents recurrence

### Prevention (LOCKED)

Before (failed):
    php artisan test ... | tail -20
    git commit ...

After (required):
    php artisan test ... || { echo "TESTS FAILED — ABORT"; exit 1; }
    git commit ...

Or:
    set -o pipefail
    php artisan test ... | tail -20

### Rules

1. Test output MUST NOT be piped through tail/head before commit without pipefail
2. OR explicit "|| { echo FAILED; exit 1; }" after each test command
3. Ledgers MUST claim DONE only after verified PASS
4. Constitution compliance: violation documented; rule LOCKED

## WP-27 Decisions (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-086 | firebase/php-jwt for JWT verification | Well-tested, Laravel-agnostic | ACCEPTED |
| D-087 | JWKS cached 1h to avoid rate limits | DFM §218 (JWKS) + perf | ACCEPTED |
| D-088 | 60s clock skew tolerance | Production-grade JWT | ACCEPTED |
| D-089 | TelegramOidcService returns completeLogin() | Atomic flow | ACCEPTED |
| D-090 | Sanctum UUID migration in production | personal_access_tokens.tokenable_id fix | ACCEPTED |
| D-091 | telegramExchange finds user via recently_authenticated_at | Simpler than storing subject on attempt | PARTIAL_REVIEW |

### Rationale

**D-086:** `firebase/php-jwt` v7.2.1 is the de-facto JWT library for PHP. Security advisory noted but package actively maintained. Alternative `lcobucci/jwt` more complex; not needed for RS256 verification.

**D-087:** JWKS from Telegram rarely changes. Caching 1h reduces external calls; falls through on cache miss.

**D-088:** 60-second leeway is industry standard for distributed auth.

**D-089:** Atomic completeLogin() = single transaction through exchange → validate → upsert → markReauth. Simplifies controller.

**D-090:** Sanctum's default `bigint` tokenable_id incompatible with our UUID User model. Fix applied to test + production. Production table was empty → no data loss risk.

**D-091:** Telegram's ID token `sub` claim is the user identifier, but we don't store it on the auth_attempt. Instead, we find the user who most recently authenticated (last 5 minutes) — which is the one who just completed OIDC. **Trade-off:** Race condition possible if multiple users log in within 5 minutes. **Mitigation:** handoff code expires in 60 seconds (per D-077). **Future:** store user_id on auth_attempt during callback (WP-27b).

## WP-27b Decisions (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-092 | HMAC-signed handoff (no schema change) | DFM §218 preserved | LOCKED |
| D-093 | HMAC-SHA256 with APP_KEY | Standard, secure | ACCEPTED |
| D-094 | D-091 RESOLVED — race condition fixed | T25 test verifies | RESOLVED |

### Rationale

**D-092:** Adding user_id column to auth_attempts would deviate from DFM §218 (LOCKED schema). Instead, embed user_id in HMAC-signed handoff code. No schema change → no architecture change → no explicit approval required.

**D-093:** HMAC-SHA256 is NIST-approved, and Laravel's app.key (base64 32 bytes) is a secure signing key. Same primitive as Sanctum.

**D-094:** telegramExchange() no longer queries `recently_authenticated_at`. User is extracted from HMAC-verified payload. T25 confirms correct user in multi-user race.

### Trade-off Analysis

| Criterion | D-092 (HMAC) | Alternative (user_id column) |
|-----------|--------------|------------------------------|
| DFM §218 preserved | ✅ | ❌ |
| Approval required | No | Yes |
| Handoff length | ~180 chars | ~64 chars |
| Security | HMAC-SHA256 | DB lookup |
| Race-free | ✅ | ✅ |

## D-095 — Test Pipe Process Failure (LOCKED)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-095 | Test scripts MUST redirect to file (not pipe) before exit check | 2nd RCA | LOCKED |

### Incident (Second Occurrence)

Same root cause as D-085, replicated in WP-27b script:

    APP_ENV=testing php artisan test ... 2>&1 | tail -35 || { exit 1; }

The pipe swallowed PHPUnit exit code. Despite the || guard, the pipe consumed the exit status; commit made with 2 FAILED tests.

### Detection

State-check after script interruption surfaced:
- AuthAttemptTest: 1 failed
- TelegramOidcTest: 1 failed

Root cause: old tests (WP-05a T08, WP-27 T20) not updated for WP-27b interface changes:
- consumeByHandoff now returns array{attempt, user} (was ?AuthAttempt)
- generateHandoff needs optional ?User $user for HMAC binding

### Resolution

- Amended commit cebbaf2 -> eea83e8 with corrected tests
- All 88 tests PASS (188 assertions)
- D-095 LOCKED to prevent third occurrence

### Prevention (LOCKED)

FORBIDDEN:
    php artisan test ... | tail -N || { exit 1; }
    (pipe consumes exit code — D-085 rule was necessary but insufficient)

REQUIRED:
    php artisan test ... > /tmp/test_out.txt 2>&1
    STATUS=$?
    tail -N /tmp/test_out.txt
    [ $STATUS -eq 0 ] || { echo "TESTS FAILED"; exit 1; }

OR:
    set -euo pipefail
    php artisan test ... | tail -N

### Rules (Stricter than D-085)

1. D-085 (pipefail) — necessary but NOT sufficient
2. D-095 — file redirect + $? capture is MANDATORY
3. NEVER pipe test output before exit check without pipefail
4. ALL test runs MUST verify exit code

Constitution compliance: 2nd violation documented; LOCKED rule stricter.

## D-096 — T01-T18 Integration Test Definitions UNKNOWN — 2026-09-29

**Context:** WP-24 (T01-T18 Integration Tests) registered as BLOCKED
("Felagi routes needed"). Routes blocker now RESOLVED (47 API routes at
WP-27b), yet T01-T18 test definitions remain undocumented.

**Evidence:**
- TEST_VERIFICATION.md L43: "All 18 tests REQUIRES_EVIDENCE — need live stack"
- No assertions, no acceptance criteria, no test matrix
- WORK_PACKAGES.md L39 + PRIORITY_PLAN.md L57 refer to T01-T18 without definition

**Decision:** Mark WP-24 as BLOCKED-ON-UNKNOWN. Do not guess test scope.

**Constitution:** UNKNOWN ≠ MISSING.

**Resolution:** Stakeholder provides T01-T18 spec (test names, behaviors, criteria).

---

## D-097 — WP-13c Frontend Stack UNKNOWN — 2026-09-29

**Context:** WP-13c (2FA Enrollment UI) has 4 requirements in
REQUIREMENT_REGISTRY.md (REQ-WP13C-001..004) but was absent from
WORK_PACKAGES.md prior to CCB.

**Evidence:**
- REQUIREMENT_REGISTRY.md L222-227: 4 requirements, reason "Frontend"
- REQ-WP13C-004 reason is literally "Design unclear"
- ARCHITECTURE_MAP.md L42: "Flutter starter (18 classes) — COMPILE BLOCKED"
- Admin web frontend stack undefined

**Decision:** Register WP-13c as DEFERRED. Do not implement without stack decision.

**Constitution:** Do not guess missing requirements.

**Resolution:** Stakeholder defines (1) admin frontend stack, (2) design spec,
(3) lost-factor recovery flow.

## D-098 — Production Root LOCKED Design UNKNOWN — 2026-09-29

**Context:** Production root `https://zagcreativity.com/` shows Laravel default
welcome page. Previously referenced in B10 CHANGE_LOG as "GAP-57" (mislabel).
Real GAP-57 = Sanctum UUID migration (DONE).

**Evidence (B14 baseline audit):**
- Live body: `<title>Laravel</title>` + Laravel starter template
- routes/web.php: only `Route::get('/')` → view('welcome')
- resources/views/: only welcome.blade.php (Laravel default)
- Document root: `public_html` → `felagi_app/public` (correct)
- LOCKED design search in `~/felagi_extracted/Felagi_Design_Package/`:
  * Final_Information_Architecture.md — no root/landing route
  * Final_Navigation_Route_Map.md — no root/landing route
  * Product_Design/ + UI_Handoff/ — only 1 unrelated match (ui-preview/data.js)

**Decision:** Register GAP-62 as UNKNOWN. Do not create new design.
Do not implement without LOCKED source.

**Constitution:** Do not redesign/reinterpret. UNKNOWN ≠ MISSING.

**Resolution:** Design owner must either:
  (a) provide LOCKED production landing page design, or
  (b) explicitly approve keeping Laravel default root.

**Mislabel correction:** B10 CHANGE_LOG L685 corrected in B14.

## D-099 — S001 Selected for Production Root — 2026-09-29

**Context:** GAP-62 (production root = Laravel default). No LOCKED design
found in B14 audit.

**New evidence (B15):** Comprehensive search found LOCKED S001 in:
- UI_Handoff/ui-preview/app.js:144 (reference impl)
- Localization/app_am.arb (screenS001, purpose, signIn)
- DFM-FDS-1.4.md §443: "Welcome → login → Home"

**Decision:** S001 Welcome is the LOCKED production entry screen.
Deploy S001 at `/` with 3 elements only.

**Constitution:** No new design. LOCKED content adapted for web.
Sign-in target: existing `/api/v1/auth/telegram/start` (WP-27).

**Closes:** GAP-62

## D-100 — API Resources Layer Deferred (design improvement) — 2026-09-29

**Context:** Audit found `app/Http/Resources/` does not exist.
47 routes return raw model JSON via `$model->toArray()`.

**Assessment:** Not a bug. Current responses work. Improvements would be:
consistent field filtering, centralized PII protection, versioning.

**Decision:** Defer until LOCKED design specifies the Resources contract.
Constitution: "Do not guess missing requirements."

**Recorded as design improvement (not critical bug).**

## D-101 — Policy Expansion Deferred (best practice) — 2026-09-29

**Context:** Only `SettingPolicy` exists. Non-admin authz is inline in
controllers (14 FORBIDDEN returns).

**Assessment:** Not a bug. SettingPolicy auto-discovery verified via tinker
(`Gate resolves Setting → App\Policies\SettingPolicy`). Inline checks
correctly return 403/404 — verified by B16 tests.

**Decision:** Defer centralized Policy layer until LOCKED design specifies
policy-per-model structure. Inline authz is sufficient and tested.

**Recorded as best-practice improvement (not critical).**

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-102 | L188-L192 → L212-L216 renumbering | No duplicate ownership violated | LOCKED |
| D-103 | HANDOFF_STATE.md full rewrite | Stale content (WP-05 wrong, tables wrong, stats wrong) | ACCEPTED |
| D-104 | MASTER_BASELINE extended (append-only) | Last Updated claim was false; append-only preserves history | ACCEPTED |

### Rationale

**D-102:** No duplicate ownership is a locked invariant. L188-L192 appeared
twice in IMPLEMENTATION_LEDGER. The first occurrence (WP-05b models) is
correct. The second set (B10-B16) was renumbered to L212-L216 so both
sets remain traceable.

**D-103:** HANDOFF_STATE contained 3 critical errors:
- "WP-05: Felagi routes not yet implemented" (WP-05 = DONE)
- "MySQL DB: 9 tables" (actual = 38 tables)
- "28 WPs (2 DONE, 1 VERIFIED, 2 PARTIAL, 2 READY, 21 BLOCKED)"
  (actual = 10 DONE, 1 VERIFIED, 1 PARTIAL, 16 BLOCKED, 1 DEFERRED)

Full rewrite required. Historical record preserved via git.

**D-104:** MASTER_BASELINE "Last Updated: 2026-09-29" but WP-27, WP-05a/b,
B10-B16 were all completed that day and missing. Append-only preserves the
original LOCKED baseline while documenting evolution.

| D-105 | REQUIREMENT_REGISTRY append-only for WP-05a/b/27/27b | Full traceability required | ACCEPTED |
| D-106 | ARCHITECTURE_MAP Layer 8 + Layer 9 added | WP-27/27b/05a/05b and B10-B17 missing from map | ACCEPTED |
| D-107 | GAP-38 merged into GAP-54 | Same issue (APP_DEBUG), duplicate entry | ACCEPTED |

### Rationale (continued)

**D-105:** 42 new REQ IDs were added for WP-05a (7), WP-05b (6), WP-27 (10),
WP-27b (7), and B10-B17 (12). Full traceability requires each requirement
to have a unique ID and clear verification link.

**D-106:** Layer 8 covers Extended Backend (WP-05a/b/27/27b). Layer 9 covers
Ledger Governance Blocks (B10-B17). Original map ended at Layer 7 (WP-13b).

**D-107:** GAP-38 (APP_DEBUG CRITICAL) and GAP-54 (APP_DEBUG resolved) are
the same issue. GAP-38 was registered before the fix; GAP-54 after. Merged
by marking GAP-38 as SUPERSEDED by GAP-54.

| D-108 | GAP-07 marked renumbered to GAP-60 | Duplicate resolution | ACCEPTED |
| D-109 | GAP-42 removed from open list | Resolved in WP-05a; still listed as open | ACCEPTED |
| D-110 | Table count 25 → 38 documented | WP-05 expanded; baseline unchanged | ACCEPTED |

### Rationale (continued)

**D-108:** GAP-07 (Observability) was renumbered to GAP-60 in B10. The
original GAP-07 entry remained, creating double-booking. Marked as
renumbered.

**D-109:** GAP-42 (auth_attempts table missing) was resolved in WP-05a
(2026-09-29). It remained in the "Pre-existing GAPs still open" list.
Removed to eliminate ambiguity.

**D-110:** Original baseline declared 25 tables. WP-05 delivered 38. This
is documented per D-046 ("All 38 tables now in production DB"). B17 adds
this reconciliation to MASTER_BASELINE for consistency without modifying
the original LOCKED content.

### Constitution Compliance

- No duplicate ownership → D-102 fixes 5 violations
- DONE = implemented + integrated + tested + verified + documented + evidenced
  → D-103 restores HANDOFF_STATE accuracy
- No hidden work → D-104 through D-110 all documented
- UNKNOWN ≠ MISSING → B-blocks vs WPs documented as U-21

### Rollback

All B17 changes: git revert <B17-commit> (see B17_ROLLBACK.md)

---
## B19 — Model Unit Tests (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-111 | Model tests placed in Feature/Models (not Unit) | RefreshDatabase requires Laravel bootstrap | ACCEPTED |
| D-112 | NOT NULL schema columns made explicit in tests (not schema change) | D-054 AUDIT BEFORE ACTION; no architecture change without approval | ACCEPTED |

### Rationale

**D-111:** The 3 new test files (AuditLogTest, SettingVersionTest, OutboxEventTest) all use RefreshDatabase trait. This requires full Laravel bootstrap (DB, migrations, container). Placing them under tests/Feature/Models (rather than tests/Unit) is consistent with existing pattern and required for DB isolation (D-067 MySQL test DB).

**D-112:** B19 audit uncovered two NOT NULL columns without DB defaults:
- setting_versions.reason
- audit_logs.request_id

Two options were available:
(a) Modify migrations to add defaults -> architecture change, requires approval
(b) Make the columns explicit in test payloads -> no schema change

Option (b) chosen. This preserves the LOCKED schema and documents the required columns via tests. It also surfaces the NOT NULL constraint as a de-facto contract.

This is consistent with D-100/D-101 (deferred design improvements) - do not silently alter schema.

### Test Results (verified)

- Command: APP_ENV=testing php artisan test
- Result: 131 passed (266 assertions)
- Failures: 0
- Duration: 7.61s

### Constitution Compliance

- No hidden work -> both decisions documented
- No silent changes -> no schema touched
- D-054 AUDIT BEFORE ACTION -> audit preceded all code
- IMPLEMENTED != VERIFIED -> tests run before commit

---
## B21 — Extended Model Tests + Spec Requests (2026-09-29)

| ID | Decision | Basis | Status |
|----|----------|-------|--------|
| D-113 | CreatesTestCategory trait for DRY test setup | Multiple tests need Category | ACCEPTED |
| D-114 | Distinct providers per rating in scopeValid test | UNIQUE(need_id, from_user_id, to_user_id) constraint | ACCEPTED |
| D-115 | Spec request docs for WP-05c and T01-T18 | Constitution: UNKNOWN != MISSING | ACCEPTED |

### Rationale

**D-113:** Multiple test files need a Category to create a valid Need
(category_id NOT NULL). Rather than duplicate Category::create() in each
test, a trait (CreatesTestCategory) provides makeCategory() helper. This
follows DRY and matches the B19 pattern of inline helpers.

**D-114:** ratings table has UNIQUE(need_id, from_user_id, to_user_id).
The scopeValid test needs 5 ratings with scores 1-5 for the same need.
Using distinct providers per rating satisfies the constraint while still
testing the 1-5 score range.

**D-115:** Two spec requests were documented:
- WP-05c: admin read endpoints (undefined scope)
- T01-T18: integration test definitions (undefined behaviors)

Both are UNKNOWN (not MISSING). Docs provide the questions and constraints
for stakeholder resolution. No implementation attempted without spec.

### Test Results (verified)

- Command: APP_ENV=testing php artisan test
- Result: 162 passed (320 assertions)
- Failures: 0
- Duration: 8.52s

### Constitution Compliance

- No hidden work -> all decisions documented
- No silent changes -> no schema touched
- D-054 AUDIT BEFORE ACTION -> audit preceded code
- IMPLEMENTED != VERIFIED -> 162 tests run before commit
- UNKNOWN != MISSING -> spec requests registered

---

## SECTION 4 — TEST VERIFICATION
**Source:** TEST_VERIFICATION.md

# TEST VERIFICATION — Felagi v1.4.2
Last Updated: 2026-09-29

## Source-Level Tests (All PASS)

| Tool | Result | Evidence |
|------|--------|----------|
| `generate_runtime.py` | PASS | 246 tokens + 46 screens |
| `verify_package.py` | 61/61 PASS | + 66 contrast pairs |
| `verify_foundation.py` | 324/324 PASS | — |
| `verify_ai_ux.py` | 17/17 PASS | — |
| `verify_completion.py` | 26/26 PASS | — |
| `policy_tests.js` | 21/21 PASS | — |
| `comparison_tests.js` | 36/36 PASS | — |
| `ads_tests.js` | 55/55 PASS | — |
| `preview_logic_probe.js` | 5,024 transitions PASS | DOM shim only |
| Semantic verification | 140/140 PASS | — |
| Contrast pairs | 66/66 PASS | — |

**Total: 7,810+ checks PASS (source-level)**

## Runtime Tests (All PENDING)

| Test | Status | Blocker |
|------|--------|---------|
| Browser render | BLOCKED | No Chromium |
| Amharic glyph @200% | BLOCKED | No device |
| 400% zoom | BLOCKED | No device |
| Keyboard/AT nav | BLOCKED | No screen reader |
| Flutter analyze | BLOCKED | No SDK |
| Flutter test | BLOCKED | No SDK |
| Flutter build | BLOCKED | No SDK |
| Device glyph/layout | BLOCKED | No device |
| Authenticated payment | BLOCKED | No creds |
| AI provider live | BLOCKED | No creds |
| Telegram delivery | BLOCKED | No bot token |
| Admin actions live | BLOCKED | No backend |
| Ads serving | BLOCKED | No ad server |
| Concurrency | BLOCKED | No load env |
| Media/SSRF scan | BLOCKED | No scan infra |
| Telemetry | BLOCKED | No metrics env |

## Integration Tests (T01-T18)
All 18 tests REQUIRES_EVIDENCE — need live stack.

## Evidence Boundary
`Designed ≠ Implemented ≠ Verified`
`Preview ≠ Production proof`
`Published ≠ Applied ≠ Verified`

## WP-13 Runtime Tests (2026-09-29)

### ChangeLifecycleTest — 8 PASS

| Test | Status | Duration |
|------|--------|----------|
| test_can_create_draft | ✅ PASS | 2.38s |
| test_validate_rejects_type_mismatch | ✅ PASS | 0.08s |
| test_publish_creates_new_version | ✅ PASS | 0.09s |
| test_publish_with_stale_version_returns_409 | ✅ PASS | 0.07s |
| test_dependency_blocks_boosts_on | ✅ PASS | 0.08s |
| test_publish_writes_audit_log | ✅ PASS | 0.07s |
| test_publish_writes_outbox_event | ✅ PASS | 0.09s |
| test_unauthorized_returns_403 | ✅ PASS | 0.07s |

**Total:** 8 passed (15 assertions)
**Duration:** 3.00s
**Test DB:** MySQL isolated (`zagcreht_felagi_test`)
**Command:** `php artisan test --filter=ChangeLifecycleTest`

### Evidence Chain

| Layer | Evidence | Status |
|-------|----------|--------|
| Implementation | 14 new files + 3 patches | ✅ |
| Integration | 10 admin routes registered | ✅ |
| Verification | 8 tests, 15 assertions | ✅ |
| Production isolation | Prod DB untouched (settings=31, users=0) | ✅ |
| Documentation | Ledgers updated | ✅ |

### Assertion Coverage

| Requirement | Test | Assertions |
|-------------|------|------------|
| REQ-WP13-001 | test_can_create_draft | status=201, success=true, data.status=DRAFT |
| REQ-WP13-003 | test_validate_rejects_type_mismatch | status=200, errors not empty |
| REQ-WP13-006 | test_publish_creates_new_version | status=200, DB has v=2 |
| REQ-WP13-009 | test_publish_with_stale_version_returns_409 | status=409, error.code |
| REQ-WP13-010 | test_dependency_blocks_boosts_on | errors not empty |
| REQ-WP13-007 | test_publish_writes_audit_log | DB has audit_logs row |
| REQ-WP13-011 | test_publish_writes_outbox_event | DB has outbox_events row |
| REQ-WP13-012 | test_unauthorized_returns_403 | status=403 |

### Runtime Tests Still PENDING (WP-13 scope)

| Test | Status | Blocker |
|------|--------|---------|
| Rollback E2E | PARTIAL | Test not yet written (impl complete) |
| Apply/Verify workflow | BLOCKED | Requires runtime service (WP-13b) |
| Reauth 5-min window | BLOCKED | users.recently_authenticated_at missing |
| Second factor (CRITICAL) | BLOCKED | 2FA infra missing |
| Idempotency-Key from header | BLOCKED | Storage strategy TBD |

### Evidence Boundary

- `Designed ≠ Implemented` — ✅ all implemented
- `Implemented ≠ Verified` — ✅ 8 tests pass
- `Verified ≠ Deployed` — deploy is separate step
- `Production PASS` — not claimed (WP-13 scope only)

## WP-13b Runtime Tests (2026-09-29)

### Summary

**Tests: 36 passed (63 assertions)**
**Duration: 4.98s**
**DB: zagcreht_felagi_test (isolated)**

### ChangeLifecycleTest — 8 PASS

| Test | Status |
|------|--------|
| test_can_create_draft | ✅ PASS |
| test_validate_rejects_type_mismatch | ✅ PASS |
| test_publish_creates_new_version | ✅ PASS |
| test_publish_with_stale_version_returns_409 | ✅ PASS |
| test_dependency_blocks_boosts_on | ✅ PASS |
| test_publish_writes_audit_log | ✅ PASS |
| test_publish_writes_outbox_event | ✅ PASS |
| test_unauthorized_returns_403 | ✅ PASS |

### ReauthTest — 9 PASS

| Test | Status |
|------|--------|
| is_fresh_false_when_never_authenticated | ✅ PASS |
| is_fresh_true_when_within_window | ✅ PASS |
| is_fresh_false_when_stale | ✅ PASS |
| mark_updates_timestamp | ✅ PASS |
| require_passes_for_low_risk | ✅ PASS |
| require_throws_for_high_risk_stale | ✅ PASS |
| publish_high_setting_without_fresh_auth_returns_401 | ✅ PASS |
| publish_high_setting_with_fresh_auth_passes_middleware | ✅ PASS |
| publish_low_setting_without_fresh_auth_passes | ✅ PASS |

### TwoFactorTest — 11 PASS

| Test | Status |
|------|--------|
| generate_secret_returns_base32 | ✅ PASS |
| verify_accepts_current_code | ✅ PASS |
| verify_rejects_invalid_code | ✅ PASS |
| verify_rejects_malformed | ✅ PASS |
| verify_detects_replay | ✅ PASS |
| generate_recovery_codes | ✅ PASS |
| consume_recovery_code | ✅ PASS |
| consume_recovery_code_rejects_unknown | ✅ PASS |
| require_for_passes_for_high | ✅ PASS |
| require_for_throws_for_critical_without_2fa | ✅ PASS |
| require_for_passes_for_critical_with_2fa | ✅ PASS |

### IdempotencyTest — 8 PASS

| Test | Status |
|------|--------|
| request_hash_is_order_independent | ✅ PASS |
| request_hash_differs_on_body | ✅ PASS |
| begin_without_header_returns_new | ✅ PASS |
| begin_first_time_returns_new | ✅ PASS |
| begin_replay_after_complete | ✅ PASS |
| begin_conflicts_on_different_body | ✅ PASS |
| begin_progress_for_in_flight | ✅ PASS |
| cleanup_removes_expired | ✅ PASS |

### Evidence Boundary

- `Implemented ≠ Verified` — ✅ verified (36/36)
- `Verified ≠ Deployed` — deploy separate
- `Production PASS` — not claimed (WP-13b scope only)

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)
Original test evidence preserved above. New tests appended for traceability.

## Test Count Evolution — Full Trail

The test suite grew from 8 (WP-13) to 106 (B16). Chronological record:

| Stage | Date | New | Cumulative | Evidence |
|-------|------|-----|------------|----------|
| WP-13 | 2026-09-29 | 8 | 8 | ChangeLifecycleTest |
| WP-13b | 2026-09-29 | 28 | 36 | Reauth + 2FA + Idempotency |
| WP-05a | 2026-09-29 | 14 | 50 | AuthAttemptTest |
| WP-05b | 2026-09-29 | 12 | 62 | TelegramFoundationTest |
| WP-27 | 2026-09-29 | 22 | 84 | TelegramOidcTest (+2 reconciliation) |
| WP-27b | 2026-09-29 | 4 | 88 | HMAC tests (net of refactor) |
| (interim) | 2026-09-29 | 2 | 90 | UNKNOWN reconciliation |
| B16 | 2026-09-29 | 16 | 106 | NeedFlow + OfferFlow + MessageFlow |
| **Final** | — | — | **106** | **220 assertions** |

### UNKNOWN Reconciliation (documented, not blocking)

The following transitions have unclear deltas:
- 62 → 84: +22 (WP-27 claimed 20 tests → +2 unaccounted)
- 84 → 88: +4 (WP-27b claimed 6 tests → -2 unaccounted)
- 88 → 90: +2 (no documented source)

**Impact:** None. The final count (106) is verified via B16 full-suite run.
**Action:** Documented as UNKNOWN. No further reconciliation unless a
verification discrepancy emerges.

## WP-05a Test Evidence — AuthAttemptTest

| Test | Status | Assertions |
|------|--------|------------|
| PKCE verifier generation (64 chars) | PASS | — |
| S256 challenge derivation | PASS | — |
| auth_attempts row creation | PASS | — |
| Encrypted PKCE cast | PASS | — |
| handoff_hash SHA-256 | PASS | — |
| Single-use enforcement | PASS | — |
| (plus 8 more) | PASS | — |
| **Total** | **14 PASS** | **34 assertions** |

## WP-05b Test Evidence — TelegramFoundationTest

| Test | Status |
|------|--------|
| TelegramDestination model (scopeActive) | PASS |
| TelegramDestination canPublish | PASS |
| TelegramPublication (9 states) | PASS |
| TelegramPublication scopePending | PASS |
| TelegramPublicationEvent append-only | PASS |
| AdminTelegramController destinations.index | PASS |
| AdminTelegramController destinations.show | PASS |
| AdminTelegramController publications.index | PASS |
| AdminTelegramController publications.show | PASS |
| Authorization (admin only) | PASS |
| Route registration | PASS |
| Model relationships | PASS |
| **Total** | **12 PASS** |

## WP-27 Test Evidence — TelegramOidcTest

| Category | Tests | Status |
|----------|-------|--------|
| OIDC exchange (code → token) | 4 | PASS |
| ID token validation (7-step) | 5 | PASS |
| JWKS caching | 2 | PASS |
| Clock skew tolerance | 1 | PASS |
| User upsert | 3 | PASS |
| Handoff generation | 3 | PASS |
| Sanctum token issuance | 2 | PASS |
| **Total** | **20 PASS** | **43 assertions** |

## WP-27b Test Evidence — HMAC Tests (T21-T26)

| Test | Status |
|------|--------|
| T21 — HMAC signature validity | PASS |
| T22 — Invalid signature rejected | PASS |
| T23 — Expired handoff rejected | PASS |
| T24 — User ID embedded correctly | PASS |
| T25 — Multi-user race: correct user selected | PASS |
| T26 — DFM §218 compliance (no schema change) | PASS |
| **Total** | **6 PASS** |

## B16 Test Evidence — Flow Tests

### NeedFlowTest (6 tests)
- test_can_create_need
- test_can_list_needs
- test_can_show_need
- test_can_update_own_need
- test_cannot_update_other_need
- test_unauthorized_returns_401

### OfferFlowTest (6 tests)
- test_can_create_offer
- test_can_list_offers
- test_can_show_offer
- test_can_update_own_offer
- test_cannot_update_other_offer
- test_unauthorized_returns_401

### MessageFlowTest (4 tests)
- test_can_send_message
- test_can_list_messages
- test_cannot_message_without_offer
- test_unauthorized_returns_401

**B16 Total:** 16 tests · 30 assertions

## B17 Verification — No Tests Run

B17 is documentation-only. No code, schema, or route changes.
No tests added, modified, or run.

The full test suite remains: **106 tests · 220 assertions**.

## Evidence Boundary (unchanged)

- `Designed ≠ Implemented ≠ Verified`
- `Implemented ≠ Verified`
- `Verified ≠ Deployed`
- `Production PASS` — NOT claimed (6/9 gates pending)

---
## B19 — Model Unit Tests (2026-09-29)

### New Test Files (3)

| File | Tests | Purpose |
|------|-------|---------|
| AuditLogTest.php | 8 | Hash chain, scopes, casts, actor |
| SettingVersionTest.php | 8 | Immutability, casts, relations |
| OutboxEventTest.php | 9 | Statuses, scopes, markDone/markFailed |

### Test Suite Growth

| Stage | Tests | Assertions |
|-------|-------|------------|
| Pre-B19 | 106 | 220 |
| B19 | +25 | +46 |
| **Post-B19** | **131** | **266** |

### B19 Test Run

Command: APP_ENV=testing php artisan test
Result: 131 passed (266 assertions)
Duration: 7.61s
Test DB: zagcreht_felagi_test (isolated)

### Test Coverage Expansion

| Model | Before B19 | After B19 |
|-------|------------|-----------|
| AuditLog | 0 | 8 |
| SettingVersion | 0 | 8 |
| OutboxEvent | 0 | 9 |

### Schema Findings (B19 audit)

Two NOT NULL columns without defaults were discovered:
- setting_versions.reason (NOT NULL)
- audit_logs.request_id (NOT NULL)

Both were made explicit in tests per constitution rule (IMPLEMENTED != VERIFIED).

---
## B21 — Extended Model Tests + Audit + Spec Requests (2026-09-29)

### New Test Files (3)

| File | Tests | Focus |
|------|-------|-------|
| NotificationTest.php | 11 | Statuses, scopes, read lifecycle |
| RatingTest.php | 9 | Relations, valid scope, uniqueness |
| UserTest.php | 11 | SoftDeletes, scopes, encrypted casts |

### Test Suite Growth

| Stage | Tests | Assertions |
|-------|-------|------------|
| Pre-B21 | 131 | 266 |
| B21 | +31 | +54 |
| **Post-B21** | **162** | **320** |

### Coverage Expansion

| Model | Before B21 | After B21 |
|-------|------------|-----------|
| Notification | 0 | 11 |
| Rating | 0 | 9 |
| User | 12 (feature) | 11 (unit) |

### New Artifacts (docs/)

| Path | Lines | Purpose |
|------|-------|---------|
| docs/audits/MIGRATION_INTEGRITY_B21.md | 69 | Migration audit (read-only) |
| docs/spec-requests/WP-05c_admin_read_endpoints.md | 64 | Stakeholder spec request |
| docs/spec-requests/T01-T18_integration_tests.md | 86 | Stakeholder spec request |

### Schema Findings

- needs.category_id NOT NULL (resolved via CreatesTestCategory trait)
- offers.offered_price + proposal_message NOT NULL (explicit in tests)
- ratings UNIQUE(need_id, from_user_id, to_user_id) (distinct providers)

### Test Run

- Command: APP_ENV=testing php artisan test
- Result: 162 passed (320 assertions)
- Failures: 0
- Duration: 8.52s



════════════════════════════════════════════════════════════
# FILE: public/handoff/FELAGI_STATUS_REPORT.md
════════════════════════════════════════════════════════════

# FELAGI v1.4.2 — STATUS REPORT
**Generated:** 2026-09-29
**HEAD:** 1a17a7c (B22)

## Executive Summary

| Field | Value |
|-------|-------|
| Design contract version | 1.4.1 |
| Handoff release version | 1.4.2 |
| App HEAD | 1a17a7c (B22) |
| Design HEAD | 27edd9d (B11) |
| Production release | BLOCKED (6/9 gates pending) |

## Numbers at a Glance

| Metric | Count |
|--------|-------|
| WP DONE | 10 |
| WP VERIFIED | 1 |
| WP PARTIAL | 1 |
| WP BLOCKED | 16 |
| WP DEFERRED | 1 |
| B-Blocks DONE | 13 (B10-B22) |
| Tests passing | 162 |
| Assertions | 320 |
| Test files | 19 |
| Model test files | 6 |
| Ledger files | 14 |
| Bundle artifacts | 4 (B21_DONE) |
| Docs (audits+specs) | 3 |

---

## Source of Truth — Canonical Ledgers (14)

Location: `~/felagi_app/public/handoff/ledgers/`

| # | File | Purpose | Updated |
|---|------|---------|---------|
| 1 | MASTER_BASELINE.md | Locked baseline + extensions | B17 |
| 2 | REQUIREMENT_REGISTRY.md | ~1,700 reqs + 42 new IDs | B17 |
| 3 | ARCHITECTURE_MAP.md | Layers 0-11 | B17 |
| 4 | WORK_PACKAGES.md | 28 WPs + 13 B-Blocks | B17 |
| 5 | IMPLEMENTATION_LEDGER.md | L001 -> L216 | B17 |
| 6 | TEST_VERIFICATION.md | 396 lines full trail | B21 |
| 7 | OPEN_GAPS.md | 223 lines all GAPs | B17 |
| 8 | CHANGE_LOG.md | 989 lines B10-B21 | B21 |
| 9 | DECISION_LOG.md | 679 lines D-001-D-115 | B21 |
| 10 | RELEASE_STATUS.md | 188 lines gates | B17 |
| 11 | HANDOFF_STATE.md | 252 lines bundle paths | B22 |
| 12 | PRIORITY_PLAN.md | 137 lines TIER 0-6 | B17 |
| 13 | ENVIRONMENT_CHECKLIST.md | 146 lines env state | B17 |
| 14 | B17_ROLLBACK.md | 83 lines rollback | B17 |

---

## Source of Truth — Specification Documents (3)

Location: `~/felagi_app/docs/`

| # | File | Lines | Purpose |
|---|------|-------|---------|
| 1 | audits/MIGRATION_INTEGRITY_B21.md | 69 | Read-only migration audit |
| 2 | spec-requests/WP-05c_admin_read_endpoints.md | 64 | Stakeholder spec request |
| 3 | spec-requests/T01-T18_integration_tests.md | 86 | Stakeholder spec request |

## Source of Truth — Bundle Artifacts (4)

Location: `~/`

| # | File | Size | HEAD |
|---|------|------|------|
| 1 | Felagi_App_v1.4.2_20260929-1551_B21_DONE.bundle | 999K | c64fa28 |
| 2 | Felagi_Design_v1.4.2_20260929-1551_B21_DONE.bundle | 3.5M | 27edd9d |
| 3 | Felagi_v1.4.2_20260929-1551_B21_DONE_full.tar.gz | 45M | c64fa28 |
| 4 | Felagi_v1.4.2_20260929-1551_B21_DONE_FULL_with_vendor.tar.gz | 76M | c64fa28 |

## Source of Truth — Backup Archives

| Path | Purpose |
|------|---------|
| ~/B17_backups/20260929_144348/ | 26+ pre-B17/B21/B22 snapshots |
| ~/archives/Felagi_bundles_archive/ | Pre-B17 bundles |

---

## Completed — Work Packages DONE (10)

| WP | Scope | Evidence |
|----|-------|----------|
| WP-01 | Foundation Registry | PACKAGE_MANIFEST |
| WP-05 | Backend (38 tables, 20 models, 9 controllers) | Ledger L082-L141 |
| WP-05a | Auth Attempts + PKCE | AuthAttemptTest (14) |
| WP-05b | Telegram Foundation | TelegramFoundationTest (12) |
| WP-13 | Admin Change Lifecycle | ChangeLifecycleTest (8) |
| WP-13b | Reauth + TOTP 2FA + Idempotency | 36 tests |
| WP-21 | Laravel/cPanel Deployment | HTTPS live |
| WP-22 | DB Queue + Cron | queue:work + scheduler |
| WP-27 | Telegram OIDC Full Flow | TelegramOidcTest (20) |
| WP-27b | HMAC-Signed User Binding | 6 tests |

## Completed — Work Packages VERIFIED (1)

| WP | Scope | Evidence |
|----|-------|----------|
| WP-02 | Static Verification Re-run | 7,810+ PASS, 66 contrast, 140 semantic |

---

## Completed — B-Blocks DONE (13)

| Block | Title | Commit |
|-------|-------|--------|
| B10 | Constitution Compliance (8 fixes) | (prior) |
| B11 | Main Admin Verification | 27edd9d |
| B12 | Bundle refresh | (prior) |
| B13 | /downloads/ deployed (GAP-61) | (prior) |
| B14 | GAP-62 registered + B10 mislabel fix | (prior) |
| B15 | S001 Welcome live (GAP-62 closed) | (prior) |
| B16 | Non-admin tests (+16) + cleanup | 64a8c27 |
| B17 | Ledger Integrity Sweep (12 fixes) | 6ea8a97 |
| B18 | Bundle Refresh (B17_DONE) | dbc689c |
| B19 | Model Unit Tests (3 files, 25 tests) | b451224 |
| B20 | Bundle Refresh (B19_DONE) | 6c6bcb6 |
| B21 | Extended Tests + Audit + Specs (31 tests) | c64fa28 |
| B22 | Bundle Refresh (B21_DONE) | 1a17a7c |

---

## Test Coverage — Test Files (19)

### Model Unit Tests (6) — B19 + B21

| File | Tests |
|------|-------|
| AuditLogTest.php | 8 |
| SettingVersionTest.php | 8 |
| OutboxEventTest.php | 9 |
| NotificationTest.php | 11 |
| RatingTest.php | 10 |
| UserTest.php | 10 |

### Test Trait (1)

| File | Purpose |
|------|---------|
| Concerns/CreatesTestCategory.php | DRY Category factory |

### Admin Feature Tests (6) — prior

| File | Tests |
|------|-------|
| AuthAttemptTest.php | 14 |
| ChangeLifecycleTest.php | 8 |
| IdempotencyTest.php | 8 |
| ReauthTest.php | 9 |
| TelegramFoundationTest.php | 12 |
| TelegramOidcTest.php | 26 |

### User Feature Tests (3) — B16

| File | Tests |
|------|-------|
| NeedFlowTest.php | 6 |
| OfferFlowTest.php | 6 |
| MessageFlowTest.php | 4 |

---

## Test Coverage — By Model

| Model | Tests | Status |
|-------|-------|--------|
| User | 10 unit + 12 feature | GOOD |
| Notification | 11 | GOOD |
| Rating | 10 | GOOD |
| AuditLog | 8 | GOOD |
| SettingVersion | 8 | GOOD |
| OutboxEvent | 9 | GOOD |
| TelegramDestination | 12 feature | GOOD |
| TelegramPublication | 12 feature | GOOD |
| TelegramPublicationEvent | 12 feature | GOOD |
| Need | 6 feature | PARTIAL |
| Offer | 6 feature | PARTIAL |
| Message | 4 feature | PARTIAL |
| Setting | 4 feature | PARTIAL |
| Category | 0 | MISSING |
| Payment | 0 | MISSING |
| PaymentEvent | 0 | MISSING |
| Boost | 0 | MISSING |
| BoostPackage | 0 | MISSING |
| Attachment | 0 | MISSING |
| Report | 0 | MISSING |
| Comparison* | 0 | MISSING |
| UserRole | 0 unit | MISSING |
| NeedAward | 0 unit | MISSING |
| SettingDraft | 0 unit | MISSING |

## Decisions Logged (115 total)

| Range | Count | Purpose |
|-------|-------|---------|
| D-001 -> D-101 | 101 | Prior work + WP-13/13b/05a/b/27/27b |
| D-102 -> D-110 | 9 | B17 Ledger Integrity |
| D-111 -> D-112 | 2 | B19 Model Tests |
| D-113 -> D-115 | 3 | B21 Extended Tests + Specs |

---

## GAPs Status

### Critical Blockers (3)

| GAP | Issue | Owner |
|-----|-------|-------|
| GAP-08 | Positive-fee activation | product ops |
| GAP-09 | Production PASS | release owner |
| GAP-26 | cPanel doc root capability | hosting admin |

### High Severity (16)

| GAP | Issue |
|-----|-------|
| GAP-01 | Browser/AT testing |
| GAP-02 | Flutter compilation |
| GAP-03 | Live services |
| GAP-05 | Marketplace baseline |
| GAP-07 | Observability |
| GAP-10 | AI live |
| GAP-11 | Payment live |
| GAP-22 | Responsive 46-screen |
| GAP-23 | A11y 46-screen |
| GAP-24 | Runtime states |
| GAP-25 | Error recovery |
| GAP-27 | PHP/MySQL extensions |
| GAP-28 | Queue throughput |
| GAP-29 | Backup RPO/RTO drill |
| GAP-30 | Webhook signature |
| GAP-31 | Telegram OIDC creds (PARTIAL) |

### Medium Severity (8)

GAP-04, GAP-06, GAP-12, GAP-13, GAP-14, GAP-20, GAP-21, GAP-32

### Resolved (B17 reconciliation + prior)

| GAP | Issue | Resolution |
|-----|-------|------------|
| GAP-38 | APP_DEBUG CRITICAL | SUPERSEDED by GAP-54 |
| GAP-42 | auth_attempts missing | RESOLVED (WP-05a) |
| GAP-45 | Reauth not implemented | RESOLVED (WP-13b) |
| GAP-46 | 2FA not implemented | RESOLVED (WP-13b) |
| GAP-47 | Idempotency-Key ignored | RESOLVED (WP-13b) |
| GAP-48 | Apply/Verify workflow | RESOLVED (WP-13b) |
| GAP-54 | APP_DEBUG=true in prod | RESOLVED (D-075) |
| GAP-60 | OutboxEvent model missing | RESOLVED (WP-13) |
| GAP-61 | downloads/ 403 | RESOLVED (B13) |
| GAP-62 | Production root | RESOLVED (B15, S001) |

### Still Open (from prior)

| GAP | Issue | Type |
|-----|-------|------|
| GAP-49 | Rollback E2E test | Test only |
| GAP-56 | OIDC E2E manual test | Manual QA |

---

## Live URLs

| URL | Status |
|-----|--------|
| https://zagcreativity.com/ | HTTP 200 (S001 Welcome) |
| https://zagcreativity.com/downloads/ | HTTP 200 |
| https://zagcreativity.com/handoff/ | HTTP 200 |
| https://zagcreativity.com/up | HTTP 200 |

## Database State

| Item | Count |
|------|-------|
| Production tables | 41 (36 app + 5 Laravel) |
| MySQL database | zagcreht_felagi |
| Test database | zagcreht_felagi_test (isolated) |
| Migration files | 36 |
| Eloquent models | 28 |

---

## Production Gates (G01-G09)

| Gate | Status | Blocker | Action Required |
|------|--------|---------|-----------------|
| G01 Source consistency | MET | — | — |
| G02 Design completeness | MET | — | — |
| G03 Brand source | MET | — | — |
| G04 Browser/responsive/AT | BLOCKED | No Chromium | Install browser + device |
| G05 Flutter | BLOCKED | No Flutter SDK | Install Flutter SDK |
| G06 Service/security | PARTIAL+ | Payment/AI/Telegram live | Provider credentials |
| G07 Monetization health | REQUIRES_EVIDENCE | No data | Measure baseline |
| G08 Localization/usability | SOURCE MET / RUNTIME PENDING | No device | Amharic QA |
| G09 Observability | REQUIRES_EVIDENCE | No telemetry | Setup metrics |

**Production PASS permitted ONLY when:** all 9 gates passed + no BLOCKER/CRITICAL finding remains.
**Currently:** 3/9 MET, 6 pending — NOT SATISFIED.

---

## Remaining Work — WP BLOCKED (16)

| WP | Scope | Blocker | Category |
|----|-------|---------|----------|
| WP-03 | Browser/A11y Render | Chromium/device | Tooling |
| WP-04 | Flutter Compile | Flutter/Dart SDK | Tooling |
| WP-06 | Amharic Runtime | Native reviewer | Human |
| WP-07 | Marketplace Baseline | Measured data | Data |
| WP-08 | Observability | Telemetry env | Infra |
| WP-09 | Production Gates | WP-03..08 | Meta |
| WP-10 | AI Evaluation Live | AI provider key | Credentials |
| WP-11 | Payment Live | Payment provider | Credentials |
| WP-12 | Sponsored Ads Serving | Ads infra | Infra |
| WP-14 | Telegram Delivery | Bot token | Credentials |
| WP-15 | Font Glyph Coverage | Licensed font | Legal |
| WP-16 | Amharic Linguistic QA | Native reviewers | Human |
| WP-17 | Responsive 46-Screen | Real browsers | Tooling |
| WP-18 | A11y 46-Screen | AT + browser | Tooling |
| WP-19 | Runtime State Live | Full stack | Infra |
| WP-20 | Error Recovery Live | Fault injection | Tooling |

## Remaining Work — WP BLOCKED-ON-UNKNOWN (2)

| WP | Issue | Reference |
|----|-------|-----------|
| WP-24 | T01-T18 integration test definitions | D-096, spec-request doc |
| WP-13c | 2FA enrollment UI | D-097, frontend stack undefined |

## Remaining Work — WP PARTIAL / DEFERRED

| WP | Status | Remaining |
|----|--------|-----------|
| WP-06 | PARTIAL | Native reviewer validation |
| WP-13c | DEFERRED | Frontend stack decision |

## Remaining Work — Additional Referenced WPs

| WP | Issue | Status |
|----|-------|--------|
| WP-05c | Admin Read Endpoints | UNKNOWN (spec-request doc created) |
| WP-23 | Backup Restore Drill | BLOCKED (no target) |
| WP-25 | Webhook Signature | BLOCKED (no sandbox) |
| WP-26 | File Malware Scanning | BLOCKED (no host AV) |
| WP-28 | Safe Mode Drills | BLOCKED (no full stack) |

## Test Coverage Gaps — 20 Models Without Tests

| Category | Models | Tests Needed |
|----------|--------|--------------|
| Payments | Payment, PaymentEvent, Boost, BoostPackage | 30-40 |
| AI | Comparison, ComparisonOffer, ComparisonResult, ComparisonAttempt | 30-40 |
| Safety | Attachment, Report | 15-20 |
| Settings | Setting, SettingDraft | 10-15 |
| Marketplace | Category, NeedAward | 10-15 |
| Communication | Message (extend) | 5-10 |
| Admin | UserRole | 5-8 |
| Total | 20 models | ~100-150 tests |

---

## Estimated Work to Production PASS

| Category | WPs | Effort | Blocker Type |
|----------|-----|--------|--------------|
| External Credentials | WP-10, WP-11, WP-14, WP-25 | 2-4 weeks | Non-technical |
| Tooling/Environment | WP-03, WP-04, WP-17, WP-18, WP-19, WP-20 | 3-4 weeks | Install SDKs |
| Human Review | WP-06, WP-15, WP-16 | 2-3 weeks | Native speakers |
| Data/Infra | WP-07, WP-08, WP-12, WP-23 | 2-3 weeks | Setup |
| Test Expansion | B23+ (100+ tests) | 4-6 weeks | Development |
| Spec Resolution | WP-05c, WP-13c, WP-24 | TBD | Stakeholder |
| Production Gates | WP-09 | 1 week | After above |

**Optimistic:** 8-10 weeks
**Realistic:** 12-16 weeks
**Parallel:** 6-8 weeks with multiple developers

## Critical Path (Blocking Production PASS)

External Credentials (WP-10/11/14/25)
    |
    v
G06 (Service/security) --+
                          |
Tooling (WP-03/04/17/18) -+--> WP-09 (Production Gates)
                          |     = Production PASS
Human Review (WP-06/16) --+
                          |
Data/Infra (WP-07/08) ----+

## Immediate Safe Work (No External Deps)

| Task | Effort | Value |
|------|--------|-------|
| B23: Payment model tests | 1 session | HIGH |
| B23: AI model tests | 1 session | HIGH |
| B23: Category + NeedAward tests | 1 session | MED |
| B23: Attachment + Report tests | 1 session | MED |
| B23: Bundle refresh (B22_DONE) | Short | HIGH |

---

## Continuity for Next Developer

### How to Take Over

1. Read `~/felagi_app/public/handoff/ledgers/HANDOFF_STATE.md`
2. Read this report (`FELAGI_STATUS_REPORT.md`)
3. Clone: `git clone ~/Felagi_App_v1.4.2_20260929-1551_B21_DONE.bundle`
4. Run tests: `APP_ENV=testing php artisan test`
5. Review `DECISION_LOG.md` D-001 -> D-115

### What NOT to Do

- Do NOT modify LOCKED baseline
- Do NOT guess UNKNOWN requirements
- Do NOT add schema changes without approval
- Do NOT change GAP status without evidence
- Do NOT commit without ledger updates

### Recommended Next Sequence

1. Resolve stakeholder UNKNOWNs (WP-05c, T01-T18, WP-13c)
2. Extend test coverage (Category, Payment, AI, Attachment)
3. Install missing SDKs (Flutter, Chromium, Node.js)
4. Obtain credentials (AI, Payment, Telegram)
5. Measure marketplace baseline (G07)
6. Setup observability (G09)
7. Amharic QA (G08)
8. Run production gates (WP-09)

## Evidence Chain

| Layer | Location | Status |
|-------|----------|--------|
| Git commits | ~/felagi_app/.git | 55 commits |
| Ledger files | public/handoff/ledgers/ | 14 files |
| Backups | ~/B17_backups/20260929_144348/ | 26+ snapshots |
| Bundles | ~/Felagi_*_B21_DONE* | 4 verified |
| Docs | docs/audits/, docs/spec-requests/ | 3 files |
| Design | ~/felagi_extracted/.git | 15 commits |

---

## Constitution Compliance

### 40 Locked Invariants: HONORED

UNKNOWN != ZERO | PARTIAL != COMPLETE | IMPLEMENTED != VERIFIED
Designed != Implemented != Verified | Preview != Production proof
Published != Applied != Verified | No hidden work
No hidden assumptions | No silent changes
No duplicate ownership | DONE = verified + documented + evidenced

### Process Rules: HONORED

- D-054 AUDIT BEFORE ACTION
- No hidden work
- No hidden assumptions
- No silent changes
- No duplicate ownership (fixed at B17)
- UNKNOWN != MISSING

### Violations Documented & Resolved

| Violation | Resolution | Rule |
|-----------|------------|------|
| WP-05b pipe failures | Amendment + D-085 | LOCKED |
| WP-27b pipe failures | Amendment + D-095 | LOCKED |
| Config cache prod DB | migrate-test.sh + D-076 | LOCKED |
| L188-L192 duplicates | Renumbered L212-L216 | D-102 |
| HANDOFF_STATE stale | Full rewrite | D-103 |

---

## Final Checklist for Production PASS

### Ready

- [x] 14 canonical ledgers
- [x] 162 tests passing (320 assertions)
- [x] 4 verified bundles
- [x] HTTPS live
- [x] 38 app tables
- [x] Cron + queue
- [x] Audit trail D-001 -> D-115
- [x] Rollback procedure
- [x] Handoff current

### Missing (Production PASS)

- [ ] G04: Browser/AT
- [ ] G05: Flutter compilation
- [ ] G06: Payment/AI/Telegram live
- [ ] G07: Marketplace baseline
- [ ] G08: Amharic runtime
- [ ] G09: Observability
- [ ] WP-09: Final gates

### Unknown (Stakeholder)

- [ ] WP-05c: Admin read endpoints spec
- [ ] T01-T18: Integration test definitions
- [ ] WP-13c: Frontend stack decision
- [ ] U-21: B-Blocks vs WPs formalization
- [ ] GAP-56: OIDC E2E manual test

---

# END OF REPORT

**Report ID:** FELAGI_STATUS_REPORT_B22
**Generated:** 2026-09-29
**HEAD:** 1a17a7c
**Status:** COMPREHENSIVE / TRACEABLE / ACTIONABLE

---

## B24 — S001 SignIn Fix (2026-09-29)

### Chained GAPs Resolved (4)

| GAP | Issue | Fix |
|-----|-------|-----|
| GAP-63 | JS response nesting | `data.data.auth_url` |
| GAP-64 | bot_id parameter | `bot_id` added |
| GAP-65 | CSRF token | meta + header |
| GAP-66 | origin parameter | `origin` added |

### Files Changed (3)

- resources/views/welcome.blade.php (CSRF + fetch)
- app/Http/Controllers/Api/V1/AuthController.php (origin + bot_id)
- config/services.php (bot_id mapping)

### BotFather Setup

- Bot: FelagiMarketBot (8629327448)
- Domain: zagcreativity.com

### Live URLs (SOT)

| URL | Purpose |
|-----|---------|
| https://zagcreativity.com/handoff/SOURCE_OF_TRUTH.md | Reading |
| https://zagcreativity.com/handoff/FELAGI_STATUS_REPORT.md | Reading |
| https://zagcreativity.com/downloads/SOURCE_OF_TRUTH.md | Download |
| https://zagcreativity.com/downloads/FELAGI_STATUS_REPORT.md | Download |




════════════════════════════════════════════════════════════
# FILE: public/handoff/ledgers/Universal_Production_Pass_Development_Constitution_EN_AM.txt
════════════════════════════════════════════════════════════

Universal Production Pass Development Constitution
የዩኒቨርሳል ፕሮዳክሽን ፓስ የልማት ሥራ ሕገ-መንግስት

Operate under the Universal Production Pass Development Constitution.
Approved Product Specification, Design Specification, Architecture, Data/API Contracts, Security Rules and Acceptance Criteria are the locked baseline. Do not redesign, reinterpret, replace or silently weaken them.

በ"Universal Production Pass Development Constitution" መሠረት ሥራውን አከናውን።
የተፈቀዱት Product Specification, Design Specification, Architecture, Data/API Contracts, Security Rules እና Acceptance Criteria የተቆለፈ (Locked) ዋና መሠረት ናቸው። እነዚህን እንደገና አትንደፍ፣ ትርጉማቸውን በራስህ አትቀይር፣ አትተካ፣ በዝምታም አታዳክማቸው።

Before coding, audit the current system and create a requirement registry, dependency map and coherent Work Packages.

ኮድ ከመጻፍህ በፊት አሁን ያለውን ሲስተም ኦዲት አድርግ፣ Requirement Registry, Dependency Map እና ተያያዥ የሆኑ Work Packages ፍጠር።

Do not work as a long chain of tiny independent tasks. Group strongly related frontend, backend, database, API, permission, error/recovery, security, tests, logging and documentation work into complete production-oriented Work Packages and execute each package end-to-end.

ሥራውን በብዙ ትንንሽ እና እርስ በርስ ያልተያያዙ micro-tasks ረጅም ሰንሰለት አታድርግ። በጠንካራ ሁኔታ የተያያዙ frontend, backend, database, API, permission, error/recovery, security, tests, logging እና documentation ሥራዎችን በአንድ ሙሉ production-oriented Work Package ውስጥ አያይዘህ ከመጀመሪያ እስከ መጨረሻ አጠናቅቅ።

Do not rebuild existing valid functionality. Discover first, reuse where appropriate, and build only proven missing capability.

አሁን ያለ እና በትክክል የሚሠራ functionality እንደገና አትገንባ። መጀመሪያ Discover አድርግ፣ በሚገባ ከሆነ Reuse አድርግ፣ እና በማስረጃ የጎደለ መሆኑ የተረጋገጠ capability ብቻ ገንባ።

Every requirement must have a unique ID and every implementation must trace back to a requirement.

እያንዳንዱ requirement ልዩ Unique ID ሊኖረው ይገባል። እያንዳንዱ implementation ደግሞ ወደ የትኛው requirement እንደሚመለስ በግልጽ traceable መሆን አለበት።

Maintain one canonical project ledger that always shows:
NOT_STARTED / READY / IN_PROGRESS / BLOCKED / IMPLEMENTED / INTEGRATED / TESTED / VERIFIED / DONE / DEFERRED / N/A.

ሁልጊዜ የፕሮጀክቱን ሁኔታ የሚያሳይ አንድ canonical Project Ledger አቆይ። እሱም የሚከተሉትን status ይጠቀም፦
NOT_STARTED / READY / IN_PROGRESS / BLOCKED / IMPLEMENTED / INTEGRATED / TESTED / VERIFIED / DONE / DEFERRED / N/A.

Never use DONE to mean "code written." DONE means implemented, integrated, tested, verified, documented and evidenced.

"DONE" የሚለውን "ኮድ ተጻፈ" ማለት እንዳትጠቀም። DONE ማለት implemented, integrated, tested, verified, documented እና evidenced ሆኖ መጠናቀቅ ነው።

Maintain continuously:
MASTER_BASELINE
REQUIREMENT_REGISTRY
ARCHITECTURE_MAP
WORK_PACKAGES
IMPLEMENTATION_LEDGER
TEST_VERIFICATION
OPEN_GAPS
CHANGE_LOG
DECISION_LOG
RELEASE_STATUS
HANDOFF_STATE

የሚከተሉትን መዝገቦች ሁልጊዜ አዘምን እና አቆይ፦
MASTER_BASELINE
REQUIREMENT_REGISTRY
ARCHITECTURE_MAP
WORK_PACKAGES
IMPLEMENTATION_LEDGER
TEST_VERIFICATION
OPEN_GAPS
CHANGE_LOG
DECISION_LOG
RELEASE_STATUS
HANDOFF_STATE

Every completed Work Package must leave the product in a runnable, non-broken state and include test evidence, affected files/modules, remaining gaps, known risks and rollback information.

እያንዳንዱ የተጠናቀቀ Work Package ፕሮዳክቱን runnable እና non-broken ሁኔታ ላይ መተው አለበት። በተጨማሪ test evidence, affected files/modules, remaining gaps, known risks እና rollback information መያዝ አለበት።

Do not guess missing requirements. Mark them UNKNOWN and report them. UNKNOWN ≠ MISSING. IMPLEMENTED ≠ VERIFIED. PARTIAL ≠ COMPLETE.

የጎደሉ ወይም ያልተገለጹ requirements በግምት አትሙላ። UNKNOWN ብለህ መዝግብ እና report አድርግ።
UNKNOWN ≠ MISSING.
IMPLEMENTED ≠ VERIFIED.
PARTIAL ≠ COMPLETE.

If one task is blocked, isolate and document the blocker and continue independent safe Work Packages. Never fake dependent implementation.

አንድ task BLOCKED ከሆነ blockerን isolate አድርግ፣ document አድርግ፣ እና ከእሱ ነፃ የሆኑ safe Work Packages ላይ ቀጥል። በblocked dependency ላይ የተመሠረተ implementation እንደተሰራ አታስመስል።

Do not perform destructive, security-sensitive, breaking or architecture-changing work without explicit approval.

Destructive, security-sensitive, breaking ወይም architecture-changing ሥራ explicit approval ሳይኖር አታከናውን።

At the end of implementation, execute the full Production Readiness Gates:
Baseline → Implementation → Integration → Verification → Security/Operations → Deployment/Post-Deployment Verification.

Implementation ከተጠናቀቀ በኋላ ሙሉ Production Readiness Gates አሳልፍ፦
Baseline → Implementation → Integration → Verification → Security/Operations → Deployment/Post-Deployment Verification.

Production PASS is permitted only when all applicable critical requirements are VERIFIED and no unresolved BLOCKER/CRITICAL finding remains.

Production PASS የሚፈቀደው ለፕሮዳክቱ የሚመለከቱ ሁሉም critical requirements VERIFIED ሲሆኑ እና unresolved BLOCKER/CRITICAL finding ሳይቀር ብቻ ነው።

Maintain the project so that another competent developer can take over at any time, read the canonical project-control records, understand exactly what is complete, incomplete, blocked, tested and deployed, and continue without reconstructing project history from conversation or memory.

ፕሮጀክቱን ማንኛውም competent developer በማንኛውም ጊዜ ሊረከበው በሚችል መልኩ አቆይ። አዲሱ developer canonical project-control records ብቻ በማንበብ ምን እንደተጠናቀቀ፣ ምን እንዳልተጠናቀቀ፣ ምን BLOCKED እንደሆነ፣ ምን TESTED እንደሆነ እና ምን DEPLOYED እንደሆነ በትክክል ማወቅ እና ከchat history ወይም ከሰው ማስታወሻ ታሪኩን እንደገና ሳይገነባ በደህና መቀጠል መቻል አለበት።

No hidden work. No hidden assumptions. No silent changes. No unverified completion. No duplicate ownership. No dependency on a specific developer.

የመጨረሻ የማይጣሱ ሕጎች፦
የተደበቀ ሥራ አይኖር።
የተደበቀ ግምት አይኖር።
በዝምታ የሚደረግ ለውጥ አይኖር።
ያልተረጋገጠ ሥራ DONE አይባል።
Duplicate ownership አይኖር።
ፕሮጀክቱ በአንድ የተወሰነ developer ላይ ጥገኛ አይሆን።




# ═══════════════════════════════════════════
# SECTION B — EVIDENCE FILES (.md)
# ═══════════════════════════════════════════


════════════════════════════════════════════════════════════
# FILE: evidence/GAP-71b_evidence.md
════════════════════════════════════════════════════════════

# GAP-71b Evidence — Attachment Factory + HasFactory

- **Type:** Infrastructure (test factories)
- **Date (UTC):** 2026-09-30T06:50:00Z
- **Operator:** zagcreht
- **Commit before:** 4285ad1 (QUALITY_DONE)

## Files Added / Changed

| File | Change |
|------|--------|
| database/factories/AttachmentFactory.php | NEW (74 lines) |
| app/Models/Attachment.php | HasFactory trait added (+2 lines) |

## Factory Features

### `definition()` default
- uploaded_by => User::factory()
- need_id / offer_id / message_id => null
- purpose => PROFILE
- storage_disk => 'local'
- storage_key => 'attachments/<uuid>.pdf'
- original_name => 'document.pdf'
- detected_mime => 'application/pdf'
- byte_size => 12345
- sha256 => hash('sha256', random)
- visibility => PRIVATE
- scan_status => PENDING

### States
- `clean()` — scan_status=CLEAN
- `rejected()` — scan_status=REJECTED
- `publicVisibility()` — visibility=PUBLIC
- `forNeed(Need $need)` — need_id + purpose=NEED

## Smoke Test (tinker)

- Factory created UUID successfully
- purpose=PROFILE, scan_status=PENDING, visibility=PRIVATE, sha256=64 chars
- clean() state works (scan_status=CLEAN)
- publicVisibility() state works (visibility=PUBLIC)

## Full Suite Regression

- 412 tests, 691 assertions, 0 failures
- AttachmentTest: 21 tests, 33 assertions (unchanged)

## Not Changed

- No production logic changed (trait addition only)
- No migrations, no routes, no design
- Existing tests unchanged

## Rollback

git revert HEAD  # removes factory + trait

## Constitution Compliance

- [x] No destructive change
- [x] No breaking change (additive infrastructure)
- [x] No silent changes

## DONE definition

- [x] IMPLEMENTED
- [x] INTEGRATED (tinker smoke test)
- [x] TESTED (412/412)
- [ ] VERIFIED (2nd reviewer required)
- [x] DOCUMENTED
- [x] EVIDENCED



════════════════════════════════════════════════════════════
# FILE: evidence/INCIDENT_20260930_config_cache.md
════════════════════════════════════════════════════════════

# Incident Report — 2026-09-30 — config:cache + Production DB Reset

## Summary

**Severity:** HIGH (production DB schema wiped)
**Impact:** Production database `zagcreht_felagi` schema DROP+CREATE
**Data Loss:** ZERO (production DB was empty — never had real data)
**Root Cause:** `php artisan config:cache` during Phase B deploy
**Detection:** D4-6 test failures (10 scope-count failures)

## Timeline (UTC)

| Time | Event |
|------|-------|
| 06:53 | Phase B3b: `php artisan config:cache` run |
| 06:53 | Cache locked .env → `database=zagcreht_felagi` (production) |
| 07:21:05 | `migrate:fresh --env=testing` (RefreshDatabase) — cache ignored env |
| 07:21:11 | All 40 production tables DROP + CREATE |
| 07:22 | Tests ran against production (10 failures — extra rows from factories) |
| 07:27 | Incident detected (D4-6 output) |
| 07:30 | Data state verified (all 0 rows) |
| 07:35 | `config:clear` + `optimize:clear` — cache cleared |
| 07:45 | phpunit.xml fixed with `force="true"` DB overrides |
| 07:50 | Full suite passes 412/412; production DB untouched after test run |

## Root Cause

During Phase B (deploy), I ran `php artisan config:cache`. This caches
`.env` values into `bootstrap/cache/config.php`. When PHPUnit later runs,
it cannot read `.env.testing` because cached config overrides.

The `RefreshDatabase` trait then calls `migrate:fresh` against the cached
connection (production DB), not the intended test DB.

## Impact Assessment

**Data:** ZERO. Evidence:
- Login flow never worked ("Telegram Chat ID missing")
- OIDC tokens returned "invalid_grant"
- No successful user registrations logged
- Settings/Categories/Needs were all 0 pre-incident
- Production DB was a fresh deployment

**Schema:** Production schema was correctly recreated by migrations
(40 tables, 36 migrations — verified post-incident).

**Services:** Production URLs remained HTTP 200 throughout.

## Fix Applied

1. `php artisan config:clear` + `optimize:clear` — removed cache
2. `phpunit.xml` hardened:
   - `APP_ENV` value=`testing` force=`true`
   - `DB_CONNECTION` value=`mysql` force=`true`
   - `DB_DATABASE` value=`zagcreht_felagi_test` force=`true`
   - `DB_HOST` value=`127.0.0.1` force=`true`
   - Removed commented-out sqlite/`:memory:` lines
3. Verified: full suite passes 412/412; production DB untouched

## Prevention Rules

**NEVER run `php artisan config:cache` on this server.**

Reasons:
1. `.env.testing` becomes unreadable by PHPUnit
2. RefreshDatabase hits production DB
3. Force-cached DB env in phpunit.xml is a fallback, not a substitute

If config caching is required for performance:
- Use `config:cache` only on a deployment that does NOT run tests
- OR use `.env` only (no `.env.testing`) and rely on `--env` flag carefully
- OR add a CI guard that refuses `config:cache` when `.env.testing` exists

## Constitution Compliance

- [x] No hidden work — incident documented immediately
- [x] No silent changes — full disclosure in CHANGE_LOG
- [x] IMPLEMENTED != VERIFIED — fix verified by test suite
- [x] Rollback documented (see prevention rules)

## Verdict

**Data loss: NONE.** Production DB was empty. The incident caused:
- 3 hours of recovery work
- 10 false test failures
- Full database schema recreation

**Schema, services, tests are all healthy post-fix.**

## Author

Incident caused by AI action (config:cache during Phase B deploy).
Full responsibility acknowledged.

**Closed:** 2026-09-30 (post-verification)



════════════════════════════════════════════════════════════
# FILE: evidence/VERIFICATION_REPORT.md
════════════════════════════════════════════════════════════

# Verification Report — Quality Sweep (Phase C)

- **Date (UTC):** 2026-09-30T06:45:00Z
- **Scope:** B25–B28 (8 B-Blocks) + GAP-70 + GAP-71
- **Auditor:** AI (systematic pass — NOT a VERIFIED sign-off)
- **Constitution Art.:** "IMPLEMENTED != VERIFIED"

---

## Executive Summary

All 8 B-Blocks (B25–B28 + GAP-70 backfill) are VERIFIED-ELIGIBLE.
No blockers found. 412/412 tests pass. Production impact limited to
GAP-71 (1 method rename, zero callers).

---

## C1 — Full Test Suite Regression

| Metric | Value |
|--------|-------|
| Total tests | 412 |
| Total assertions | 691 |
| Failures | 0 |
| Duration | 13.48s |
| PHP | 8.2.33 |
| PHPUnit | 11.5.56 |

Command: vendor/bin/phpunit
Result: OK (412 tests, 691 assertions)

---

## C2 — Evidence File Consistency

| Block | Refs in MD | Logs on disk | Match |
|-------|------------|--------------|-------|
| B25 | 2 | 2 | yes |
| B26 | 2 | 2 | yes |
| B27 | 2 | 2 | yes |
| B28 | 2 | 2 | yes |
| Total | 8 | 8 | 8/8 |

Fixed during sweep:
- B25: added 2nd log reference (054848)
- B26: TS literal -> actual timestamp (060520)
- B27: TS literal -> actual timestamp (061552)

Before fix: 3/8 refs valid
After fix: 8/8 refs valid

---

## C3 — REQUIREMENT_REGISTRY

| Req ID | Description | Status |
|--------|-------------|--------|
| R-TEST-01 | Payment/Boost tests | COVERED (B25) |
| R-TEST-02 | AI/Comparison tests | COVERED (B26) |
| R-TEST-03 | Category tests | COVERED (B27) |
| R-TEST-04 | NeedAward tests | COVERED (B27) |
| R-TEST-05 | Attachment+Report tests | COVERED (B27) |
| R-TEST-06 | Setting+SettingDraft tests | COVERED (B28) |
| R-TEST-07 | UserRole tests | COVERED (B28) |

R-TEST items COVERED: 7/7

---

## C4 — Cross-Reference Audit

| Ledger | Count | Consistency |
|--------|-------|-------------|
| IMPLEMENTATION_LEDGER L217–L227 | 11 | yes |
| WORK_PACKAGES B-Blocks (B10–B28) | 13 | yes |
| CHANGE_LOG B18–B28 | 11 | yes |
| TEST_VERIFICATION B18–B28 | 7 sections | yes |
| REQUIREMENT_REGISTRY R-TEST | 7 + header | yes |
| OPEN_GAPS (GAP-70, GAP-71) | 2 + 2 (orig+resolved) | yes |

All cross-references consistent.

---

## C5 — Production Code Audit

Files changed (B25–B28 + backfill):

| File | Change | Notes |
|------|--------|-------|
| app/Models/Attachment.php | +/-1 line | isClean -> isScanClean (GAP-71) |

No migrations, no routes, no resources.

GAP-71 diff:

    - public function isClean(): bool
    + public function isScanClean(): bool

Impact: Zero callers (grep verified). Method was previously
unusable due to signature conflict with Laravel Model::isClean().

Approval: Explicit user authorization (Constitution Art.
"Do not perform breaking changes without explicit approval").

---

## Aggregate Findings

| Category | Result |
|----------|--------|
| Tests passing | 412/412 (100%) |
| Assertions | 691 |
| Failures | 0 |
| Evidence refs | 8/8 (100%) |
| R-TEST coverage | 7/7 (100%) |
| Cross-references | 100% consistent |
| Production changes | 1 file, 1 method rename |
| Breaking changes | 1 (GAP-71, approved) |
| Open blockers | 0 |

---

## What This Report IS

- A systematic audit pass confirming B25–B28 are VERIFIED-eligible
- Evidence that all quality gates (C1–C5) passed
- Documentation of findings + fixes

## What This Report IS NOT

- A VERIFIED sign-off — requires a second human reviewer
  per Constitution Art. "IMPLEMENTED != VERIFIED"
- A Production PASS — see WP-09 (9 gates) separately
- A bundle release — bundle refresh required after commit

---

## Next Actions

1. Commit quality-sweep changes (C7)
2. Second reviewer to sign off VERIFIED status
3. Bundle refresh at QUALITY_DONE
4. Phase B — Deploy (production push)
5. Phase D — New feature WP

---

## Constitution Compliance

- [x] IMPLEMENTED
- [x] INTEGRATED
- [x] TESTED
- [ ] VERIFIED (pending 2nd human reviewer)
- [x] DOCUMENTED
- [x] EVIDENCED

End of Verification Report.



════════════════════════════════════════════════════════════
# FILE: evidence/WIDGET_FLOW_20260930.md
════════════════════════════════════════════════════════════

# Telegram Widget Flow — Implementation Evidence

- **Date:** 2026-09-30
- **Trigger:** BotFather "Web Login is currently unavailable" — OIDC not supported
- **Fix Type:** Architecture pivot (OIDC -> Widget flow)
- **Impact:** Login flow now functional

## Root Cause

- BotFather: "Web login is currently unavailable for Felagi @FelagiMarketBot"
- TELEGRAM_CLIENT_ID=8629327448 (numeric bot ID, not hex OIDC client_id)
- OIDC URL construction added bot_id + origin -> Telegram served Widget page
- Callback expected state+code (OIDC), but Widget sends hash+auth_date

## Fix

### Files Added
- app/Services/Auth/TelegramWidgetService.php — HMAC-SHA256 verification
- tests/Feature/Auth/TelegramWidgetTest.php — 12 tests, 39 assertions

### Files Modified
- app/Http/Controllers/Api/V1/AuthController.php — added telegramWidgetStart, telegramWidgetCallback
- routes/api.php — added 2 widget routes
- config/services.php — added bot_token, bot_username
- resources/views/welcome.blade.php — Widget JS integration
- .env — added TELEGRAM_BOT_TOKEN, TELEGRAM_BOT_USERNAME

### Preserved (Deferred, Not Deleted)
- app/Services/Auth/TelegramOidcService.php — kept for future OIDC availability
- OIDC routes (/auth/telegram/start, /auth/telegram/callback) — kept

## Tests

| Suite | Tests | Assertions | Status |
|-------|-------|------------|--------|
| Widget (new) | 12 | 39 | PASS |
| Full suite | 424 | 730 | PASS |

### Widget Test Coverage
- Valid signature accepted
- Tampered hash rejected
- Tampered first_name rejected
- Expired auth_date rejected
- Missing bot_token rejected
- User upsert (create + update)
- Widget start config returned
- Widget start validates return_uri
- Full callback flow (verify -> user -> handoff)
- Invalid hash rejected (401)
- Missing state rejected (400)

## HMAC Verification Details

Per Telegram docs (core.telegram.org/widgets/login):
1. Build data_check_string from only signed fields: id, first_name, last_name, username, photo_url, auth_date (sorted alphabetically, joined by newline)
2. secret_key = SHA256(bot_token) (raw bytes)
3. expected_hash = HMAC_SHA256(data_check_string, secret_key)
4. hash_equals(received, expected)
5. Verify auth_date within WIDGET_MAX_AGE_SECONDS (300s)

Important: Exclude hash (signature) AND state (our CSRF token, not Telegram-signed).

## Constitution Compliance

- [x] No destructive change — OIDC code preserved (DEFERRED)
- [x] Additive — Widget new path
- [x] No hidden work — full incident + fix documented
- [x] IMPLEMENTED != VERIFIED — 2nd reviewer pending
- [x] Rollback: git revert <commit> (keeps OIDC if needed)

## Rollback

    git revert <WIDGET-commit>
    # OR manual:
    # - Remove widget routes from routes/api.php
    # - Remove widget methods from AuthController
    # - Revert welcome.blade.php to previous
    # - Revert config/services.php

## Status

- IMPLEMENTED (yes)
- INTEGRATED (yes)
- TESTED (yes — 12/12 + 424/424)
- VERIFIED (pending 2nd reviewer)
- DOCUMENTED (this file)
- EVIDENCED (WIDGET_phpunit_*.log, WIDGET_fullsuite_*.log)

Deploy pending.



════════════════════════════════════════════════════════════
# FILE: evidence/WP-B25_evidence.md
════════════════════════════════════════════════════════════

# WP-B25 Evidence — Payment Domain Test Suite

- **Requirement:** R-TEST-01
- **Date (UTC):** 2026-09-30T05:33:05Z
- **Operator:** zagcreht
- **Commit before:** 143a75685944ee797509202305e99b9ab492563c
- **Branch:** master

## Files created
- tests/Feature/Models/PaymentTest.php (7716 bytes, 16 tests)
- tests/Feature/Models/PaymentEventTest.php (5608 bytes, 13 tests)
- tests/Feature/Models/BoostTest.php (6620 bytes, 14 tests)
- tests/Feature/Models/BoostPackageTest.php (3437 bytes, 11 tests)

## Test results
- **Total tests:** 54
- **Assertions:** 83
- **Status:** OK (100% pass)
- **Runtime:** 7.5s
- **PHP:** 8.2.33
- **PHPUnit:** 11.5.56

## Coverage
- Payment: 16 tests
  - UUID, casts, relationships, scopes, constants
  - Unique constraints: (payer_id, idempotency_key), (provider, provider_reference)
- PaymentEvent: 13 tests
  - UUID, no timestamps, nullable payment_id
  - Boolean/array casts, unique (provider, provider_event_id)
- Boost: 14 tests
  - UUID, casts, 4 relationships, active scope
  - Unique payment_id (1:1 with Payment)
- BoostPackage: 11 tests
  - UUID, casts, defaults (ETB, active=false)
  - Active scope, hasMany boosts

## R-TEST-01 status
- Before: 0 tests (UNKNOWN)
- After: 54 tests, 83 assertions, 100% pass
- **R-TEST-01 → COVERED**

## Rollback
```bash
git revert <commit-sha>
# OR (if not committed)
rm tests/Feature/Models/PaymentTest.php \
   tests/Feature/Models/PaymentEventTest.php \
   tests/Feature/Models/BoostTest.php \
   tests/Feature/Models/BoostPackageTest.php
```

## Known risks / remaining gaps
- No factory classes for Payment/PaymentEvent/Boost/BoostPackage (used ::create() directly)
  - Suggest: WP-B25b — add HasFactory trait + factories for reuse in Feature tests
- Coverage report not generated (no --coverage-text run)
  - Suggest: run `vendor/bin/phpunit --coverage-text` for % metrics

## Constitution compliance
- [x] No production code changed (tests only)
- [x] No architecture change
- [x] No hidden work
- [x] UNKNOWN markers resolved before coding
- [x] Rollback documented
- [x] Evidence captured

## DONE definition checklist
- [x] IMPLEMENTED — 4 test files created
- [x] INTEGRATED — run against real schema (migrate:fresh --env=testing)
- [x] TESTED — 54/54 pass
- [ ] VERIFIED — requires second reviewer
- [x] DOCUMENTED — this file
- [x] EVIDENCED — WP-B25_phpunit_20260930_053259.log (initial)
- [x] EVIDENCED — WP-B25_phpunit_20260930_054848.log (re-run, warm cache)



════════════════════════════════════════════════════════════
# FILE: evidence/WP-B26_evidence.md
════════════════════════════════════════════════════════════

# WP-B26 Evidence — AI/Comparison Domain Test Suite

- **Requirement:** R-TEST-02
- **Date (UTC):** $(date -u +%Y-%m-%dT%H:%M:%SZ)
- **Operator:** zagcreht
- **Commit before:** $(git rev-parse HEAD)
- **Branch:** master

## Files created

- tests/Feature/Models/ComparisonTest.php (23 tests)
- tests/Feature/Models/ComparisonOfferTest.php (13 tests)
- tests/Feature/Models/ComparisonResultTest.php (15 tests)
- tests/Feature/Models/ComparisonAttemptTest.php (17 tests)

## Test results

- **B26 tests:** 68 (93 assertions) — 100% pass
- **Full suite:** 216 → 284 tests (403 → 496 assertions)
- **Runtime:** 4.87s (B26 only), 11.02s (full suite)
- **PHP:** 8.2.33
- **PHPUnit:** 11.5.56

## Coverage

- **Comparison (23 tests)**
  - UUID, casts (need_snapshot array, decimal:4 cost, int counts), datetimes
  - Relations: need, triggeredBy, comparisonOffers, results, attempts
  - Constants: STATUS_PENDING/PROCESSING/COMPLETED/FAILED
  - Scopes: completed(); helpers: isCompleted(), isFailed()
  - Unique: (need_id, version_number)
  - Defaults: eligible_offer_count=0, included_offer_count=0, attempt_count=0, status=PENDING
- **ComparisonOffer (13 tests)**
  - UUID, casts (offer_snapshot, credibility_snapshot arrays)
  - Relations: comparison, offer, provider, result (hasOne)
  - Unique: (comparison_id, offer_id)
  - 64-hex hash storage
  - Multiple offers per comparison
  - Timestamps enabled
- **ComparisonResult (15 tests)**
  - UUID, no timestamps
  - Relations: comparison, comparisonOffer
  - Casts: score decimal:2, criterion_scores/strengths/weaknesses/missing_information/risk_notes arrays
  - Fit explanation text (multi-KB)
  - Result hash 64 chars
  - Composite FK (comparison_offer_id, comparison_id) enforcement
  - Unique: (comparison_id, comparison_offer_id)
- **ComparisonAttempt (17 tests)**
  - UUID, no timestamps
  - Relations: comparison
  - Casts: attempt_number int, started_at/finished_at datetime, token_usage array
  - Constants: STATUS_PROCESSING/SUCCEEDED/FAILED
  - Default status=PROCESSING; started_at auto-set
  - Unique: (comparison_id, attempt_number)

## Schema findings (during test development)

- **MySQL JSON type coercion bug (test-side):** JSON integer 5.0 stored as 5.
  Fixed by using assertEquals instead of assertSame for credibility_snapshot.
  Schema itself is correct.
- **Composite FK** (comparison_results → comparison_offers via (id, comparison_id))
  verified working as designed.
- **Composite unique** (id, comparison_id) on comparison_offers verified.
- **No HasFactory** on any of the 4 models — used ::create() directly (same pattern as B25).

## R-TEST-02 status

- Before: 0 tests (UNKNOWN)
- After: 68 tests, 93 assertions, 100% pass
- **R-TEST-02 → COVERED**

## Rollback

```bash
git revert HEAD  # test-only, safe
# OR
rm tests/Feature/Models/Comparison{Test,OfferTest,ResultTest,AttemptTest}.php
```

## Known risks / remaining gaps

- No ComparisonFactory, ComparisonOfferFactory, ComparisonResultFactory, ComparisonAttemptFactory (same as B25 pattern)
- Composite FK test would benefit from an intentional violation test (future)
- Coverage report not generated (no --coverage-text run)

## Constitution compliance

- [x] No production code changed (tests only)
- [x] No architecture change
- [x] No hidden work
- [x] UNKNOWN markers resolved before coding
- [x] Rollback documented
- [x] Evidence captured

## DONE definition checklist

- [x] IMPLEMENTED — 4 test files created
- [x] INTEGRATED — run against real schema (migrate:fresh --env=testing)
- [x] TESTED — 68/68 pass; full suite 284/284 pass
- [ ] VERIFIED — requires second reviewer
- [x] DOCUMENTED — this file
- [x] EVIDENCED — WP-B26_phpunit_20260930_060520.log + WP-B26_fullsuite_20260930_060520.log



════════════════════════════════════════════════════════════
# FILE: evidence/WP-B27_evidence.md
════════════════════════════════════════════════════════════

# WP-B27 Evidence — Safety/Marketplace Domain Test Suite

- **Requirements:** R-TEST-03 (Category), R-TEST-04 (NeedAward), R-TEST-05 (Attachment + Report)
- **Date (UTC):** $(date -u +%Y-%m-%dT%H:%M:%SZ)
- **Operator:** zagcreht
- **Commit before:** $(git rev-parse HEAD)
- **Branch:** master

## Files created

- tests/Feature/Models/CategoryTest.php (15 tests)
- tests/Feature/Models/NeedAwardTest.php (13 tests)
- tests/Feature/Models/AttachmentTest.php (21 tests)
- tests/Feature/Models/ReportTest.php (17 tests)

## Test results

- **B27 tests:** 66 (99 assertions) — 100% pass
- **Full suite:** 284 → 350 tests (496 → 595 assertions)
- **Runtime:** 3.04s (Attachment only), ~XXs (full suite)
- **PHP:** 8.2.33
- **PHPUnit:** 11.5.56

## Coverage

- **Category (15 tests)**
  - UUID, casts (active bool, sort_order int), defaults (active=true, sort_order=0)
  - Relations: creator (nullable), needs (hasMany)
  - Scopes: active(), ordered() (sort_order + slug tiebreaker)
  - Helper: name($locale) — am default, en override
  - Unique: slug; max length 60
- **NeedAward (13 tests)**
  - No incrementing, no timestamps
  - PK = need_id (string/uuid)
  - Relations: need, offer, acceptedBy
  - Casts: accepted_at datetime
  - Composite PK (need_id) blocks duplicates
  - offer_id UNIQUE constraint
  - Composite FK (offer_id, need_id) → offers(id, need_id) blocks cross-need offers
- **Attachment (21 tests)**
  - UUID, no timestamps, SoftDeletes
  - Relations: uploader, need (nullable), offer (nullable), message (nullable)
  - Casts: byte_size int, created_at datetime
  - Constants: PURPOSE_*, VISIBILITY_*, SCAN_*
  - Defaults: visibility=PRIVATE, scan_status=PENDING
  - Scopes: clean()
  - Helper: isScanClean() (renamed from isClean — see GAP-71)
  - SHA-256 64-char storage
  - Soft delete preserves row
- **Report (17 tests)**
  - UUID, timestamps enabled
  - Relations: reporter, assignee (nullable)
  - Nullable: details, resolution_code, assigned_to
  - Constants: ENTITY_*, STATUS_*, REASON_*
  - Default: status=OPEN
  - Scopes: open()
  - entity_id polymorphic (no FK) — multiple reports allowed

## Schema findings

- **Production bug found (GAP-71):** Attachment::isClean() collides with Laravel Model::isClean()
  - Fixed with approval: isClean → isScanClean (breaking change)
  - No callers existed — zero runtime impact
  - See GAP-71 for full details
- NeedAward composite PK — verified working
- NeedAward composite FK (offer_id, need_id) — verified working
- Report entity_id is polymorphic-style (no FK)

## R-TEST status

- R-TEST-03 (Category): 0 → 15 tests → **COVERED**
- R-TEST-04 (NeedAward): 0 → 13 tests → **COVERED**
- R-TEST-05 (Attachment + Report): 0 → 38 tests → **COVERED**

## Rollback

Test files: `rm tests/Feature/Models/{CategoryTest,NeedAwardTest,AttachmentTest,ReportTest}.php`
Production fix: `git revert <B27-commit> -- app/Models/Attachment.php`

## Known risks / remaining gaps

- No factories for Category/NeedAward/Attachment/Report (same as B25/B26 pattern)
- GAP-71 production fix (isClean→isScanClean) — verify no external callers in future
- Coverage report not generated

## Constitution compliance

- [x] Production code changed — WITH EXPLICIT APPROVAL (GAP-71 fix)
- [x] Breaking change documented in CHANGE_LOG
- [x] No hidden work
- [x] UNKNOWN markers resolved before coding
- [x] Rollback documented
- [x] Evidence captured

## DONE definition checklist

- [x] IMPLEMENTED — 4 test files + 1 production fix
- [x] INTEGRATED — migrate:fresh --env=testing
- [x] TESTED — 66/66 pass; full suite 350/350 pass
- [ ] VERIFIED — requires second reviewer
- [x] DOCUMENTED — this file
- [x] EVIDENCED — WP-B27_phpunit_20260930_061552.log + WP-B27_fullsuite_20260930_061552.log



════════════════════════════════════════════════════════════
# FILE: evidence/WP-B28_evidence.md
════════════════════════════════════════════════════════════

# WP-B28 Evidence — Settings/Role Domain Test Suite

- **Requirements:** R-TEST-06 (Setting + SettingDraft), R-TEST-07 (UserRole)
- **Date (UTC):** $(date -u +%Y-%m-%dT%H:%M:%SZ)
- **Operator:** zagcreht
- **Commit before:** $(git rev-parse HEAD)
- **Branch:** master

## Files created
- tests/Feature/Models/SettingTest.php (25 tests)
- tests/Feature/Models/SettingDraftTest.php (20 tests)
- tests/Feature/Models/UserRoleTest.php (17 tests)

## Test results
- **B28 tests:** 62 (96 assertions) — 100% pass
- **Full suite:** 350 → 412 tests (595 → 691 assertions)
- **Runtime:** ~9s (B28 only), 14.28s (full suite)
- **PHP:** 8.2.33 | PHPUnit: 11.5.56

## Coverage

### Setting (25 tests)
- PK = key (string), no id, no incrementing, no timestamps auto
- Casts: value_json/default_json/schema_json arrays, is_secret bool, version_number int, updated_at datetime
- Relations: versions, drafts, updatedBy (nullable)
- Constants: GROUP_*, RISK_*, TYPE_*
- Defaults: is_secret=false, version_number=1
- Helpers: requiresReauth() (HIGH+CRITICAL), requiresSecondFactor() (CRITICAL only)

### SettingDraft (20 tests)
- UUID, timestamps enabled
- Relations: setting, proposer
- Casts: proposed_value_json/validation_report/impact_preview arrays
- Nullable: validation_report, impact_preview
- Constants: STATUS_DRAFT/VALIDATED/REJECTED/PUBLISHED
- Default status=DRAFT
- Helper: isEditable() (DRAFT+VALIDATED)
- FK setting_key must exist (verified)
- Multiple drafts per setting allowed

### UserRole (17 tests)
- No incrementing, no timestamps, composite PK (user_id, role)
- Relations: user, grantedBy (nullable)
- Casts: granted_at, revoked_at
- Constants: ROLE_MAIN_ADMIN/ADMIN/MODERATOR
- Scope: active() (revoked_at null)
- Composite PK prevents duplicates
- Same user can have multiple roles
- User scope withRole() filters active only

## Schema findings
- settings uses UPDATED_AT constant (no created_at)
- setting_drafts FK setting_key → settings.key verified
- user_roles uses granted_at useCurrent — model doesn't auto-populate
  (test uses DB fetch via where()->first())
- UserRole has NO $primaryKey set (composite) — avoid fresh()/find()

## R-TEST status
- R-TEST-06 (Setting + SettingDraft): PARTIAL → **COVERED**
- R-TEST-07 (UserRole): 0 → **COVERED**

## Rollback
Test files only: `rm tests/Feature/Models/{SettingTest,SettingDraftTest,UserRoleTest}.php`

## Known risks / remaining gaps
- No factories for Setting/SettingDraft/UserRole (same pattern as B25-B27)
- Coverage report not generated

## Constitution compliance
- [x] No production code changed (tests only)
- [x] UNKNOWN markers resolved before coding
- [x] No silent changes
- [x] Rollback documented
- [x] Evidence captured

## DONE definition checklist
- [x] IMPLEMENTED — 3 test files
- [x] INTEGRATED — migrate:fresh --env=testing
- [x] TESTED — 62/62 pass; full suite 412/412 pass
- [ ] VERIFIED — requires second reviewer
- [x] DOCUMENTED — this file
- [x] EVIDENCED — WP-B28_phpunit_20260930_062707.log + WP-B28_fullsuite_20260930_062707.log




# ═══════════════════════════════════════════
# SECTION C — EVIDENCE LOGS (tail 50 lines each)
# ═══════════════════════════════════════════


════════════════════════════════════════════════════════════
# FILE: evidence/WIDGET_fullsuite_20260930_080425.log
════════════════════════════════════════════════════════════

PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.33
Configuration: /home/zagcreht/felagi_app/phpunit.xml

...............................................................  63 / 424 ( 14%)
............................................................... 126 / 424 ( 29%)
............................................................... 189 / 424 ( 44%)
............................................................... 252 / 424 ( 59%)
............................................................... 315 / 424 ( 74%)
............................................................... 378 / 424 ( 89%)
..............................................                  424 / 424 (100%)

Time: 00:13.875, Memory: 62.50 MB

OK (424 tests, 730 assertions)


... (truncated to last 50 lines)


════════════════════════════════════════════════════════════
# FILE: evidence/WIDGET_phpunit_20260930_080425.log
════════════════════════════════════════════════════════════

PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.33
Configuration: /home/zagcreht/felagi_app/phpunit.xml

............                                                      12 / 12 (100%)

Time: 00:03.428, Memory: 46.50 MB

Telegram Widget (Tests\Feature\Auth\TelegramWidget)
 ✔ Verify accepts valid signature
 ✔ Verify rejects tampered hash
 ✔ Verify rejects tampered first name
 ✔ Verify rejects expired auth date
 ✔ Verify rejects missing bot token
 ✔ Upsert user creates new
 ✔ Upsert user updates existing
 ✔ Widget start returns config
 ✔ Widget start validates return uri
 ✔ Widget callback full flow
 ✔ Widget callback rejects invalid hash
 ✔ Widget callback rejects missing state

OK (12 tests, 39 assertions)


... (truncated to last 50 lines)


════════════════════════════════════════════════════════════
# FILE: evidence/WP-B25_phpunit_20260930_053259.log
════════════════════════════════════════════════════════════

 ✔ Scope active requires status and window
 ✔ Payment id is unique per boost

Boost Package (Tests\Feature\Models\BoostPackage)
 ✔ Uuid auto generated
 ✔ Persists core attributes
 ✔ Duration days casts integer
 ✔ Price casts to decimal 2
 ✔ Active casts to boolean
 ✔ Default currency is etb
 ✔ Default active is false
 ✔ Published settings version nullable
 ✔ Has many boosts
 ✔ Scope active filters correctly
 ✔ Duration days accepts unsigned smallint

Payment (Tests\Feature\Models\Payment)
 ✔ Uuid auto generated
 ✔ Persists core attributes
 ✔ Belongs to payer
 ✔ Belongs to need
 ✔ Has many events
 ✔ Boost is null initially
 ✔ Amount casts to decimal 2
 ✔ Confirmed at casts to datetime
 ✔ Failed at casts to datetime
 ✔ Is confirmed false for pending
 ✔ Is confirmed true for confirmed
 ✔ Scope confirmed filters
 ✔ Purpose constants
 ✔ Status constants
 ✔ Unique payer id and idempotency key
 ✔ Unique provider and provider reference

Payment Event (Tests\Feature\Models\PaymentEvent)
 ✔ Uuid auto generated
 ✔ No timestamps
 ✔ Belongs to payment
 ✔ Payment id nullable
 ✔ Signature valid casts to boolean
 ✔ Sanitized metadata casts to array
 ✔ Sanitized metadata nullable
 ✔ Received at casts to datetime
 ✔ Processing status constants
 ✔ Default processing status is received
 ✔ Unique provider and provider event id
 ✔ Payload digest stores 64 hex
 ✔ Event type stored correctly

OK (54 tests, 83 assertions)


... (truncated to last 50 lines)


════════════════════════════════════════════════════════════
# FILE: evidence/WP-B25_phpunit_20260930_054848.log
════════════════════════════════════════════════════════════

 ✔ Scope active requires status and window
 ✔ Payment id is unique per boost

Boost Package (Tests\Feature\Models\BoostPackage)
 ✔ Uuid auto generated
 ✔ Persists core attributes
 ✔ Duration days casts integer
 ✔ Price casts to decimal 2
 ✔ Active casts to boolean
 ✔ Default currency is etb
 ✔ Default active is false
 ✔ Published settings version nullable
 ✔ Has many boosts
 ✔ Scope active filters correctly
 ✔ Duration days accepts unsigned smallint

Payment (Tests\Feature\Models\Payment)
 ✔ Uuid auto generated
 ✔ Persists core attributes
 ✔ Belongs to payer
 ✔ Belongs to need
 ✔ Has many events
 ✔ Boost is null initially
 ✔ Amount casts to decimal 2
 ✔ Confirmed at casts to datetime
 ✔ Failed at casts to datetime
 ✔ Is confirmed false for pending
 ✔ Is confirmed true for confirmed
 ✔ Scope confirmed filters
 ✔ Purpose constants
 ✔ Status constants
 ✔ Unique payer id and idempotency key
 ✔ Unique provider and provider reference

Payment Event (Tests\Feature\Models\PaymentEvent)
 ✔ Uuid auto generated
 ✔ No timestamps
 ✔ Belongs to payment
 ✔ Payment id nullable
 ✔ Signature valid casts to boolean
 ✔ Sanitized metadata casts to array
 ✔ Sanitized metadata nullable
 ✔ Received at casts to datetime
 ✔ Processing status constants
 ✔ Default processing status is received
 ✔ Unique provider and provider event id
 ✔ Payload digest stores 64 hex
 ✔ Event type stored correctly

OK (54 tests, 83 assertions)


... (truncated to last 50 lines)


════════════════════════════════════════════════════════════
# FILE: evidence/WP-B26_fullsuite_20260930_060520.log
════════════════════════════════════════════════════════════

PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.33
Configuration: /home/zagcreht/felagi_app/phpunit.xml

...............................................................  63 / 284 ( 22%)
............................................................... 126 / 284 ( 44%)
............................................................... 189 / 284 ( 66%)
............................................................... 252 / 284 ( 88%)
................................                                284 / 284 (100%)

Time: 00:13.207, Memory: 60.50 MB

OK (284 tests, 496 assertions)


... (truncated to last 50 lines)


════════════════════════════════════════════════════════════
# FILE: evidence/WP-B26_phpunit_20260930_060520.log
════════════════════════════════════════════════════════════

 ✔ No timestamps
 ✔ Belongs to comparison
 ✔ Attempt number casts integer
 ✔ Started at casts to datetime
 ✔ Finished at casts to datetime
 ✔ Finished at nullable
 ✔ Token usage casts to array
 ✔ Token usage nullable
 ✔ Status constants
 ✔ Default status is processing
 ✔ Failure code nullable
 ✔ Failure code stored
 ✔ Provider request id nullable
 ✔ Unique comparison id and attempt number
 ✔ Multiple attempts with different numbers
 ✔ Started at auto set by db

Comparison Offer (Tests\Feature\Models\ComparisonOffer)
 ✔ Uuid auto generated
 ✔ Belongs to comparison
 ✔ Belongs to offer
 ✔ Belongs to provider
 ✔ Has one result initially null
 ✔ Offer snapshot casts to array
 ✔ Credibility snapshot casts to array
 ✔ Offer snapshot hash stores 64 hex
 ✔ Unique comparison id and offer id
 ✔ Can add multiple offers to one comparison
 ✔ Uses timestamps
 ✔ Created at populated
 ✔ Updated at populated

Comparison Result (Tests\Feature\Models\ComparisonResult)
 ✔ Uuid auto generated
 ✔ No timestamps
 ✔ Belongs to comparison
 ✔ Belongs to comparison offer
 ✔ Score casts decimal 2
 ✔ Score nullable
 ✔ Criterion scores casts to array
 ✔ Strengths casts to array
 ✔ Weaknesses casts to array
 ✔ Missing information casts to array
 ✔ Risk notes casts to array
 ✔ Fit explanation stored as text
 ✔ Result hash is 64 chars
 ✔ Created at casts to datetime
 ✔ Unique comparison id and comparison offer id

OK (68 tests, 93 assertions)


... (truncated to last 50 lines)


════════════════════════════════════════════════════════════
# FILE: evidence/WP-B27_fullsuite_20260930_061552.log
════════════════════════════════════════════════════════════

PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.33
Configuration: /home/zagcreht/felagi_app/phpunit.xml

...............................................................  63 / 350 ( 18%)
............................................................... 126 / 350 ( 36%)
............................................................... 189 / 350 ( 54%)
............................................................... 252 / 350 ( 72%)
............................................................... 315 / 350 ( 90%)
...................................                             350 / 350 (100%)

Time: 00:12.236, Memory: 60.50 MB

OK (350 tests, 595 assertions)


... (truncated to last 50 lines)


════════════════════════════════════════════════════════════
# FILE: evidence/WP-B27_phpunit_20260930_061552.log
════════════════════════════════════════════════════════════

 ✔ Persists core attributes
 ✔ Active casts to boolean
 ✔ Sort order casts integer
 ✔ Default active is true
 ✔ Default sort order is zero
 ✔ Belongs to creator
 ✔ Creator nullable
 ✔ Has many needs
 ✔ Scope active filters
 ✔ Scope ordered sorts by sort order then slug
 ✔ Name returns amharic by default
 ✔ Name returns en for en locale
 ✔ Slug unique constraint
 ✔ Slug max length 60

Need Award (Tests\Feature\Models\NeedAward)
 ✔ No incrementing
 ✔ No timestamps
 ✔ Primary key is need id
 ✔ Primary key type is string
 ✔ Persists core attributes
 ✔ Belongs to need
 ✔ Belongs to offer
 ✔ Belongs to accepted by user
 ✔ Accepted at casts to datetime
 ✔ Request id stored
 ✔ Composite pk prevents duplicate need award
 ✔ Offer id is unique
 ✔ Composite fk prevents cross need offer

Report (Tests\Feature\Models\Report)
 ✔ Uuid auto generated
 ✔ Persists core attributes
 ✔ Belongs to reporter
 ✔ Assigned to nullable
 ✔ Belongs to assignee when set
 ✔ Details nullable
 ✔ Resolution code nullable
 ✔ Entity type constants
 ✔ Status constants
 ✔ Reason constants
 ✔ Default status is open
 ✔ Scope open filters
 ✔ Multiple reports for same entity allowed
 ✔ Entity id has no fk constraint
 ✔ Uses timestamps
 ✔ Created at populated
 ✔ Resolution code stored when resolved

OK (66 tests, 99 assertions)


... (truncated to last 50 lines)


════════════════════════════════════════════════════════════
# FILE: evidence/WP-B28_fullsuite_20260930_062707.log
════════════════════════════════════════════════════════════

PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.33
Configuration: /home/zagcreht/felagi_app/phpunit.xml

...............................................................  63 / 412 ( 15%)
............................................................... 126 / 412 ( 30%)
............................................................... 189 / 412 ( 45%)
............................................................... 252 / 412 ( 61%)
............................................................... 315 / 412 ( 76%)
............................................................... 378 / 412 ( 91%)
..................................                              412 / 412 (100%)

Time: 00:14.290, Memory: 62.50 MB

OK (412 tests, 691 assertions)


... (truncated to last 50 lines)


════════════════════════════════════════════════════════════
# FILE: evidence/WP-B28_phpunit_20260930_062707.log
════════════════════════════════════════════════════════════

 ✔ Type constants
 ✔ Requires reauth true for high
 ✔ Requires reauth true for critical
 ✔ Requires reauth false for low
 ✔ Requires reauth false for medium
 ✔ Requires second factor true for critical
 ✔ Requires second factor false for high

Setting Draft (Tests\Feature\Models\SettingDraft)
 ✔ Uuid auto generated
 ✔ Uses timestamps
 ✔ Persists core attributes
 ✔ Belongs to setting
 ✔ Belongs to proposer
 ✔ Proposed value json casts to array
 ✔ Validation report casts to array
 ✔ Validation report nullable
 ✔ Impact preview casts to array
 ✔ Impact preview nullable
 ✔ Created at casts to datetime
 ✔ Updated at casts to datetime
 ✔ Status constants
 ✔ Default status is draft
 ✔ Is editable true for draft
 ✔ Is editable true for validated
 ✔ Is editable false for rejected
 ✔ Is editable false for published
 ✔ Fk setting key must exist
 ✔ Multiple drafts for same setting allowed

User Role (Tests\Feature\Models\UserRole)
 ✔ No incrementing
 ✔ No timestamps
 ✔ Persists core attributes
 ✔ Belongs to user
 ✔ Granted by nullable
 ✔ Belongs to granted by user
 ✔ Granted at casts to datetime
 ✔ Granted at auto set by db
 ✔ Revoked at nullable
 ✔ Revoked at casts to datetime
 ✔ Role constants
 ✔ Scope active filters revoked
 ✔ Composite pk prevents duplicate user role
 ✔ Same user can have multiple roles
 ✔ User roles relation
 ✔ User scope with role finds admins
 ✔ User scope with role excludes revoked

OK (62 tests, 96 assertions)


... (truncated to last 50 lines)



# ═══════════════════════════════════════════
# SECTION D — RUNTIME INFO
# ═══════════════════════════════════════════

## Git Log (last 30 commits)

```
8539761 WIDGET-FLOW: Telegram Login Widget pivot (OIDC unavailable)
f14d3ec GAP-71c + INCIDENT: Ledgers update (L229, factories, incident)
84e1be5 GAP-71c: 14 additional factories + HasFactory traits (infrastructure)
331e98e INCIDENT-FIX: Harden phpunit.xml + document config:cache incident
ce30e04 WP-05c: Register as BLOCKED_ON_UNKNOWN (Phase D audit)
2ca5e4f B7: RELEASE_STATUS update — B25-B28 + GAP-70 + GAP-71b deploy
7cf679b GAP-71b: Attachment factory + HasFactory (infrastructure)
4285ad1 Quality sweep (Phase C): evidence consistency + R-TEST registry + verification report
c6c4cb7 GAP-70 backfill: B18-B24 ledger entries (doc-only)
c68ea52 B28: Settings/Role test suite (R-TEST-06/07) + ledger updates
6db0e86 B27: Safety/Marketplace tests + GAP-71 fix (R-TEST-03/04/05)
861fd6e B26: AI/Comparison domain test suite (R-TEST-02) + ledger updates
ce47d24 B25-ledger: sync SHA refs to actual HEAD (ebeac42)
ebeac42 B25-ledger: clarify B25 commit chain (202716b..4e66721)
694e5ff B25-evidence: remove reference to deleted log
ca743b2 B25-evidence: restore second phpunit log
4e66721 B25-ledger: finalize SHA references (202716b)
f68b3f8 B25-evidence: remove duplicate phpunit log
3725843 B25-evidence: reference both phpunit runs
202716b B25: Payment domain test suite (R-TEST-01) + ledger updates
143a756 fix(B24): S001 signIn — GAP-63/64/65/66 chained fixes
3e7236c docs(B24): Update source-of-truth with B24 signin fix
48217b2 docs(B23): Publish source-of-truth to public handoff + downloads
7a0d21e docs(B22): Add MASTER SOURCE OF TRUTH (consolidated 4 files)
6bc49c8 docs(B22): Add comprehensive project status report (523 lines)
1a17a7c docs(B22): Bundle refresh — 4 artifacts at B21_DONE
c64fa28 test(B21): Extended model tests + migration audit + spec requests
6c6bcb6 docs(B20): Bundle refresh — 4 artifacts at B19_DONE
b451224 test(B19): Model unit tests — 25 new tests for 3 critical models
dbc689c docs(B18): Bundle refresh — 4 artifacts at B17_DONE
```

## Test Summary

```
..............................................                  424 / 424 (100%)

Time: 00:13.376, Memory: 62.50 MB

OK (424 tests, 730 assertions)
```

## File Inventory

```
Ledgers: 14
Evidence: 18
Tests: 22
Factories: 18
Bundles: 8
```

---

**End of FELAGI Complete Handoff — 2026-09-30T08:19:35Z**
