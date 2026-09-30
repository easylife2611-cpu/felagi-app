# WP-B28 Evidence — Settings/Role Domain Test Suite

- **Requirements:** R-TEST-06 (Setting + SettingDraft), R-TEST-07 (UserRole)
- **Date (UTC):** $(date -u +%Y-%m-%dT%H:%M:%SZ)
- **Operator:** zagcreht
- **Commit before:** $(git rev-parse HEAD)
- **Branch:** master

## Files created
- tests/Feature/Models/SettingTest.php (25 tests)
- tests/Feature/Models/SettingDraftTest.php (20 tests)
- tests/Feature/Models/UserRoleTest.php (17 tests)

## Test results
- **B28 tests:** 62 (96 assertions) — 100% pass
- **Full suite:** 350 → 412 tests (595 → 691 assertions)
- **Runtime:** ~9s (B28 only), 14.28s (full suite)
- **PHP:** 8.2.33 | PHPUnit: 11.5.56

## Coverage

### Setting (25 tests)
- PK = key (string), no id, no incrementing, no timestamps auto
- Casts: value_json/default_json/schema_json arrays, is_secret bool, version_number int, updated_at datetime
- Relations: versions, drafts, updatedBy (nullable)
- Constants: GROUP_*, RISK_*, TYPE_*
- Defaults: is_secret=false, version_number=1
- Helpers: requiresReauth() (HIGH+CRITICAL), requiresSecondFactor() (CRITICAL only)

### SettingDraft (20 tests)
- UUID, timestamps enabled
- Relations: setting, proposer
- Casts: proposed_value_json/validation_report/impact_preview arrays
- Nullable: validation_report, impact_preview
- Constants: STATUS_DRAFT/VALIDATED/REJECTED/PUBLISHED
- Default status=DRAFT
- Helper: isEditable() (DRAFT+VALIDATED)
- FK setting_key must exist (verified)
- Multiple drafts per setting allowed

### UserRole (17 tests)
- No incrementing, no timestamps, composite PK (user_id, role)
- Relations: user, grantedBy (nullable)
- Casts: granted_at, revoked_at
- Constants: ROLE_MAIN_ADMIN/ADMIN/MODERATOR
- Scope: active() (revoked_at null)
- Composite PK prevents duplicates
- Same user can have multiple roles
- User scope withRole() filters active only

## Schema findings
- settings uses UPDATED_AT constant (no created_at)
- setting_drafts FK setting_key → settings.key verified
- user_roles uses granted_at useCurrent — model doesn't auto-populate
  (test uses DB fetch via where()->first())
- UserRole has NO $primaryKey set (composite) — avoid fresh()/find()

## R-TEST status
- R-TEST-06 (Setting + SettingDraft): PARTIAL → **COVERED**
- R-TEST-07 (UserRole): 0 → **COVERED**

## Rollback
Test files only: `rm tests/Feature/Models/{SettingTest,SettingDraftTest,UserRoleTest}.php`

## Known risks / remaining gaps
- No factories for Setting/SettingDraft/UserRole (same pattern as B25-B27)
- Coverage report not generated

## Constitution compliance
- [x] No production code changed (tests only)
- [x] UNKNOWN markers resolved before coding
- [x] No silent changes
- [x] Rollback documented
- [x] Evidence captured

## DONE definition checklist
- [x] IMPLEMENTED — 3 test files
- [x] INTEGRATED — migrate:fresh --env=testing
- [x] TESTED — 62/62 pass; full suite 412/412 pass
- [ ] VERIFIED — requires second reviewer
- [x] DOCUMENTED — this file
- [x] EVIDENCED — WP-B28_phpunit_20260930_062707.log + WP-B28_fullsuite_20260930_062707.log
