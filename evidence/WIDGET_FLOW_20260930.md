# Telegram Widget Flow — Implementation Evidence

- **Date:** 2026-09-30
- **Trigger:** BotFather "Web Login is currently unavailable" — OIDC not supported
- **Fix Type:** Architecture pivot (OIDC -> Widget flow)
- **Impact:** Login flow now functional

## Root Cause

- BotFather: "Web login is currently unavailable for Felagi @FelagiMarketBot"
- TELEGRAM_CLIENT_ID=8629327448 (numeric bot ID, not hex OIDC client_id)
- OIDC URL construction added bot_id + origin -> Telegram served Widget page
- Callback expected state+code (OIDC), but Widget sends hash+auth_date

## Fix

### Files Added
- app/Services/Auth/TelegramWidgetService.php — HMAC-SHA256 verification
- tests/Feature/Auth/TelegramWidgetTest.php — 12 tests, 39 assertions

### Files Modified
- app/Http/Controllers/Api/V1/AuthController.php — added telegramWidgetStart, telegramWidgetCallback
- routes/api.php — added 2 widget routes
- config/services.php — added bot_token, bot_username
- resources/views/welcome.blade.php — Widget JS integration
- .env — added TELEGRAM_BOT_TOKEN, TELEGRAM_BOT_USERNAME

### Preserved (Deferred, Not Deleted)
- app/Services/Auth/TelegramOidcService.php — kept for future OIDC availability
- OIDC routes (/auth/telegram/start, /auth/telegram/callback) — kept

## Tests

| Suite | Tests | Assertions | Status |
|-------|-------|------------|--------|
| Widget (new) | 12 | 39 | PASS |
| Full suite | 424 | 730 | PASS |

### Widget Test Coverage
- Valid signature accepted
- Tampered hash rejected
- Tampered first_name rejected
- Expired auth_date rejected
- Missing bot_token rejected
- User upsert (create + update)
- Widget start config returned
- Widget start validates return_uri
- Full callback flow (verify -> user -> handoff)
- Invalid hash rejected (401)
- Missing state rejected (400)

## HMAC Verification Details

Per Telegram docs (core.telegram.org/widgets/login):
1. Build data_check_string from only signed fields: id, first_name, last_name, username, photo_url, auth_date (sorted alphabetically, joined by newline)
2. secret_key = SHA256(bot_token) (raw bytes)
3. expected_hash = HMAC_SHA256(data_check_string, secret_key)
4. hash_equals(received, expected)
5. Verify auth_date within WIDGET_MAX_AGE_SECONDS (300s)

Important: Exclude hash (signature) AND state (our CSRF token, not Telegram-signed).

## Constitution Compliance

- [x] No destructive change — OIDC code preserved (DEFERRED)
- [x] Additive — Widget new path
- [x] No hidden work — full incident + fix documented
- [x] IMPLEMENTED != VERIFIED — 2nd reviewer pending
- [x] Rollback: git revert <commit> (keeps OIDC if needed)

## Rollback

    git revert <WIDGET-commit>
    # OR manual:
    # - Remove widget routes from routes/api.php
    # - Remove widget methods from AuthController
    # - Revert welcome.blade.php to previous
    # - Revert config/services.php

## Status

- IMPLEMENTED (yes)
- INTEGRATED (yes)
- TESTED (yes — 12/12 + 424/424)
- VERIFIED (pending 2nd reviewer)
- DOCUMENTED (this file)
- EVIDENCED (WIDGET_phpunit_*.log, WIDGET_fullsuite_*.log)

Deploy pending.
