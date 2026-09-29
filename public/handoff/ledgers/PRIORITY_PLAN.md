# PRIORITY PLAN — Felagi v1.4.2
Last Updated: 2026-09-29

## Priority Tiers

### TIER 0 — Immediate (DONE)
- WP-01: Foundation Registry ✅
- WP-02: Static Re-run ✅
- WP-ENV: Environment Verification ✅
- WP-PRIORITY: Ledgers built ✅

### TIER 1 — Fast Unblockers
| WP | Scope | Status |
|----|-------|--------|
| WP-21 | Laravel/cPanel Deploy | ✅ DONE |
| WP-22 | DB Queue + Cron | ✅ DONE |
| WP-03 | Browser/A11y | BLOCKED |
| WP-04 | Flutter | BLOCKED |

### TIER 2 — Core Infrastructure
| WP | Scope | Blocker |
|----|-------|---------|
| WP-27 | Telegram OIDC | ✅ DONE |
| WP-11 | Payment Live | Creds |
| WP-25 | Webhook | Sandbox |
| WP-14 | Telegram Delivery | Bot token |
| WP-10 | AI Live | API key |

### TIER 3 — Feature Completeness
| WP | Scope | Blocker |
|----|-------|---------|
| WP-05 | Backend Services | ✅ DONE |
| WP-12 | Sponsored Ads | Infra |
| WP-13 | Admin Lifecycle | ✅ DONE |
| WP-13c | 2FA Enrollment UI | DEFERRED (UNKNOWN) |
| WP-26 | File Malware | Host AV |

### TIER 4 — Verification Sweeps
| WP | Scope | Blocker |
|----|-------|---------|
| WP-17 | Responsive 46 | Browser |
| WP-18 | A11y 46 | AT |
| WP-19 | Runtime States | Stack |
| WP-20 | Error Recovery | Stack |

### TIER 5 — Governance
| WP | Scope | Blocker |
|----|-------|---------|
| WP-16 | Amharic QA | Reviewer |
| WP-07 | Marketplace | Data |
| WP-06 | Amharic Runtime | Device |
| WP-15 | Font Glyph | License |

### TIER 6 — Production
| WP | Scope | Requires |
|----|-------|----------|
| WP-23 | Backup Drill | Target |
| WP-24 | T01-T18 | UNKNOWN (definitions missing) |
| WP-28 | Safe Mode | Stack |
| WP-08 | Observability | Metrics |
| WP-09 | **Production Gates** | ALL above |

## Recommended Sequence
1. **WP-21** — Laravel install
2. **WP-22** — Queue + Cron
3. **WP-27** — Telegram OIDC
4. **WP-11** — Payment
5. **WP-10** — AI
6. **WP-14** — Telegram delivery
7. **WP-05** — Backend services
8. **WP-13** — Admin lifecycle
9. **WP-17, WP-18** — Verification sweeps
10. **WP-23, WP-24** — Production prep
11. **WP-09** — Final gates

**Estimated: 10-11 weeks (parallel: 6-8 weeks)**

## Constitution Rule
"If one task is BLOCKED, isolate and document the blocker and continue independent safe Work Packages."
