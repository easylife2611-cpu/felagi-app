# N06-N10 Evidence Requirements

**Generated:** 2026-09-30
**Purpose:** D3 — Evidence package for BLOCKED findings

## Overview

5 findings (N06-N10) remain BLOCKED/REQUIRES_EVIDENCE.

---

## N06 — Frontend/A11y QA (HIGH, BLOCKED)

**Blocker:** No browser / no AT

**Evidence Required:**
- Browser matrix test (Chrome, Firefox, Safari, Edge)
- Responsive verification (46 screens × 4 breakpoints)
- Screen reader test (NVDA / JAWS / VoiceOver)
- Keyboard navigation (all 46 screens)
- Color contrast (WCAG 2.1 AA)

**Environment:** GAP-01

---

## N07 — Flutter Team (HIGH, BLOCKED)

**Blocker:** No Flutter SDK / Dart

**Evidence Required:**
- Flutter compilation (all platforms)
- APK build artifact
- Dart analyzer clean output
- Widget test results

**Environment:** GAP-02

---

## N08 — Backend/Security QA (HIGH, REQUIRES_EVIDENCE)

**Blocker:** No live services / telemetry

**Evidence Required:**
- Penetration test report
- OWASP Top 10 audit
- API fuzz test results
- Rate limit verification
- Webhook signature validation

**Environment:** GAP-03, GAP-30

---

## N09 — Amharic/UX Reviewers (MEDIUM, PARTIAL)

**Blocker:** No native Amharic reviewer

**Evidence Required:**
- Amharic translation review (469+ keys)
- UX copy review (en + am)
- Font glyph coverage

**Environment:** GAP-04, GAP-20

---

## N10 — Product Operations (HIGH, REQUIRES_EVIDENCE)

**Blocker:** No product ops decision

**Evidence Required:**
- Positive-fee activation decision
- Monetization finalization
- Pricing approval
- Production PASS authorization

**Environment:** GAP-08, GAP-09

---

## Summary

| ID | Items | Blocker |
|---|---|---|
| N06 | 5 | GAP-01 |
| N07 | 4 | GAP-02 |
| N08 | 5 | GAP-03, GAP-30 |
| N09 | 3 | GAP-04, GAP-20 |
| N10 | 4 | GAP-08, GAP-09 |

**Total:** 21 evidence items
**All require external resources (not code)**

## Constitution Note

Per "UNKNOWN != MISSING": remain BLOCKED — not deleted, not guessed.
