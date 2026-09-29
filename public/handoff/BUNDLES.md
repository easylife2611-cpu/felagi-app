# Felagi v1.4.2 — Bundle Index

**Generated:** 20260929-1419
**Tag:** B15_DONE

**Git state:**
- felagi_app:       `ad389a9` (46 commits)
- felagi_extracted: `27edd9d` (15 commits)

## Active Bundles

| File | Content |
|------|---------|
| `Felagi_App_v1.4.2_20260929-1419_B15_DONE.bundle` | felagi_app git |
| `Felagi_Design_v1.4.2_20260929-1419_B15_DONE.bundle` | felagi_extracted git |
| `Felagi_v1.4.2_20260929-1419_B15_DONE_full.tar.gz` | Lean source (composer install required) |
| `Felagi_v1.4.2_20260929-1419_B15_DONE_FULL_with_vendor.tar.gz` | Full source with vendor (runnable) |

## Restore

### App bundle
    git clone Felagi_App_v1.4.2_20260929-1419_B15_DONE.bundle felagi_app
    cd felagi_app && composer install
    cp .env.example .env && php artisan key:generate
    php artisan migrate

### Design bundle
    git clone Felagi_Design_v1.4.2_20260929-1419_B15_DONE.bundle felagi_extracted

### Lean tarball
    tar -xzf Felagi_v1.4.2_20260929-1419_B15_DONE_full.tar.gz && cd felagi_app && composer install

### Full tarball
    tar -xzf Felagi_v1.4.2_20260929-1419_B15_DONE_FULL_with_vendor.tar.gz && cd felagi_app

## Completed WPs

- WP-13:  Admin Change Lifecycle
- WP-13b: Reauth + TOTP 2FA + Idempotency
- WP-05a: Auth Attempts + PKCE OIDC
- WP-05b: Telegram Foundation
- WP-27:  Telegram OIDC Full Flow
- WP-27b: HMAC-Signed User Binding (D-091 fix)
- GAP-54: APP_DEBUG=false
- WP-05d: Cleanup .bak files
- **B10:   Constitution Compliance Block (8 data integrity fixes)**

## Tests

**90 PASS** (190 assertions) — MySQL test DB isolated

## B10+B11+B12 Changes

- WORK_PACKAGES.md: WP-05/22/27 status deduped; statistics 8→10 DONE; WP-13c added DEFERRED
- OPEN_GAPS.md: GAP-07→GAP-60; GAP-42/46/56 literal duplicates cleaned
- HANDOFF_STATE.md: Next Priority refreshed; path corrected
- PRIORITY_PLAN.md: TIER 1/2/3 aligned
- IMPLEMENTATION_LEDGER: L188 CCB entry
- DECISION_LOG: D-096 (T01-T18 UNKNOWN), D-097 (WP-13c UNKNOWN)
