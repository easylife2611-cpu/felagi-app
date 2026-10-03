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
| S003-PROFILE | Post-login Profile screen | DONE | IMPLEMENTATION_LEDGER L231 |
| S004-BROWSE | Browse Needs (full) | DONE | IMPLEMENTATION_LEDGER L232 |
| S005-CREATE | Create Need (full) | DONE | IMPLEMENTATION_LEDGER L233 |
| S008-DETAIL | Need Details (full) | DONE | IMPLEMENTATION_LEDGER L234 |
| S009-MY-NEEDS | My Needs (full) | DONE | IMPLEMENTATION_LEDGER L235 |
| S011-SUBMIT-OFFER | Submit Offer (full) | DONE | IMPLEMENTATION_LEDGER L236 |
| S010-RECEIVED-OFFERS | Received Offers (full) | DONE | IMPLEMENTATION_LEDGER L237 |
| S012-OFFER-DETAIL | Offer Detail (full) | DONE | IMPLEMENTATION_LEDGER L238 |
| S013-MY-OFFERS | My Offers (full) | DONE | IMPLEMENTATION_LEDGER |
| S018-NOTIFICATIONS | Notifications (full) | DONE | IMPLEMENTATION_LEDGER |
| S017-MESSAGES | Messages (full) | DONE | IMPLEMENTATION_LEDGER |
| S014-COMPARE | Compare Confirmation (full) | DONE | IMPLEMENTATION_LEDGER |
| S006-PUBLIC-PREVIEW | Public Preview (full) | DONE | IMPLEMENTATION_LEDGER |
| S007-CREATED | Need-Created (full) | DONE | IMPLEMENTATION_LEDGER |
| S015-COMPARISON-RESULT | AI Comparison Result (full) | DONE | IMPLEMENTATION_LEDGER |
| S016-COMPARISON-HISTORY | Comparison History (full) | DONE | IMPLEMENTATION_LEDGER |
| S019-BOOST | Boost (full) | DONE | IMPLEMENTATION_LEDGER |
| S020-RATING | Rating (full) | DONE | IMPLEMENTATION_LEDGER |
| S021-REPORT | Report/Support (full) | DONE | IMPLEMENTATION_LEDGER |
| S022-TELEGRAM | Telegram Publications (full) | DONE | IMPLEMENTATION_LEDGER |
| S023-UNLOCK | Offer Unlock (full) | DONE | IMPLEMENTATION_LEDGER |
| A001-A023 | Admin Screens (23, placeholders) | DONE | IMPLEMENTATION_LEDGER L252 |

---

## L345 Reconciliation — WP-24 + WP-13c Status (2026-10-03)

**Supersedes:**
- L39 (`WP-24 | BLOCKED | Felagi routes needed`)
- L44 (`WP-13c | DEFERRED | design unclear`)

### WP-24 — T01-T18 Integration Tests

| Field | Before | After |
|-------|--------|-------|
| Status | BLOCKED | **PARTIAL** |
| Reason | "Felagi routes needed" | Routes exist (47 API routes) |

**Evidence:**
- Routes: 47 registered under `/api/v1` (verified via `route:list`)
- T01-T31 matrix: `DFM-FDS-1.4.md` §13
- Local subset test: `tests/Feature/Integration/T01_T26LocalSubsetTest.php`
- **13 tests PASS** (T01, T02, T03, T04, T07, T09, T10, T13, T14, T17, T23, T26)
- 18 tests remain REQUIRES_EVIDENCE (external services)

**Remaining (blocked on external):**
T05, T06, T08, T11, T12, T15, T18, T19, T20, T21, T22, T24, T25, T27-T31

### WP-13c — 2FA Enrollment UI

| Field | Before | After |
|-------|--------|-------|
| Status | DEFERRED | **DONE (001-003), UNKNOWN (004)** |
| Reason | "design unclear" | Stack decided: Blade + vanilla JS |

**Evidence:**
- 8 API routes registered (`/api/v1/auth/2fa/*`)
- 1 web route: `/profile/2fa`
- Controllers: `TwoFactorController`, `TwoFactorWebController`
- Service: `app/Services/Auth/TwoFactorService.php`
- View: `resources/views/profile/2fa.blade.php` (TOTP + QR + recovery + disable)
- **28 tests PASS** (`tests/Feature/Auth/TwoFactorTest.php`)

**REQ Status:**

| Req | Description | Status |
|-----|-------------|--------|
| REQ-WP13C-001 | TOTP enrollment UI | ✅ COVERED |
| REQ-WP13C-002 | Recovery codes display UI | ✅ COVERED |
| REQ-WP13C-003 | Self-service 2FA disable | ✅ COVERED |
| REQ-WP13C-004 | Lost-factor recovery flow | 🔴 UNKNOWN |

**Remaining:** REQ-WP13C-004 requires stakeholder design decision (no
current spec). Marked UNKNOWN per "UNKNOWN != MISSING".

**Recorded by:** L345

---

## L346-D — WP-13c Spec Request Filed (2026-10-03)

**Supersedes:** Prior "REQ-WP13C-004 UNKNOWN" note in the L345 section.

### Status update

| Req | Before L346-D | After L346-D |
|-----|---------------|--------------|
| REQ-WP13C-001 | COVERED | COVERED |
| REQ-WP13C-002 | COVERED | COVERED |
| REQ-WP13C-003 | COVERED | COVERED |
| REQ-WP13C-004 | UNKNOWN | **UNKNOWN (spec request filed)** |

Status for 004 is intentionally unchanged: it is still UNKNOWN, but
a formal request now exists on the path to resolution.

### What L346-D delivered

| Artifact | Purpose |
|----------|---------|
| `docs/spec-requests/REQ-WP13C-004_lost_factor_recovery.md` | Formal 6-question spec request |
| `app/Contracts/LostFactorRecoveryInterface.php` | Empty marker interface (no methods) |
| Ledger cross-references | REQUIREMENT_REGISTRY + OPEN_GAPS updated |

### What L346-D did NOT deliver

- No backend implementation
- No method signatures on the interface
- No changes to existing 2FA / audit / outbox behavior

### Blocked components (unchanged by L346-D)

| Component | Status | Gate |
|-----------|--------|------|
| RecoveryTicket model | NOT STARTED | Design owner (Q1, Q2, Q3) |
| Admin recovery endpoint | NOT STARTED | Design owner (Q2, Q6) |
| TOTP reset service | PARTIAL | Design owner (Q4, Q5) |
| Notification flow | EXISTS (OutboxEvent) | — |
| Audit chain | EXISTS (AuditLog) | — |
| User recovery UI | NOT STARTED | D-097 (stack) |

### Next action

Design owner reviews `GAP-53_DESIGN_PROPOSAL_20261002.md` and the
new spec request; answers the 6 questions; issues LOCKED spec.
Developer implements backend only after LOCKED spec.

**Recorded by:** L346-D
