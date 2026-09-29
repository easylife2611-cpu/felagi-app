# Spec Request — WP-05c: Admin Read Endpoints

**Date:** 2026-09-29
**Status:** PENDING STAKEHOLDER
**Type:** Specification request (not implementation)

## Context

B17 audit identified missing spec for WP-05c (admin read endpoints).
Evidence:
- B10 CHANGE_LOG mentioned "WP-05c admin read endpoints" without detail
- No corresponding Work Package definition exists
- Not registered in WORK_PACKAGES.md

## What We Know

- Admin write endpoints exist (WP-13, WP-13b): /api/v1/admin/changes/*
- Admin read endpoints for what entities? UNKNOWN
- Authorization pattern (SettingPolicy) exists — could be extended

## Questions for Stakeholder

1. Which admin resources need read endpoints?
   - Users? Needs? Offers? Payments? Audit logs?
2. What fields per resource? (LOCKED design source?)
3. Pagination strategy? (cursor vs offset)
4. Filter/sort/search requirements?
5. Field-level authorization (secret settings pattern)?
6. Response envelope (existing BaseApiController)?

## Related Requirements

- REQ-O* (Admin Boundaries) — 5 requirements, LOCKED
- REQ-AQ* (Admin Detailed Specs) — 38 requirements, LOCKED
- REQ-BP* (HTTP API) — 30 requirements, LOCKED

## Constitution Constraint

- UNKNOWN != MISSING: WP-05c is UNKNOWN, not MISSING
- Do not guess: no implementation without LOCKED spec
- Register as BLOCKED_ON_UNKNOWN until stakeholder responds

## Estimated Work (after spec)

| Component | Files | Tests |
|-----------|-------|-------|
| Controllers | 3-5 | — |
| Routes | 5-10 | — |
| Requests | 2-3 | — |
| Policies | 2-3 | — |
| Tests | — | 20-30 |

## Decision Required

Should WP-05c be:
(a) Implemented (needs spec)
(b) Deferred to next phase
(c) Removed (superseded)

## Escalation

- Product Owner: (UNKNOWN)
- Design Owner: (UNKNOWN)
- Release Owner: (UNKNOWN)
