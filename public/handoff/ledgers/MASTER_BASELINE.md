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
