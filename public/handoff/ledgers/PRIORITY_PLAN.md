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

---
## APPEND-ONLY EXTENSIONS (B17 — 2026-09-29)
Original TIER plan preserved above. Additive updates below.

## TIER 0 Completion Clarification (B17)

The TIER 0 items (WP-01, WP-02, WP-ENV, WP-PRIORITY) were marked DONE in
the original plan. Post-audit status confirmed:
- WP-01 Foundation Registry — DONE
- WP-02 Static Re-run — VERIFIED (7,810+ PASS)
- WP-ENV Environment Verification — DONE
- WP-PRIORITY Ledgers built — DONE

## B-Blocks Completion (B10-B17)

All 8 B-blocks completed on 2026-09-29:

| Block | Deliverable | Status |
|-------|-------------|--------|
| B10 | Constitution Compliance (8 fixes) | DONE |
| B11 | Main Admin Foundation verification | DONE |
| B12 | Bundle refresh | DONE |
| B13 | /downloads/ deployed (GAP-61) | DONE |
| B14 | GAP-62 registered | DONE |
| B15 | S001 Welcome live (GAP-62 closed) | DONE |
| B16 | Non-admin tests + cleanup | DONE |
| B17 | Ledger Integrity Sweep | DONE |

## Next Priority (post-B17)

### Immediate — Stakeholder Decisions Required
1. **WP-24** — T01-T18 Integration Tests — D-096 (UNKNOWN definitions)
2. **WP-13c** — 2FA Enrollment UI — D-097 (UNKNOWN stack)
3. **WP-05c** — Admin read endpoints spec (pending)
4. **U-21** — B-Blocks vs Work Packages — formalize or keep separate?

### Safe, Additive Work (Constitution-compliant, no external deps)
5. **B18** — Extended non-admin write tests (extend B16 pattern)
6. **B18** — Model unit tests (fill missing coverage)
7. **B18** — Migration integrity audit (additive, no schema change)

### Blocked on External Dependencies
8. **WP-10** — AI Integration (provider key)
9. **WP-11** — Payment Live (payment creds)
10. **WP-14** — Telegram Delivery (bot token)
11. **WP-25** — Webhook Signature (provider sandbox)
12. **WP-23** — Backup Restore Drill (backup target)

### Blocked on Device/Tooling
13. **WP-03/04** — Browser/Flutter SDK
14. **WP-17/18** — Device/AT testing

## Recommended Sequence (revised)
1. **Stakeholder decisions** — WP-24, WP-13c, WP-05c, U-21
2. **B18** — Safe additive test work (parallel)
3. **External deps** — await creds for WP-10, WP-11, WP-14, WP-25
4. **WP-09** — Final production gates (when above complete)
