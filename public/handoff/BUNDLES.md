# Felagi v1.4.2 — Bundle Index

**Generated:** 20260929-1305
**Tag:** WP27b_DONE

**Git state:**
- felagi_app:       `4d32a93` (34 commits)
- felagi_extracted: `2c18b54` (14 commits)

## Active Bundles

| File | Content |
|------|---------|
| `Felagi_App_v1.4.2_20260929-1305_WP27b_DONE.bundle` | felagi_app git |
| `Felagi_Design_v1.4.2_20260929-1305_WP27b_DONE.bundle` | felagi_extracted git |
| `Felagi_v1.4.2_20260929-1305_WP27b_DONE_full.tar.gz` | Lean source (composer install required) |
| `Felagi_v1.4.2_20260929-1305_WP27b_DONE_FULL_with_vendor.tar.gz` | Full source with vendor (runnable) |

## Restore

### App bundle
    git clone Felagi_App_v1.4.2_20260929-1305_WP27b_DONE.bundle felagi_app
    cd felagi_app && composer install
    cp .env.example .env && php artisan key:generate
    php artisan migrate

### Design bundle
    git clone Felagi_Design_v1.4.2_20260929-1305_WP27b_DONE.bundle felagi_extracted

### Lean tarball
    tar -xzf Felagi_v1.4.2_20260929-1305_WP27b_DONE_full.tar.gz && cd felagi_app && composer install

### Full tarball
    tar -xzf Felagi_v1.4.2_20260929-1305_WP27b_DONE_FULL_with_vendor.tar.gz && cd felagi_app

## Completed WPs

- WP-13:  Admin Change Lifecycle
- WP-13b: Reauth + TOTP 2FA + Idempotency
- WP-05a: Auth Attempts + PKCE OIDC
- WP-05b: Telegram Foundation
- WP-27:  Telegram OIDC Full Flow
- WP-27b: HMAC-Signed User Binding (D-091 fix)
- GAP-54: APP_DEBUG=false
- WP-05d: Cleanup .bak files

## Tests

**88 PASS** (188 assertions) — MySQL test DB isolated
