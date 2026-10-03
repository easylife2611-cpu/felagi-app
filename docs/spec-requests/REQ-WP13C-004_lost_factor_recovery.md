# Spec Request — REQ-WP13C-004: Lost-Factor Recovery Flow

**Date:** 2026-10-03 (L346-D)
**Status:** PENDING DESIGN OWNER
**Type:** Specification request (not implementation)
**Severity:** HIGH (per GAP-53)
**Reference:** Auth Contract §449, GAP-53, REQ-WP13C-004

## Context

WP-13c (2FA Enrollment UI) is DONE for REQ-WP13C-001/002/003
(enrollment, recovery codes display, self-service disable).
REQ-WP13C-004 (lost-factor recovery flow) remains **UNKNOWN** —
no LOCKED design spec exists.

LOCKED constraint (Auth Contract §449):
> "lost-factor recovery is a controlled, audited process, not a
> secret bypass."

A design proposal exists (`GAP-53_DESIGN_PROPOSAL_20261002.md`) but
requires design owner approval. This spec request formalizes the
6 open questions for design owner decision.

**This is NOT a duplicate of GAP-53 — it is the formal request to
the design owner to resolve the 6 open questions in GAP-53.**

## Requirements (from LOCKED sources)

| # | Requirement | Source |
|---|---|---|
| 1 | Controlled process (not self-service bypass) | Auth §449 |
| 2 | Audited (every step logged) | Auth §449 |
| 3 | No secret bypass (no plaintext reset) | Auth §449 |
| 4 | Admin involvement (Main Admin reauth required?) | Admin Auth Contract |
| 5 | Second-factor recovery only — not auth bypass | Auth §449 |
| 6 | Account-scoped (does not affect other users) | Constitution |
| 7 | Rate-limited | Auth §418 |
| 8 | Reauth within 5-min window | WP-13b |

## Open questions for design owner

| # | Question | Impact |
|---|---|---|
| 1 | Can Main Admin approve own recovery? (self-recovery) | Dual-control requirement |
| 2 | Is a second admin required for HIGH-risk? (dual control) | Workflow complexity |
| 3 | Time window between request and approval? | Auto-expiry duration |
| 4 | Recovery codes: reset all or keep remaining? | User re-enrollment UX |
| 5 | Lost recovery codes + lost TOTP: same flow? | Scope of REQ-WP13C-004 |
| 6 | Support channel: S021 only, or dedicated route? | UI surface area |

## What we can infer (tentatively)

| Hypothesis | Evidence | Confidence |
|---|---|---|
| Admin-mediated (not user self-service) | "controlled, audited" | HIGH |
| Reauth required for approval | Auth §449 + WP-13b | HIGH |
| User must re-enroll TOTP | "not a secret bypass" | HIGH |
| Notifications on both channels | Pattern in existing services | MEDIUM |
| Rate-limited per user + admin | Auth §418 + existing pattern | HIGH |

None of these are sufficient to implement without design owner
confirmation on the 6 questions above.

## Blocked components (until spec)

| # | Component | Status | Depends on |
|---|---|---|---|
| 1 | RecoveryTicket model | NOT STARTED | Q1, Q2, Q3 |
| 2 | Admin recovery endpoint | NOT STARTED | Q2, Q6 |
| 3 | TOTP reset service | PARTIAL (TotpService exists) | Q4, Q5 |
| 4 | Notification flow | EXISTS (OutboxEvent) | Q4 |
| 5 | Audit chain | EXISTS (AuditLog) | — |
| 6 | User recovery request UI | NOT STARTED | Q6 + D-097 |

## Recommended next steps

1. Design owner reviews `GAP-53_DESIGN_PROPOSAL_20261002.md` (already exists)
2. Design owner answers the 6 questions above
3. Design owner issues LOCKED spec
4. Developer implements backend (RecoveryTicket + endpoint + service)
5. Developer integrates with existing 2FA + audit + outbox
6. QA tests end-to-end with real admin session

## Constitution compliance

- UNKNOWN != MISSING: REQ-WP13C-004 preserved as UNKNOWN
- Do not guess: this is a request, not implementation
- No silent changes: every step cited to LOCKED source
- Append-only: existing GAP-53 proposal untouched
- No duplication: this formalizes GAP-53 for the requirement registry

## Related

- `docs/reports/GAP-53_DESIGN_PROPOSAL_20261002.md` (proposal)
- `docs/reports/STAKEHOLDER_DECISIONS_20261002.md` (Decision 4.2)
- `public/handoff/ledgers/OPEN_GAPS.md` (GAP-53)
- `public/handoff/ledgers/REQUIREMENT_REGISTRY.md` (REQ-WP13C-004)

**Recorded by:** L346-D
