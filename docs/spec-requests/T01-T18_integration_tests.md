# Spec Request — T01-T18: Integration Test Definitions

**Date:** 2026-09-29
**Status:** PENDING STAKEHOLDER
**Type:** Specification request (not implementation)

## Context

WP-24 (T01-T18 Integration Tests) has been BLOCKED since initial audit.
The blocker was originally "Felagi routes needed" — now RESOLVED (47 routes
exist at WP-27b). But T01-T18 test definitions remain UNKNOWN.

## Evidence

- TEST_VERIFICATION.md: "All 18 tests REQUIRES_EVIDENCE — need live stack"
- No assertions, no acceptance criteria, no test matrix
- WORK_PACKAGES.md refers to T01-T18 without definition
- D-096: Marked as BLOCKED-ON-UNKNOWN

## Questions for Stakeholder

1. What are T01-T18? (Test IDs? Test scenarios? Acceptance cases?)
2. Per test: what is the input, expected output, acceptance criterion?
3. Which components does each test touch?
   - API only? Full stack? Real services?
4. Are these tests runnable in the current environment?
5. Do they require external services (Telegram, AI, Payment)?
6. Sequencing: any dependencies between tests?

## What We Can Infer (Tentatively)

| Hypothesis | Evidence | Confidence |
|-----------|----------|------------|
| Integration tests | Name suggests | HIGH |
| 18 tests | T01-T18 numbering | HIGH |
| Full stack | "live stack" reference | MEDIUM |
| External deps | "REQUIRES_EVIDENCE" | MEDIUM |

None of these are sufficient to implement without confirmation.

## Related Requirements

- REQ-J* (Test Protocols) — 12 requirements, LOCKED
- REQ-BJ* (Test Matrix) — 18 requirements, LOCKED (suspicious match)
- REQ-BL* (Acceptance Cases) — 8 requirements, LOCKED

## Constitution Constraint

- UNKNOWN != MISSING: WP-24 is UNKNOWN, not MISSING
- Do not guess: no implementation without LOCKED spec
- D-096: Do not guess missing requirements

## Estimated Work (after spec)

| Component | Files | Tests |
|-----------|-------|-------|
| Test classes | 18+ | 18 |
| Fixtures/seeders | 5-10 | — |
| Test DB setup | 1-2 | — |
| Documentation | 1-2 | — |

Total: potentially 30-50 files, ~18 tests.

## Decision Required

Should WP-24 (T01-T18) be:
(a) Implemented (needs spec)
(b) Deferred to next phase
(c) Removed (superseded)

## Resolution Path

Stakeholder provides T01-T18 spec (test names, behaviors, criteria).
Then: WP-24 can be unblocked and estimated.

## Escalation

- Product Owner: (UNKNOWN)
- Design Owner: (UNKNOWN)
- Release Owner: (UNKNOWN)

## Related UNKNOWNs

- U-21: B-Blocks vs Work Packages formal relationship
- D-096: T01-T18 definitions UNKNOWN
- D-097: WP-13c frontend stack UNKNOWN
