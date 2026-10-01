# Sponsored Advertising — Privacy & Consent

**Status:** Canonical (ADS-55)
**Owner:** `System_Specification/Sponsored_Advertising_Contract.md`
**Generated:** 2026-10-01
**Applies to:** A023 (Sponsored Ads admin), S004 (Browse), S005 (Search),
S008 (Need detail)
**Scope:** Sponsored Advertising subsystem only. Does not replace the
foundation privacy policy.

---

## 1. Lawful Basis

Sponsored placements are shown based on **contextual** signals only.
No profile, no inferred sensitive trait, no cross-service identity is
used to select an ad. Serving is governed by the admin-controlled
`adsEnabled` master and per-placement switches (`adsBrowse`,
`adsSearch`, `adsNeedDetail`). Default state is OFF for every install.

## 2. Data We Use (Contextual Only)

| Signal | Purpose |
|---|---|
| Placement ID | Which slot the ad may fill |
| Marketplace category (public) | Coarse context match |
| Explicitly coarse region | Coarse context match |
| Locale (am / en) | Language of the ad copy |
| Campaign schedule | Start/end eligibility |
| Session pseudonymous reference | Frequency caps, deduplication |

## 3. Data We Do NOT Use

Targeting **must not** derive from, and delivery **must not** read:

- Messages or attachments
- Phone number or physical address
- Payment or wallet records
- Private provider history
- AI comparison results or reasoning
- Reports or support tickets
- Secrets or inferred sensitive traits
- Any persistent cross-service identity

## 4. Retention

| Data class | Retention | Notes |
|---|---|---|
| Raw ad events | **30 days** | Delivery ID, event type, observed time |
| Aggregate reports | **180 days** | Non-identifying counts only |
| Immutable Admin audit | Per foundation policy | Not altered by this subsystem |

Operational retention is separate from the foundation's immutable audit
rules.

## 5. Deletion & Restricted Requests

A restricted deletion request removes linkable analytics. Aggregate,
non-identifying counts may remain per the approved retention policy.
Deletion does not alter immutable Admin audit records.

## 6. Tracking Prohibitions

- No third-party tracking pixels or scripts.
- No cross-app or cross-service identity.
- No raw marketplace records inside ad payloads.
- No advertiser HTML/JS/CSS/iframe.

## 7. Impression and Click Definitions

- **Impression:** ≥ 50 % of the ad is continuously visible for ≥ 1 s
  while the document is foreground. Hidden or obscured resets the timer.
- **Rendered** is not **seen**. **Impression** is not **read**.
- **Click:** explicit user activation. It is **not** a purchase or
  conversion.
- Events dedupe by `(delivery_id, event_type)` and unique event ID.
- Offline events are reconciled within 24 h using the original observed
  time. Counts are never manufactured.
- CTR = clicks attributable to measured impressions in the same cohort,
  divided by those impressions. A zero denominator is **UNKNOWN**, not
  `0 %`.
- Tracking unavailable is explicit, never fabricated zeros.

## 8. Audit Trail

Every ad-related Admin action records: actor, permission, reason,
before/after versions, creative/destination approval, schedule,
placement/caps, impact digest, publication/application/probe outcome,
and rollback reference. Private contact details and media upload
secrets are redacted.

## 9. Consent Points

| Where | What |
|---|---|
| A023 diagnostics | Operator sees tracking status (`NOT_AVAILABLE` until instrumented) |
| A023 master toggle | Turning OFF stops new delivery; history is retained |
| Consumer placement | Localized "Sponsored" label + sponsor + disclosure |
| Admin login footer | Terms & Privacy Policy link (foundation) |

## 10. Consumer Disclosure

A consumer-visible sponsored placement always shows:

1. Persistent localized **Sponsored** label
2. Sponsor (advertiser display name)
3. Title, optional approved media, short body
4. Neutral CTA
5. Disclosure that the placement is paid

No fake Need state, provider rating, AI score, or "best/recommended"
badge is ever shown.

## 11. Evidence Boundary

Published ≠ Applied ≠ Verified. Design coverage is not implementation
evidence. Live media scanning, external host catalog, serving counters,
and real analytics ingestion remain **outside** this subsystem until
separately verified (see `OPEN_GAPS.md` — GAP-12, GAP-13, GAP-14).

## 12. Escalation

- Canonical owner: `System_Specification/Sponsored_Advertising_Contract.md`
- Admin owner: A023 (Sponsored Ads)
- Requirement: ADS-55
