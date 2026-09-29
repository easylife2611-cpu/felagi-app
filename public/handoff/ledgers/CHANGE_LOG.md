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
