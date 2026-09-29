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
