# WP-B27 Evidence — Safety/Marketplace Domain Test Suite

- **Requirements:** R-TEST-03 (Category), R-TEST-04 (NeedAward), R-TEST-05 (Attachment + Report)
- **Date (UTC):** $(date -u +%Y-%m-%dT%H:%M:%SZ)
- **Operator:** zagcreht
- **Commit before:** $(git rev-parse HEAD)
- **Branch:** master

## Files created

- tests/Feature/Models/CategoryTest.php (15 tests)
- tests/Feature/Models/NeedAwardTest.php (13 tests)
- tests/Feature/Models/AttachmentTest.php (21 tests)
- tests/Feature/Models/ReportTest.php (17 tests)

## Test results

- **B27 tests:** 66 (99 assertions) — 100% pass
- **Full suite:** 284 → 350 tests (496 → 595 assertions)
- **Runtime:** 3.04s (Attachment only), ~XXs (full suite)
- **PHP:** 8.2.33
- **PHPUnit:** 11.5.56

## Coverage

- **Category (15 tests)**
  - UUID, casts (active bool, sort_order int), defaults (active=true, sort_order=0)
  - Relations: creator (nullable), needs (hasMany)
  - Scopes: active(), ordered() (sort_order + slug tiebreaker)
  - Helper: name($locale) — am default, en override
  - Unique: slug; max length 60
- **NeedAward (13 tests)**
  - No incrementing, no timestamps
  - PK = need_id (string/uuid)
  - Relations: need, offer, acceptedBy
  - Casts: accepted_at datetime
  - Composite PK (need_id) blocks duplicates
  - offer_id UNIQUE constraint
  - Composite FK (offer_id, need_id) → offers(id, need_id) blocks cross-need offers
- **Attachment (21 tests)**
  - UUID, no timestamps, SoftDeletes
  - Relations: uploader, need (nullable), offer (nullable), message (nullable)
  - Casts: byte_size int, created_at datetime
  - Constants: PURPOSE_*, VISIBILITY_*, SCAN_*
  - Defaults: visibility=PRIVATE, scan_status=PENDING
  - Scopes: clean()
  - Helper: isScanClean() (renamed from isClean — see GAP-71)
  - SHA-256 64-char storage
  - Soft delete preserves row
- **Report (17 tests)**
  - UUID, timestamps enabled
  - Relations: reporter, assignee (nullable)
  - Nullable: details, resolution_code, assigned_to
  - Constants: ENTITY_*, STATUS_*, REASON_*
  - Default: status=OPEN
  - Scopes: open()
  - entity_id polymorphic (no FK) — multiple reports allowed

## Schema findings

- **Production bug found (GAP-71):** Attachment::isClean() collides with Laravel Model::isClean()
  - Fixed with approval: isClean → isScanClean (breaking change)
  - No callers existed — zero runtime impact
  - See GAP-71 for full details
- NeedAward composite PK — verified working
- NeedAward composite FK (offer_id, need_id) — verified working
- Report entity_id is polymorphic-style (no FK)

## R-TEST status

- R-TEST-03 (Category): 0 → 15 tests → **COVERED**
- R-TEST-04 (NeedAward): 0 → 13 tests → **COVERED**
- R-TEST-05 (Attachment + Report): 0 → 38 tests → **COVERED**

## Rollback

Test files: `rm tests/Feature/Models/{CategoryTest,NeedAwardTest,AttachmentTest,ReportTest}.php`
Production fix: `git revert <B27-commit> -- app/Models/Attachment.php`

## Known risks / remaining gaps

- No factories for Category/NeedAward/Attachment/Report (same as B25/B26 pattern)
- GAP-71 production fix (isClean→isScanClean) — verify no external callers in future
- Coverage report not generated

## Constitution compliance

- [x] Production code changed — WITH EXPLICIT APPROVAL (GAP-71 fix)
- [x] Breaking change documented in CHANGE_LOG
- [x] No hidden work
- [x] UNKNOWN markers resolved before coding
- [x] Rollback documented
- [x] Evidence captured

## DONE definition checklist

- [x] IMPLEMENTED — 4 test files + 1 production fix
- [x] INTEGRATED — migrate:fresh --env=testing
- [x] TESTED — 66/66 pass; full suite 350/350 pass
- [ ] VERIFIED — requires second reviewer
- [x] DOCUMENTED — this file
- [x] EVIDENCED — WP-B27_phpunit_${TS}.log + WP-B27_fullsuite_${TS}.log
