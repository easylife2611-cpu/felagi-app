# Stakeholder Decisions Required — 2026-10-02

**Status:** ACTION REQUIRED
**Design HEAD:** 03aed32
**Reference:** Release_Gates_Evidence_20261002.md, GAP-53_DESIGN_PROPOSAL_20261002.md

**Purpose:** Every developer-actionable item is complete. This memo
lists decisions required from non-developer stakeholders.

---

## Decision Inventory

| # | Decision | Owner | Priority |
|---|---|---|---|
| 1 | Positive-fee activation (GAP-08) | product ops | CRITICAL |
| 2 | Production PASS approval (GAP-09) | release owner | CRITICAL |
| 3 | cPanel doc root capability (GAP-26) | hosting admin | CRITICAL |
| 4 | Frontend stack decision (D-097) | design owner | HIGH |
| 5 | GAP-53 recovery flow spec | design owner | HIGH |
| 6 | Browser/AT QA execution (G04) | external QA | HIGH |
| 7 | Flutter device build (G05) | Flutter team | HIGH |
| 8 | Payment provider credentials (G06) | infra + product | HIGH |
| 9 | AI fallback application (G06) | engineering lead | MEDIUM |
| 10 | SSRF/AV live probes (G06) | infrastructure | MEDIUM |
| 11 | Telemetry setup (G09) | infrastructure | MEDIUM |
| 12 | Monetization baseline (G07) | product ops | HIGH |
| 13 | Amharic native review (G08) | design owner | MEDIUM |

---

## 1. Product Operations

### Decision 1.1 — Positive-fee activation (GAP-08)

**Question:** Should Offer Submission Unlock be activated (positive ETB)?

**Required before activation:**
- Marketplace health baseline measured (GAP-07)
- Deterioration thresholds approved
- Provider catalogs loaded
- G06 + G07 evidence complete

**Current state:** Offer Submission Unlock = OFF, fee = 0 ETB

**Owner action:** Measure baseline, approve thresholds, signal go/no-go.

---

### Decision 1.2 — Monetization baseline (G07)

**Question:** What are the approved metrics + thresholds?

**Required metrics:**
- time-to-first-Offer
- Offers/Need
- qualified Offers
- submit conversion
- accepted-Offer conversion
- completion rate
- repeat rate

**Owner action:** Define baseline period, approve thresholds.

---

## 2. Release Owner

### Decision 2.1 — Production PASS approval (GAP-09)

**Question:** When all 9 gates are MET, who signs Production PASS?

**Required:** All critical requirements VERIFIED + no unresolved
BLOCKER/CRITICAL + all 9 gates passed.

**Current state:** G04/G05/G06/G08 PARTIAL; G07/G09 REQUIRES_EVIDENCE.

**Owner action:** Define acceptance criteria sign-off process.

---

## 3. Hosting Admin

### Decision 3.1 — cPanel doc root (GAP-26)

**Question:** Can cPanel point document root to public/ for this account?

**Reason:** Laravel standard deployment requires public/ as doc root.

**Owner action:** Verify capability; document approach.

---

## 4. Design Owner

### Decision 4.1 — Frontend stack (D-097)

**Question:** What is the frontend stack for 2FA UI + S015 UX + ads UI?

**Blocked items:**
- WP-13c-FE: 2FA enrollment UI (QR + verify)
- WP-13c-FE: Recovery codes display
- WP-13c-FE: Self-service 2FA disable
- WP-10-FE: S015 AI comparison full UI
- GAP-50/51/52: 2FA frontend

**Owner action:** Decide stack (Blade / Alpine / Livewire / Flutter
Web / SPA) and document.

---

### Decision 4.2 — GAP-53 recovery flow spec

**Question:** Per GAP-53_DESIGN_PROPOSAL_20261002.md, answer 6 questions:

1. Can Main Admin approve own recovery?
2. Is a second admin required for HIGH-risk?
3. Time window between request and approval?
4. Recovery codes: reset all or keep remaining?
5. Lost recovery codes + lost TOTP: same flow?
6. Support channel: S021 only, or dedicated route?

**Owner action:** Review proposal; provide LOCKED spec.

---

### Decision 4.3 — Amharic native review (G08)

**Question:** Who is the native Amharic reviewer?

**Source portion:** DONE (terminology, action voice, parity)

**Runtime portion required:**
- Amharic glyph rendering on device
- Orientation/continuity tasks
- Native reviewer sign-off

**Owner action:** Appoint reviewer; schedule verification.

---

## 5. External QA

### Decision 5.1 — Browser/AT execution (G04)

**Question:** Who executes real screen reader + keyboard tests?

**Required evidence:**
- NVDA / JAWS / VoiceOver session recording
- Keyboard navigation full flow
- 200% / 400% text scaling
- Amharic glyph rendering

**Current state:** Playwright audit complete (46/46 clean).

**Owner action:** Schedule AT session; produce evidence file.

---

## 6. Flutter Team

### Decision 6.1 — Device build (G05)

**Question:** Who executes flutter build apk/ios + device test?

**Required evidence:**
- Build artifact hash
- Device install + launch log
- Amharic font rendering
- TalkBack/VoiceOver confirmation

**Current state:** analyze + test complete (2/2).

**Owner action:** Schedule device build + evidence.

---

## 7. Infrastructure + Product

### Decision 7.1 — Payment provider credentials (G06)

**Question:** Which payment provider; when are credentials available?

**Required for:**
- Live payment test (verified callback)
- Reconciliation flow test
- Refund flow test

**Owner action:** Provide sandbox then production credentials.

---

### Decision 7.2 — AI fallback application (G06)

**Question:** Approve config/ai.php fallback list?

**Current state:** gemini-flash-lite-latest works (200); primary
gemini-flash-latest returns 503 intermittently.

**Proposed fallback (documented, not applied):**
gemini-flash-lite-latest -> gemini-3-flash-preview

**Owner action:** Approve or deny fallback application.

---

### Decision 7.3 — SSRF / AV media scanning (G06)

**Question:** Who provides SSRF live probe and AV media scanner?

**Required:**
- SSRF live probe (external hosts)
- Ads media scanning (AV)
- Real analytics (telemetry)

**Owner action:** Provision infra; schedule evidence.

---

## 8. Operations

### Decision 8.1 — Telemetry setup (G09)

**Question:** What observability platform?

**Required evidence:**
- Correlated safe request/audit IDs visible
- UNKNOWN/partial reporting
- Rollback probe

**Owner action:** Provision telemetry; produce evidence.

---

## Summary — Decisions Required

| Priority | Item | Owner |
|---|---|---|
| CRITICAL | GAP-08 positive-fee | product ops |
| CRITICAL | GAP-09 production PASS | release owner |
| CRITICAL | GAP-26 cPanel doc root | hosting admin |
| HIGH | D-097 frontend stack | design owner |
| HIGH | GAP-53 recovery spec | design owner |
| HIGH | G04 AT session | external QA |
| HIGH | G05 device build | Flutter team |
| HIGH | G06 payment creds | infra + product |
| HIGH | G07 baseline | product ops |
| MEDIUM | G06 fallback | engineering lead |
| MEDIUM | G06 SSRF/AV | infrastructure |
| MEDIUM | G08 Amharic | design owner |
| MEDIUM | G09 telemetry | infrastructure |

---

## What Developer Has Done

- All 46 screens implemented (S001-S023 + A001-A023)
- 1218 PHPUnit tests passed (3443 assertions)
- Flutter Design System: 2/2 tests passed
- G04 Playwright 46/46 clean
- G05 analyze + test done
- G06 live probes done (Telegram/Gemini/API)
- G08 source Amharic corrected
- L333 ledger entry committed
- SOURCE_OF_TRUTH synced

**No developer-actionable work remains.**

---



### Decision 9.1 — DPO Announcement Effective Date (GAP-F58)

**Source:** docs/privacy/DPO_ANNOUNCEMENT.md
**Current state:** `Effective date: [To be filled]`

**Question:** What is the official effective date of the DPO announcement?

**Legal basis:** Proclamation 1321/2024, Art. 27

**Owner action:** Design owner / DPO provides effective date; update
DPO_ANNOUNCEMENT.md with concrete value.

**Constitution:** UNKNOWN != MISSING — placeholder preserved as UNKNOWN.

---
**Generated by:** L333 + Stakeholder Decisions task
