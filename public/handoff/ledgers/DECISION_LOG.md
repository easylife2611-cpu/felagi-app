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
