# ENVIRONMENT CHECKLIST — Felagi v1.4.2
Last Verified: 2026-09-29

## Local Development Tools
| # | Resource | Required | Found | Status |
|---|----------|----------|-------|--------|
| 1.1 | Flutter SDK | >= 3.22 | NOT FOUND | ❌ |
| 1.2 | Dart SDK | >= 3.4 | NOT FOUND | ❌ |
| 1.3 | Node.js | >= 18 LTS | NOT FOUND | ❌ |
| 1.4 | Python 3 | >= 3.10 | 3.6.8 | ❌ TOO OLD |
| 1.5 | Git | >= 2.30 | not checked | ⏳ |
| 1.6 | Chromium/Firefox | Latest | NOT FOUND | ❌ |
| 1.7 | Text Editor/IDE | Any | available | ✅ |
| 1.8 | Terminal/Shell | bash/zsh | bash | ✅ |

## Server (cPanel)
| # | Resource | Required | Found | Status |
|---|----------|----------|-------|--------|
| 2.1 | cPanel access | Full | Yes | ✅ |
| 2.2 | SSH/terminal | Yes | Yes | ✅ |
| 2.3 | Doc root ability | Set public/ | NOT VERIFIED | ⏳ |
| 2.4 | PHP | 8.2+ | 8.2.33 | ✅ |
| 2.5 | PHP extensions | Full Laravel set | Full set | ✅ |
| 2.6 | MySQL/MariaDB | 8.0+/10.5+ | 10.6.28 | ✅ |
| 2.7 | Composer | Latest | 2.8.12 | ✅ |
| 2.8 | Cron 1-min | Yes | NOT VERIFIED | ⏳ |
| 2.9 | Outbound HTTPS | Yes | Working | ✅ |
| 2.10 | Disk space | >= 5GB | 51 GB | ✅ |
| 2.11 | Backup target | Off-host | NOT CONFIGURED | ❌ |

## Telegram Credentials
| # | Resource | Status |
|---|----------|--------|
| 3.1 | OIDC Client ID | ❌ |
| 3.2 | OIDC Client Secret | ❌ |
| 3.3 | Redirect URI | ❌ |
| 3.4 | Bot token | ❌ |
| 3.5 | Owned channel ID | ❌ |
| 3.6 | Supergroup ID | ❌ |
| 3.7 | Posting rights proof | ❌ |

## AI Provider
| # | Resource | Status |
|---|----------|--------|
| 4.1 | AI API key | ❌ |
| 4.2 | Model ID | ❌ |
| 4.3 | Billing quota | ❌ |
| 4.4 | API base URL | ❌ |
| 4.5 | Adapter fixtures | ❌ |

## Payment Provider
| # | Resource | Status |
|---|----------|--------|
| 5.1 | Merchant ID | ❌ |
| 5.2 | API credentials | ❌ |
| 5.3 | Webhook secret | ❌ |
| 5.4 | Provider webhook URL | ❌ |
| 5.5 | Sandbox account | ❌ |
| 5.6 | Refund API creds | ❌ |

## Backup Target
| # | Resource | Status |
|---|----------|--------|
| 6.1 | Off-host storage | ❌ |
| 6.2 | Encryption keys | ❌ |
| 6.3 | Restore env | ❌ |
| 6.4 | RPO/RTO target | ❌ |

## Device/Testing
| # | Resource | Status |
|---|----------|--------|
| 7.1 | Android device | ❌ |
| 7.2 | iOS device | ❌ |
| 7.3 | TalkBack | ❌ |
| 7.4 | VoiceOver | ❌ |
| 7.5 | Amharic speaker | ❌ |
| 7.6 | Owner reviewer | ❌ |

## Network
| # | Resource | Status |
|---|----------|--------|
| 8.1 | Outbound HTTPS | ✅ Working |
| 8.2 | Rate limit headroom | ⏳ |
| 8.3 | Firewall rules | ⏳ |

## Data/Governance
| # | Resource | Status |
|---|----------|--------|
| 9.1 | Marketplace baseline | ❌ |
| 9.2 | Deterioration thresholds | ❌ |
| 9.3 | Provider catalogs | ❌ |
| 9.4 | Legal retention policy | ❌ |

## Summary
| Category | Available | Missing | Pending |
|----------|-----------|---------|---------|
| Local tools | 2/8 | 4 | 2 |
| Server | 7/11 | 1 | 3 |
| Telegram | 0/7 | 7 | 0 |
| AI | 0/5 | 5 | 0 |
| Payment | 0/6 | 6 | 0 |
| Backup | 0/4 | 4 | 0 |
| Device | 0/6 | 6 | 0 |
| Network | 1/3 | 0 | 2 |
| Data | 0/4 | 4 | 0 |
| **TOTAL** | **10/54** | **37** | **7** |

**WP-21 CAN START. WP-22 READY. Others blocked.**

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)

### Environment Changes Since Original Checklist

| Item | Original | Current |
|------|----------|---------|
| MySQL DB | Not created | `zagcreht_felagi` (38 tables) |
| MySQL test DB | Not created | `zagcreht_felagi_test` (isolated) |
| Laravel | Not installed | 11.56.1 |
| HTTPS live | Not deployed | https://zagcreativity.com |
| Cron | Not configured | 1-min scheduler + queue:work |
| Composer packages | 0 | 110+ (incl. Sanctum, firebase/php-jwt, google2fa) |
| Git repo | Not initialized | `~/felagi_app/.git` (commits: 1bb9f19 → 64a8c27) |

### Still Missing (as of B17)

| # | Resource | Status |
|---|----------|--------|
| 1 | Flutter SDK | NOT FOUND |
| 2 | Node.js | NOT FOUND |
| 3 | Python 3.10+ | 3.6.8 (too old) |
| 4 | Chromium/Firefox | NOT FOUND |
| 5 | Telegram OIDC creds | PARTIAL (WP-27 done, live E2E pending) |
| 6 | AI API key | MISSING |
| 7 | Payment credentials | MISSING |
| 8 | Backup target | NOT CONFIGURED |
| 9 | Android/iOS device | MISSING |
| 10 | Amharic reviewer | MISSING |

### B16 .bak Cleanup

8 stale `.bak` files moved to `.archives/20260929-b16-bak-cleanup/`.

### B17 Verification

No environment changes. Documentation-only.
