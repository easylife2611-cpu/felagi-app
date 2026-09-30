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
