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
