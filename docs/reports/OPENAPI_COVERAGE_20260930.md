# OpenAPI Coverage — Felagi v1.4.2

**Generated:** 2026-09-30
**Purpose:** D — Document endpoint surface (no full spec yet)

## Summary

| Category | Count |
|---|---|
| User (S001-S023) | 20+ endpoints |
| Admin write (WP-13/13b) | 15+ endpoints |
| Admin read (WP-05c) | 23 endpoints |
| Telegram read (WP-05b) | 4 endpoints |
| Health | 1 endpoint |
| **Total** | **~63 endpoints** |

## Public Endpoints (no auth)

| Method | Path | Handler |
|---|---|---|
| GET | `/api/health` | HealthController |

## Auth (Sanctum)

| Method | Path | Handler |
|---|---|---|
| POST | `/api/v1/auth/telegram/start` | AuthController@telegramStart |
| POST | `/api/v1/auth/telegram/widget/start` | AuthController@telegramWidgetStart |
| GET | `/api/v1/auth/telegram/widget/callback` | AuthController@telegramWidgetCallback |

## User Endpoints

See `public/handoff/SOURCE_OF_TRUTH.md` → "API Mapping Coverage" section
for the complete per-screen mapping.

## Admin Read (WP-05c)

23 endpoints under `/api/v1/admin/*` — see `docs/specs/WP-05c_LOCKED.md`.

## Full OpenAPI 3.0 Spec

**Status:** DEFERRED to next session
**Reason:** Requires ~60 min for accurate spec generation
**Prerequisite:** All endpoints stable (blocked: WP-10, WP-13c)

## Recommended Next Step

When WP-10 and WP-13c unblock:
1. Generate OpenAPI 3.0 from Laravel routes
2. Validate with `swagger-cli`
3. Publish to `/public/downloads/openapi.yaml`
4. Add Swagger UI route `/api/docs`
