# T01-T18 Definitions — Found in Design Package

**Generated:** 2026-09-30
**Purpose:** A — Resolve spec request `T01-T18_integration_tests.md`

## Resolution

Spec request was `PENDING STAKEHOLDER`. **Definitions FOUND** in
`~/felagi_extracted/Felagi_Design_Package/System_Specification/DFM-FDS-1.4.md`
(line 483–513).

**Status:** BLOCKED_ON_UNKNOWN → **EVIDENCE_AVAILABLE** ✅

## Full Test Matrix (T01-T31)

| ID | Scenario | Layer |
|---|---|---|
| T01 | Provider creates Offer on another's OPEN Need; self/dup/expired denied | API, policy, DB |
| T02 | Two simultaneous accepts → 1 award, 1 accepted Offer, 1 IN_PROGRESS; loser 409 | Concurrency, DB |
| T03 | Stale If-Match cannot overwrite; version increments once | API, DB |
| T04 | Offer edit after comparison cannot change snapshot; Compare Again = N+1 | Comparison, DB |
| T05 | Same 4 criteria/weights/vocab per Offer; no boost/arrival bias | AI fixture |
| T06 | Unknown facts labeled; no invented provider fact/score | AI adversarial |
| T07 | Malformed JSON, extra IDs, out-of-range, injection fail safe | AI validation |
| T08 | AI timeout/retry retains snapshot; exhaust → FAILED; retry once | Jobs, API |
| T09 | Completion creates 1 result set, 1 event, all notifications; retry no dup | DB, outbox |
| T10 | Provider A cannot fetch B's result/contact/attachment via guessed IDs | Security |
| T11 | Forged paid status, invalid sig, wrong amount, dup callback cannot dup boost | Payment |
| T12 | Pending callback during safe mode retained; activates once after recovery | Payment, recovery |
| T13 | Ratings require COMPLETED + correct pair; self/dup rejected | API, DB |
| T14 | Feature OFF blocks UI/API/job; history preserved | Admin, API, Flutter |
| T15 | Critical publish requires role + reauth + 2FA + reason + version + audit | Admin, security |
| T16 | Private executable upload, MIME spoof, traversal denied | File security |
| T17 | Signed Telegram login validates state/PKCE/nonce/issuer/audience/expiry | Auth |
| T18 | Queue crash + duplicate outbox → exactly 1 business effect | Reliability |
| T19 | Page limits, sort, rate limits, AI cost caps hold | Performance |
| T20 | Backup restore in isolated target meets RPO/RTO | Operations |
| T21 | Rollback after additive migration runs compatible release | Deployment |
| T22 | Flutter loading/empty/error/offline/permission + am/en a11y | Mobile |
| T23 | Request ID joins error/job/audit/admin view without secrets | Observability |
| T24 | Export PDF/XLSX/CSV/TXT matches stored version | Export |
| T25 | Late payment after FAILED → REVIEW_REQUIRED once | Payment |
| T26 | Offer edit at/after deadline denied; PENDING withdrawal OK | API, state |
| T27 | Unknown public group/expired permission rejected; channel/group paused | Telegram |
| T28 | Creation requires ack; preview excludes private fields; deep link current | Privacy |
| T29 | Dup events, 429 retry_after, permission loss → bounded queue | Queue |
| T30 | Cancel/stop halts queued; retained forwards reported | API, Admin |
| T31 | Distribution/boost don't change snapshot/criteria/scores | AI regression |

## Release Evidence Requirements

> "Release evidence must include automated results AND manual integration
> proof for actual Telegram identity, AI provider, selected payment sandbox,
> cPanel cron/queue, external backup and restore. A passing local unit test
> is not evidence that those external integrations work in production."

## Impact

- **WP-24 (T01-T18 integration tests):** Can be implemented
- **Blocker:** Some tests need live services (payment, Telegram, AI)
  → Those remain BLOCKED_ON_UNKNOWN per GAP-03
- **Runnable locally:** T01-T04, T07, T09, T10, T13, T14, T16, T17, T18, T23, T26
- **Needs external:** T05-T06, T08, T11-T12, T15, T19-T22, T24-T25, T27-T31

## Constitution Note

Per "UNKNOWN != MISSING": now documented as EVIDENCE_AVAILABLE.
Implementation of local-runnable subset can proceed.
