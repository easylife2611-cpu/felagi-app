# CREDENTIAL REQUEST — Felagi Production Blockers

**Generated:** 2026-10-10
**HEAD:** d178587
**Purpose:** Request external credentials to unblock 4 premium screens
**Status:** 23/23 premium UI complete — 4 screens await credentials

---

## Summary

| ID | Blocker | Owner | Screens | Priority |
|---|---|---|---|---|
| B1 | Payment (Stripe/Chapa) | Finance | S019, S023 | CRITICAL |
| B2 | AI (Gemini) | Product | S015 | CRITICAL |
| B3 | Telegram Bot Token | Product | S022 | HIGH |
| B4 | Ad Server | Marketing | (A022) | MEDIUM |
| B5 | Telemetry (Sentry) | Infra | G09 | MEDIUM |

**Impact:** 4 user screens await credentials. All UI + logic complete.

---

## Impact Matrix

| Credential | Unblocks | User Impact | Production Gate |
|---|---|---|---|
| B1 Payment | S019 Boost, S023 Offer Unlock | Users can pay for boost + unlock offers | G06 Partial |
| B2 AI | S015 Comparison Result | AI compares offers | G06 Partial |
| B3 Telegram | S022 Telegram Publications | Users post to Telegram channels | G06 Partial |
| B4 Ads | A022 Sponsored Ads | Admin can manage ad campaigns | G07 |
| B5 Telemetry | G09 Observability | Production monitoring | G09 |


---

## B1 — Payment Provider (CRITICAL)

**Purpose:** Enable paid Boost + Offer Unlock features

**Requested from:** Finance / Product

**What we need:**
- Provider choice: Chapa (Ethiopia) or Stripe (International)
- API Secret Key (production)
- Public/Publishable Key (production)
- Webhook Signing Secret
- Merchant ID / Account ID
- Sandbox credentials (for testing first)

**Environment variables (.env):**
```
PAYMENT_PROVIDER=chapa|stripe
CHAPA_SECRET_KEY=sk_live_xxx
CHAPA_WEBHOOK_SECRET=whsec_xxx
STRIPE_SECRET_KEY=sk_live_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
```

**Blocks:** S019 Boost, S023 Offer Unlock

**Impact:** Users cannot pay for premium features. Revenue blocked.

**Timeline if unblocked:** 1-2 days (integration + test)

**Contact:** Finance lead

---

## B2 — AI Provider (CRITICAL)

**Purpose:** Enable AI-powered offer comparison

**Requested from:** Product / Engineering

**What we need:**
- Provider: Google Gemini (recommended)
- API Key (production)
- Model name (e.g., gemini-1.5-pro)
- Rate limits / quota info
- Sandbox API key (for testing)

**Environment variables (.env):**
```
AI_PROVIDER=gemini
GEMINI_API_KEY=AIza_xxx
GEMINI_MODEL=gemini-1.5-pro
```

**Blocks:** S015 Comparison Result

**Impact:** Users cannot compare offers with AI assistance. Core feature missing.

**Timeline if unblocked:** 1 day (API already integrated, just key needed)

**Contact:** Product lead

---

## B3 — Telegram Bot Token (HIGH)

**Purpose:** Enable Telegram channel publications

**Requested from:** Product

**What we need:**
- Bot Token (from BotFather)
- Bot Username (already: @FelagiMarketBot)
- Bot ID (already: 8629327448)
- Channel ID(s) for publications
- Webhook Secret (for updates)
- Admin access to channel(s)

**Environment variables (.env):**
```
TELEGRAM_BOT_TOKEN=123456:ABC-xxx
TELEGRAM_CHANNEL_ID=-1001234567890
TELEGRAM_WEBHOOK_SECRET=xxx
```

**Blocks:** S022 Telegram Publications

**Impact:** Users cannot post needs to Telegram channels. Distribution blocked.

**Timeline if unblocked:** 1-2 days (integration + test)

**Contact:** Product lead

---

## B4 — Ad Server (MEDIUM)

**Purpose:** Enable sponsored ads (admin A022)

**Requested from:** Marketing

**What we need:**
- Ad network choice (Google Ads / Custom)
- Publisher ID
- Ad unit IDs (banner, interstitial)
- API credentials (if any)

**Environment variables (.env):**
```
ADS_PROVIDER=google|custom
ADS_PUBLISHER_ID=pub-xxx
ADS_API_KEY=xxx
```

**Blocks:** A022 Sponsored Ads (admin)

**Impact:** Admin cannot manage ad campaigns. Monetization partial.

**Timeline if unblocked:** 2-3 days

**Contact:** Marketing lead

---

## B5 — Telemetry (MEDIUM)

**Purpose:** Enable production observability (G09)

**Requested from:** Infra

**What we need:**
- Provider: Sentry (recommended) or OpenTelemetry
- DSN (Data Source Name)
- Environment name (production)
- Organization slug

**Environment variables (.env):**
```
SENTRY_LARAVEL_DSN=https://xxx@sentry.io/xxx
SENTRY_TRACES_SAMPLE_RATE=0.1
SENTRY_ENVIRONMENT=production
```

**Blocks:** G09 Observability gate

**Impact:** No production monitoring. Cannot diagnose issues.

**Timeline if unblocked:** 1 day (package install + config)

**Contact:** Infra lead

---

## Next Steps

### For Requesters

1. Review the specific requirements above
2. Provide credentials securely (not via plain email)
3. Use `https://zagcreativity.com/secure-form` or encrypted channel
4. Notify dev team when credentials are ready

### For Dev Team

1. Update `.env` with received credentials
2. Run: `php artisan config:clear && php artisan optimize:clear`
3. Test each screen: S015, S019, S022, S023
4. Update DIRECTOR_GATE_CHECKLIST.md status
5. Push changes to origin

### Priority Order

1. **B1 (Payment)** — highest revenue impact
2. **B2 (AI)** — core feature
3. **B3 (Telegram)** — distribution channel
4. **B4 (Ads)** — secondary monetization
5. **B5 (Telemetry)** — production safety

---

## Reference

- Director Gate: `public/handoff/DIRECTOR_GATE_CHECKLIST.md`
- Handoff State: `public/handoff/HANDOFF_STATE.md`
- Master Handoff: `public/handoff/FELAGI_MASTER_HANDOFF.md` (v1.9)
- SOT: `public/handoff/SOURCE_OF_TRUTH.md`
- Full Report: `storage/app/private/handoff_ledgers/L366_PREMIUM_COMPLETION_REPORT.md`
- GitHub: https://github.com/easylife2611-cpu/felagi-app/tree/d178587

---

**Generated by:** L366 (2026-10-10)

