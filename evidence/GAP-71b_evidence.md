# GAP-71b Evidence — Attachment Factory + HasFactory

- **Type:** Infrastructure (test factories)
- **Date (UTC):** 2026-09-30T06:50:00Z
- **Operator:** zagcreht
- **Commit before:** 4285ad1 (QUALITY_DONE)

## Files Added / Changed

| File | Change |
|------|--------|
| database/factories/AttachmentFactory.php | NEW (74 lines) |
| app/Models/Attachment.php | HasFactory trait added (+2 lines) |

## Factory Features

### `definition()` default
- uploaded_by => User::factory()
- need_id / offer_id / message_id => null
- purpose => PROFILE
- storage_disk => 'local'
- storage_key => 'attachments/<uuid>.pdf'
- original_name => 'document.pdf'
- detected_mime => 'application/pdf'
- byte_size => 12345
- sha256 => hash('sha256', random)
- visibility => PRIVATE
- scan_status => PENDING

### States
- `clean()` — scan_status=CLEAN
- `rejected()` — scan_status=REJECTED
- `publicVisibility()` — visibility=PUBLIC
- `forNeed(Need $need)` — need_id + purpose=NEED

## Smoke Test (tinker)

- Factory created UUID successfully
- purpose=PROFILE, scan_status=PENDING, visibility=PRIVATE, sha256=64 chars
- clean() state works (scan_status=CLEAN)
- publicVisibility() state works (visibility=PUBLIC)

## Full Suite Regression

- 412 tests, 691 assertions, 0 failures
- AttachmentTest: 21 tests, 33 assertions (unchanged)

## Not Changed

- No production logic changed (trait addition only)
- No migrations, no routes, no design
- Existing tests unchanged

## Rollback

git revert HEAD  # removes factory + trait

## Constitution Compliance

- [x] No destructive change
- [x] No breaking change (additive infrastructure)
- [x] No silent changes

## DONE definition

- [x] IMPLEMENTED
- [x] INTEGRATED (tinker smoke test)
- [x] TESTED (412/412)
- [ ] VERIFIED (2nd reviewer required)
- [x] DOCUMENTED
- [x] EVIDENCED
