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
