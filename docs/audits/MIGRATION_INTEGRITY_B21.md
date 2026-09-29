# Migration Integrity Audit — B21

**Date:** 2026-09-29
**Scope:** 36 migration files → 41 tables
**Type:** Read-only audit (no schema change)

## Executive Summary

All 36 migrations are consistent with model definitions. Five NOT NULL
columns without defaults were discovered and documented (D-112). No schema
change made per Constitution (LOCKED baseline).

## Migration Count

| Metric | Count |
|--------|-------|
| Migration files | 36 |
| Tables (SHOW TABLES) | 41 |
| Delta | +5 (Laravel defaults) |

## Laravel Default Tables (5)

1. cache (Laravel cache)
2. cache_locks (atomic locks)
3. sessions (session store)
4. migrations (migration tracking)
5. personal_access_tokens (Sanctum)

Conclusion: 36 app tables + 5 framework tables = 41 total. Matches.

## NOT NULL Columns Without Defaults (Documented in D-112)

| Table | Column | Model | Test Impact |
|-------|--------|-------|-------------|
| setting_versions | reason | SettingVersion | Explicit in test |
| audit_logs | request_id | AuditLog | Explicit in test |
| needs | category_id | Need | Test trait (CreatesTestCategory) |
| offers | offered_price | Offer | Explicit in test |
| offers | proposal_message | Offer | Explicit in test |

These are intentional: no defaults means the app must always supply values.

## Schema Constraints Discovered

| Constraint | Table | Test Impact |
|-----------|-------|-------------|
| UNIQUE (need_id, from_user_id, to_user_id) | ratings | Distinct providers per rating |
| UNIQUE (need_id, provider_id) | offers | One offer per provider per need |
| UNIQUE event_key | outbox_events | (tested) |

## B21 Test Coverage Achieved

| Model | Before B21 | After B21 |
|-------|------------|-----------|
| Notification | 0 | 11 |
| Rating | 0 | 9 |
| User | 12 (feature) | 11 (unit) |
| AuditLog | 0 | 8 |
| SettingVersion | 0 | 8 |
| OutboxEvent | 0 | 9 |

Total: +31 tests, +54 assertions.

## Constitution Compliance

- No hidden work: all findings documented
- No silent changes: no schema touched
- UNKNOWN != MISSING: constraints surfaced by tests
- AUDIT BEFORE ACTION: audit preceded fixes (D-054)
