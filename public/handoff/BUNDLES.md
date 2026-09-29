# Felagi v1.4.2 — Bundle Index

**Generated:** 20260929-1205
**Last Tag:** WP05a_DONE
**Latest commits:**
- felagi_app:     3ad9c84 (WP-05a: auth_attempts + PKCE)
- felagi_extracted: e74741b (cleanup: remove .bak from tracking)

## Active Bundles

| File | Size | Content |
|------|------|---------|
| `Felagi_App_v1.4.2_20260929-1205_WP05a_DONE.bundle` | 205K | felagi_app git (25 commits) |
| `Felagi_Design_v1.4.2_20260929-1205_WP05a_DONE.bundle` | 3.5M | felagi_extracted git (10 commits) |
| `Felagi_v1.4.2_20260929-1205_WP05a_DONE_full.tar.gz` | 151K | felagi_app full (no vendor) |

## Restore Instructions

### App bundle
```bash
git clone Felagi_App_v1.4.2_20260929-1205_WP05a_DONE.bundle felagi_app_restored
cd felagi_app_restored
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Design bundle
```bash
git clone Felagi_Design_v1.4.2_20260929-1205_WP05a_DONE.bundle felagi_extracted_restored
```

### Full tarball
```bash
tar -xzf Felagi_v1.4.2_20260929-1205_WP05a_DONE_full.tar.gz
cd felagi_app
composer install
```

## Completed Work Packages

- ✅ WP-13: Admin Change Lifecycle (7b97c36)
- ✅ WP-13b: Reauth + TOTP 2FA + Idempotency (679f8f8)
- ✅ WP-05a: Auth Attempts + PKCE (3ad9c84)
- ✅ GAP-54: APP_DEBUG=false (baf007a)
- ✅ WP-05d: Cleanup .bak files (3aa4547)

## Test Status

- **50 tests PASS** (97 assertions)
- Duration: ~5 seconds
- Test DB: MySQL isolated (zagcreht_felagi_test)
