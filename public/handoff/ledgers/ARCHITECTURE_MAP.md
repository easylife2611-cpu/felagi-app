# ARCHITECTURE MAP — Felagi v1.4.2
Last Updated: 2026-09-29

## Layer 0 — Governance
- `START_HERE.md`
- `README.md`
- `PACKAGE_MANIFEST.json`
- `DELIVERABLE_INDEX.json`
- `Release_Gates_and_Evidence.md` (11 canonical owners, 9 gates)
- 6-level authority hierarchy

## Layer 1 — Product Contracts (11 owners)
1. Master PDS — product identity/revenue/scope
2. `token_registry.json` — visual values
3. `screen-manifest.json` — screens/routes/state/API
4. Localization ARB — static microcopy
5. `Monetization_Payment_Specification.md`
6. `AI_Evaluation_Contract.md`
7. `Admin_Authorization_Contract.md`
8. `Sponsored_Advertising_Contract.md`
9. `Design_Integration_Contract.md`
10. `Final_Interaction_Contract.md`
11. `Release_Gates_and_Evidence.md`

## Layer 2 — Surfaces
- 23 user screens (S001-S023)
- 23 admin screens (A001-A023)
- 11 interaction overlays (O01-O11)
- 5 mobile tabs (Needs/My Needs/My Offers/Notifications/Profile)
- 26 journeys (13 main + 3 admin + 4 governed + 3 special + 3)

## Layer 3 — Design System (FGM-TOKENS-1.4)
- 14 token families
- 31 component primitives + 22 compositions
- 14 universal states
- 12 patterns
- 6 breakpoints (320/360/600/840/1200/1600)
- WCAG 2.2 AA target
- 9 canonical Amharic terms LOCKED

## Layer 4 — Runtime Targets
- **Flutter starter** (18 classes) — COMPILE BLOCKED
- **HTML preview** — fictional, no live APIs
- **25 MySQL/InnoDB tables**
- **~40 API routes** (base `/api/v1`)
- **Laravel + database queue + 1-min cron**

## Layer 5 — Evidence
- ✅ SOURCE: 7,810+ PASS, 66 contrast, 140 semantic
- ❌ RUNTIME: 0 (N06-N10)
- ❌ DEPLOYMENT: cPanel/PHP/MySQL unverified
- ❌ RPO/RTO: no timed drill

## Layer 6 — Admin Change Lifecycle (WP-13)
Added: 2026-09-29

### HTTP — /api/v1/admin/changes
- POST / → store (draft create)
- GET /{id} → show
- POST /{id}/validate → validateDraft
- POST /{id}/simulate → simulate
- POST /{id}/preview → preview
- POST /{id}/publish → publish
- GET /{id}/audit → audit trail
- POST /{id}/rollback → new draft from older version

### Service — AdminChangeService
- createDraft(): draft from key + value
- validateDraft(): type check + DFM §8.2 dependencies
- simulate(): before/after diff
- preview(): persist impact_preview
- publish(): DB transaction (5 writes)
- rollback(): new draft based on old version

### Supporting Services
- AuditWriter: SHA-256 hash chain (prev_hash + hash)
- OutboxWriter: aggregate_id = setting_versions.id (UUID)

### Publish Transaction (atomic)
1. setting_versions (immutable row)
2. settings (value + version_number++)
3. setting_drafts (status → PUBLISHED)
4. audit_logs (hash chain)
5. outbox_events (aggregate_type=setting_version)

### Data
- settings (PK=key VARCHAR) — current value + version_number
- setting_versions (append-only) — immutable history
- setting_drafts (DRAFT/VALIDATED/REJECTED/PUBLISHED)
- scheduled_settings (Phase 4 scheduling)
- audit_logs (append-only hash chain)
- outbox_events (aggregate_id UUID)

### Authorization — SettingPolicy
- view(): MAIN_ADMIN any; ADMIN non-secret only
- create(): ADMIN+
- update(): secret → MAIN_ADMIN only; else ADMIN+
- publish(): HIGH/CRITICAL → MAIN_ADMIN only; else ADMIN+
- rollback(): alias to publish()

### Exception — SettingsVersionConflictException
- Maps to HTTP 409
- Code: SETTINGS_VERSION_CONFLICT
- Payload: {setting_key, expected_version, actual_version}

### Seeder — ControlRegistrySeeder (31 settings)
- 7 FEATURE toggles (HIGH)
- 4 Telegram config (HIGH/MEDIUM)
- 3 Marketplace config (MEDIUM/HIGH)
- 9 AI config (HIGH/MEDIUM)
- 1 Payment provider (CRITICAL)
- 2 Uploads/Exports (MEDIUM)
- 2 Content (LOW)
- 2 Privacy (MEDIUM)
- 1 Safe Mode (CRITICAL)

### Test — ChangeLifecycleTest
- 8 tests, 15 assertions
- Uses RefreshDatabase (isolated MySQL test DB)
- Setup: seed ControlRegistrySeeder
