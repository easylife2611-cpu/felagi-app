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


---

## S003 Profile + Post-Login Routing — 2026-09-30

**Type:** Feature
**HEAD before:** 4f64b1d (HANDOFF_FINAL)

### Changes
- NEW: resources/views/profile.blade.php (S003 spec — 229 lines)
- NEW: resources/views/browse.blade.php (S004 placeholder — 62 lines)
- UPD: routes/web.php — /profile, /browse routes
- UPD: resources/views/welcome.blade.php — 3 redirects → /profile

### Flow
Login → S003 Profile → S004 Browse

### Verification
- All routes HTTP 200
- profile matches: 12
- browse matches: 6
- welcome redirects: 3

### Rollback
git revert <S003-commit>

---

## B28 — GAP-71c: Remaining factories (8 models)

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L258

### Added
- 8 factories: AuditLog, AuthAttempt, OutboxEvent, Rating,
  SettingVersion, TelegramDestination, TelegramPublication,
  TelegramPublicationEvent

### Changed
- 8 models: HasFactory trait

### Tests
- 699 tests · 1,380 assertions · 0 failures
- No new tests (infra only)

### GAPs
- GAP-71c — ✅ RESOLVED

---

## B29 — PHPUnit 11 deprecation cleanup

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L259

### Changed
- tests/Feature/Screens/AdminScreensTest.php
  - `@dataProvider` → `#[DataProvider]` (3×)
  - Removed empty `/** */` stubs (3×)
  - Added `use PHPUnit\Framework\Attributes\DataProvider;`

### Tests
- 699 tests · 1,380 assertions · **0 deprecations**
- (Previous: 3 PHPUnit deprecations)

---

## B30 — Documentation reconciliation

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L260

### Changed
- OPEN_GAPS.md — GAP-71c stale list corrected
- SOURCE_OF_TRUTH.md — HEAD/timestamp refreshed

### Tests
- 699 · 1,380 · 0 failures · 0 deprecations (no code change)

---

## B31 — D1-D5 audit reports + INDEX

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L261

### Added
- docs/reports/BUNDLE_INVENTORY_20260930.md (D2)
- docs/reports/PHPUNIT12_COMPAT_20260930.md (D1)
- docs/reports/SPEC_REQUESTS_STATUS_20260930.md (D4)
- docs/reports/N06_N10_EVIDENCE_REQUIREMENTS_20260930.md (D3)
- public/handoff/ledgers/INDEX.md (D5)

### Tests
- 699 · 1,380 · 0 failures · 0 deprecations (no code change)

---

## B32 — WP-05c implemented (23 admin read endpoints)

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L262

### Added
- app/Policies/AdminReadPolicy.php
- app/Http/Requests/Admin/AdminListRequest.php
- app/Http/Controllers/Api/V1/Admin/AdminReadController.php
- tests/Feature/Admin/AdminReadEndpointsTest.php (27 tests)
- tests/Feature/Admin/AdminReadPaginationTest.php (10 tests)

### Changed
- routes/api.php: +23 GET /api/v1/admin/* (auth:sanctum)

### Tests
- +37 tests, +373 assertions
- Full suite: 736 tests · 1,753 assertions · 0 failures

### WP
- WP-05c: BLOCKED_ON_UNKNOWN → RESOLVED

---

## B33 — Session addendum: health + reports + bundle cleanup

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L263

### Added
- app/Http/Controllers/Api/V1/HealthController.php (E)
- tests/Feature/Health/HealthCheckTest.php (E)
- docs/reports/T01-T18_DEFINITIONS_20260930.md (A)
- docs/reports/GAP-70_VERIFICATION_20260930.md (C)
- docs/reports/OPENAPI_COVERAGE_20260930.md (D)

### Changed
- routes/api.php: +GET /api/health (E)

### Bundles (B)
- Archived 21 old bundles to ~/bundle_archive/

### Tests
- +3 tests, +18 assertions
- Full suite: 739 tests / 1,771 assertions / 0 failures

---

## B34 — WP-13c backend (2FA enrollment API)

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L264

### Added
- app/Http/Controllers/Api/V1/TwoFactorController.php (7 methods)
- tests/Feature/Auth/TwoFactorEnrollmentTest.php (22 tests)
- database/migrations/2026_09_30_155000_alter_totp_recovery_codes_to_text.php

### Changed
- routes/api.php: +7 routes under /api/v1/auth/2fa/*

### Tests
- +22 tests, +59 assertions
- Full suite: 761 tests / 1,830 assertions / 0 failures

### WP
- WP-13c backend: DONE
- WP-13c UI: still BLOCKED (D-097)

---

## B35 — WP-10 AI provider + comparison service

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L265

### Added
- config/ai.php
- app/Services/AI/GeminiClient.php
- app/Services/AI/ComparisonService.php
- tests/Feature/AI/ComparisonAiTest.php (17 tests)

### Changed
- app/Http/Controllers/Api/V1/ComparisonController.php (store → real AI)
- tests/Feature/Screens/S014CompareTest.php (501 → 503)

### Tests
- +17 tests, +53 assertions
- Full suite: 778 tests / 1,883 assertions / 0 failures

### Provider
- Google Gemini (gemini-flash-latest)

### WP
- WP-10: BLOCKED → RESOLVED
- GAP-10: AI live → RESOLVED

---

## B36 — T01-T26 local subset + If-Match + RequestId

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L266

### Added
- tests/Feature/Integration/T01_T26LocalSubsetTest.php (13 tests)
- app/Http/Middleware/RequestId.php

### Changed
- app/Http/Controllers/Api/V1/NeedController.php (If-Match guard)
- app/Http/Controllers/Api/V1/OfferController.php (If-Match guard)
- bootstrap/app.php (RequestId middleware + exception envelope)

### Tests
- +13 tests, +18 assertions
- Full suite: 791 tests / 1,901 assertions / 0 failures

### GAPs
- T03 (If-Match): RESOLVED
- T23 (request_id on auth errors): RESOLVED

---

## B37 — Sponsored Advertising implemented (full subsystem)

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L267

### Added
- 5 migrations (advertisers, ad_creatives, ad_campaigns, ad_deliveries, ad_events)
- 5 models + 5 factories
- AdDeliveryService + AdEventService
- AdsDeliveryController + AdsEventController + AdminAdsController
- resources/views/admin/sponsored-ads.blade.php (A023 full UI)
- tests/Feature/Ads/{AdDeliveryTest,AdEventTest,AdminAdsTest}.php (37 tests)

### Changed
- routes/api.php — +18 routes
- AdminReadController — A023 removed (graduated)
- AdminReadEndpointsTest / AdminScreensTest — A023 updated

### Tests
- +37 tests, +79 assertions
- Full suite: 827 / 1,963 / 0 failures / 1 skipped

### Feature
- Sponsored Advertising subsystem (governed, contract-compliant)
- Default: master OFF, placements OFF

---

## B38 — Admin browser login (Telegram-first, session-based)

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L268

### Added
- app/Http/Middleware/EnsureAdminRole.php
- app/Http/Controllers/Admin/Auth/AdminLoginController.php
- resources/views/admin/auth/login.blade.php
- tests/Feature/Admin/Auth/AdminLoginTest.php (15 tests)

### Changed
- routes/web.php: /admin/* now requires auth + admin
- bootstrap/app.php: admin alias + redirectGuestsTo
- layouts/admin.blade.php: server-side who + CSRF logout form
- lang/en.json + lang/am.json: +39 keys
- AdminScreensTest: admin auth setUp + count 25

### Tests
- +15 tests, +28 assertions
- Full suite: 842 / 1,991 / 0 failures / 1 skipped

### Feature
- Admin browser login (Telegram-first, session-based)
- 3 roles supported (MAIN_ADMIN / ADMIN / MODERATOR)

---

## B39 — Completion matrix + status report

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L269

### Added
- docs/reports/COMPLETION_MATRIX_20260930.md (276 lines)
- docs/reports/STATUS_REPORT_20260930.md (196 lines)

### Changed
- public/handoff/SOURCE_OF_TRUTH.md (HEAD + timestamp)

### Tests
- No change (845 / 1991 / 0 failures)

### Documentation
- Full screen-by-screen completion matrix
- Executive status report
- 11 external blockers documented with owners

---

## B40 — Admin Login: Telegram Widget integration (L268-followup)

**Date:** 2026-09-30
**Commit:** (this commit)
**Ref:** L268-followup

### Added
- POST /admin/login/telegram route
- AdminLoginController::telegramCallback()
- Telegram Widget embedded in login page

### Changed
- resources/views/admin/auth/login.blade.php
- .gitignore (env backup patterns)

### Tests
- No change (845 / 1991 / 0 failures)

### Verification
- Live at https://zagcreativity.com/admin/login
- BotFather domain: zagcreativity.com
- Login flow works (Telegram Widget -> session)

---

## B41 — Gap Analysis: Design Package vs Production

**Date:** 2026-10-01
**Commit:** (this commit)
**Ref:** L270

### Added
- docs/reports/GAP_ANALYSIS_20260930.md (404 lines)
  - Part 1: Capability inventory
  - Part 2: Screen-by-screen comparison (46 screens)
  - Part 3: Controls (55) + Requirements (212)
  - Part 4: THE GAP LIST (32 open + 18 resolved)
  - Part 5: Overall coverage
  - Part 6: Conclusion

### Changed
- public/handoff/SOURCE_OF_TRUTH.md (HEAD + timestamp + gap reference)

### Tests
- No change (845 / 1991 / 0 failures)

### Analysis
- 46/46 screens complete
- 55/55 controls complete
- 107/107 API endpoints complete
- 32 open gaps (11 critical external + 21 actionable)
- 18 gaps resolved this session

## L273 — GAP-FACT-01 status correction (OPEN → RESOLVED) — 2026-10-01

Verified 34/34 model factories exist with HasFactory trait. The gap was
already closed by L258 (7581e3d) and later additive work; the gap analysis
(L270) had not been refreshed. No code change.


## L274 — ADS-57 Traceability Matrix + ADS-61 Final Execution — 2026-10-01

NEW: docs/reports/ADS_TRACEABILITY_MATRIX_20261001.md (125 lines).
NEW: docs/reports/ADS_FINAL_EXECUTION_20261001.md (101 lines).
NEW: tests/Feature/Ads/SponsoredAdsTraceabilityTest.php (6 tests).

ADS-57 OPEN → RESOLVED.
ADS-61 OPEN → RESOLVED.
ADS-58 remains NOT STARTED (external, design owner).

Tests: 866 → 872 (+6). Assertions: 2088 → 2106. Failures: 0.

