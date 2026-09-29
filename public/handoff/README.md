# Felagi v1.4.2 — Developer Handoff

**Read this first:** https://zagcreativity.com/handoff/

## What is Felagi?

Felagi (ፈላጊ) is a Need-First Marketplace for the Ethiopian market:
- Users post Needs
- Providers submit Offers
- AI compares Offers fairly (advisory)
- Requester decides

## Current State

- **Design:** LOCKED at v1.4.1 (no changes allowed)
- **Backend:** Laravel 11, 38 tables, 20 models, 9 controllers
- **Deployment:** LIVE at https://zagcreativity.com
- **Production:** PENDING (runtime evidence required)

## Where Everything Is

| Item | Location |
|------|----------|
| Web entry | https://zagcreativity.com/handoff/ |
| App code | `/home/zagcreht/felagi_app/` |
| Design package | `/home/zagcreht/felagi_extracted/Felagi_Design_Package/` |
| 13 ledgers | `PROJECT_CONTROL/` in design package |
| Original ZIP | https://zagcreativity.com/downloads/Felagi_Clean_Developer_Handoff_v1.4.2.zip |
| Full snapshot | https://zagcreativity.com/downloads/ |

## What to Read

1. **Web handoff** (https://zagcreativity.com/handoff/)
2. **MASTER_BASELINE.md** (locked rules)
3. **WORK_PACKAGES.md** (current status)
4. **HANDOFF_STATE.md** (session continuity)

## Constitution

Do NOT:
- Redesign architecture
- Reinterpret design contract
- Skip evidence
- Make silent changes
- Claim production PASS without runtime evidence

Always:
- Record in CHANGE_LOG.md
- Record decisions in DECISION_LOG.md
- Mark UNKNOWN explicitly
- Test before claiming DONE

## Next Steps

Recommended: **WP-13 Admin Change Lifecycle** (backend exists)

Alternative: **WP-10 AI Integration** (needs API key)
Alternative: **WP-27 Telegram OIDC** (needs credentials)

See full plan at: https://zagcreativity.com/handoff/#next
