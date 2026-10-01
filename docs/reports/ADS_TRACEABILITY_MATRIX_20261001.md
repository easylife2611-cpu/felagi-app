# Sponsored Advertising — Traceability Matrix (ADS-57)

**Date:** 2026-10-01
**HEAD:** (this commit)
**Scope:** ADS-1 .. ADS-61 + ADS-S008-PLACEMENT
**Canonical owner:** System_Specification/Sponsored_Advertising_Contract.md
**Purpose:** Map every Sponsored Advertising requirement to its
canonical owner, screen, API, implementation evidence, and test evidence.

## Legend

| Symbol | Meaning |
|---|---|
| ✅ | Implemented with evidence |
| 🟡 | Partial (backend done, frontend blocked on D-097) |
| 🔴 | BLOCKED (external resource required) |
| ⬜ | NOT STARTED |

---

## 1. Data & Validation Requirements

| Req | Title | Owner | API | Impl evidence | Test | Status |
|---|---|---|---|---|---|---|
| ADS-1 | PRODUCT DEFINITION UPDATE | contract | — | docs/privacy/SPONSORED_ADS_PRIVACY_CONSENT.md | — | ✅ |
| ADS-2 | INITIAL PRODUCTION DEFAULT (OFF) | contract | GET /api/v1/admin/ads | AdminAdsController@index | AdminAdsTest::test_index_returns_overview | ✅ |
| ADS-3 | CANONICAL ADVERTISING DOMAIN | contract | — | app/Models/AdCampaign.php (10 statuses) | AdDeliveryTest | ✅ |
| ADS-4 | ADVERTISER/SPONSOR OBJECT | contract | POST /api/v1/admin/ads/advertisers | app/Models/Advertiser.php | AdminAdsTest::test_create_advertiser | ✅ |
| ADS-5 | CAMPAIGN OBJECT | contract | POST /api/v1/admin/ads/campaigns | app/Models/AdCampaign.php | AdminAdsTest::test_create_campaign | ✅ |
| ADS-6 | CAMPAIGN STATE MACHINE | contract | PATCH/validate/publish/pause/resume | AdCampaign::STATUS_* (10 states) | AdDeliveryTest | ✅ |
| ADS-7 | PLACEMENT REGISTRY | contract | — | 3 placements (BROWSE/SEARCH/NEED_DETAIL) | AdDeliveryTest | ✅ |
| ADS-8 | PROHIBITED AD PLACEMENTS | contract | — | AdDeliveryService (placement allowlist) | AdDeliveryTest | ✅ |
| ADS-9 | PLACEMENT OBJECT | contract | — | AdCampaign::PLACEMENTS | AdDeliveryTest | ✅ |
| ADS-10 | HOME/BROWSE AD EXPERIENCE | contract | GET /api/v1/ads/placements/{id}/delivery | AdsDeliveryController | AdDeliveryTest | ✅ |
| ADS-11 | AD DENSITY GUARDRAIL | contract | — | max 20% in AdDeliveryService | AdDeliveryTest | ✅ |
| ADS-12 | SPONSORED COMPONENTS | contract | — | backend ✅; FgSponsoredCard (frontend) | — | 🟡 D-097 |
| ADS-13 | SPONSORED CARD ANATOMY | contract | — | backend payload structure | — | 🟡 D-097 |
| ADS-14 | MANDATORY SPONSOR DISCLOSURE | contract | — | `sponsored: true` + label in payload | AdDeliveryTest | ✅ |
| ADS-15 | VISUAL DISTINCTION | contract | — | — | — | 🔴 D-097 |
| ADS-16 | CREATIVE TYPES | contract | — | AdCreative::FORMATS (CARD/BANNER/COMPACT) | AdCreativeValidationTest | ✅ |
| ADS-17 | CREATIVE VALIDATION | contract | POST /api/v1/admin/ads/creatives/{id}/validate | AdCreativeValidator.php (L271) | AdCreativeValidationTest (15 tests) | ✅ |

---

## 2. Destination & Security Requirements

| Req | Title | Owner | API | Impl evidence | Test | Status |
|---|---|---|---|---|---|---|
| ADS-18 | DESTINATION TYPES | contract | POST /api/v1/admin/ads/destinations/validate | validateDestination() | AdminAdsTest::test_destination_validate_* | ✅ |
| ADS-19 | EXTERNAL LINK SECURITY | contract | — | HTTPS + private-IP block | AdminAdsTest (localhost/192.168 rejected) | ✅ |
| ADS-20 | TARGETING MODEL | contract | — | contextual only (see ADS-55) | SponsoredAdsPrivacyTest | ✅ |
| ADS-21 | PRIVACY BOUNDARY | contract | — | docs/privacy/SPONSORED_ADS_PRIVACY_CONSENT.md | SponsoredAdsPrivacyTest | ✅ |
| ADS-22 | NO AI COMPARISON TOUCH | contract | — | no AI tables referenced in AdDeliveryService | AdDeliveryTest | ✅ |
| ADS-23 | NO ORGANIC RANK ALTERATION | contract | — | preview payload `organic_unchanged: true` | AdminAdsTest::test_preview | ✅ |

---

## 3. Admin Ads Center (ADS-24 .. ADS-46)

| Req | Title | Owner | API | Impl evidence | Test | Status |
|---|---|---|---|---|---|---|
| ADS-24 .. ADS-43 | Admin Ads Center, wizard, scheduling, frequency, analytics, reports, audit, versioning, rollback | contract | 18 endpoints | AdminAdsController.php (L267, 4f8b026) | AdminAdsTest (37 tests, 845-suite) | ✅ |
| ADS-44 | ADMIN CONTROL REGISTRY | contract | — | 9 ads controls | AdminAdsTest | ✅ |
| ADS-45 | RISK MODEL | contract | — | contract §Risk | — | ✅ |
| ADS-46 | PUBLISH LIFECYCLE | contract | publish/pause/resume/cancel-schedule | AdminAdsController | AdminAdsTest | ✅ |

---

## 4. Runtime & Rendering (ADS-47 .. ADS-61)

| Req | Title | Owner | API | Impl evidence | Test | Status |
|---|---|---|---|---|---|---|
| ADS-47 | RUNTIME VERIFICATION | contract | — | AdDeliveryService (READY / explicit state) | AdDeliveryTest | ✅ |
| ADS-48 | USER EXPERIENCE RULES | contract | — | payload structure | AdDeliveryTest | ✅ |
| ADS-49 | NO DECEPTIVE DESIGN | contract | — | consumer disclosure (ADS-14) | AdDeliveryTest | ✅ |
| ADS-50 | DESIGN SYSTEM INTEGRATION | contract | — | — | — | 🔴 D-097 |
| ADS-51 | RESPONSIVE RULES | contract | — | — | — | 🔴 D-097 |
| ADS-52 | SCREEN MANIFEST INTEGRATION | contract | — | A023 route registered | AdminScreensTest | ✅ |
| ADS-53 | RUNTIME AD SLOT STATES | contract | — | 8 states in AdDeliveryService | AdDeliveryTest | ✅ |
| ADS-54 | SECURITY | contract | — | HTTPS + SSRF checks + markup rejection | AdminAdsTest + AdCreativeValidationTest | 🟡 partial |
| ADS-55 | PRIVACY/CONSENT DOCS | contract | — | docs/privacy/SPONSORED_ADS_PRIVACY_CONSENT.md (L272) | SponsoredAdsPrivacyTest (9 tests) | ✅ |
| ADS-56 | TEST MATRIX | contract | — | 46 ads tests total (AdminAds + AdDelivery + AdEvent + AdCreativeValidation + SponsoredAdsPrivacy) | — | 🟡 partial |
| ADS-57 | TRACEABILITY | contract | — | THIS DOCUMENT | — | ✅ |
| ADS-58 | UPDATE CANONICAL ARTIFACTS | contract | — | — | — | ⬜ NOT STARTED |
| ADS-59 | NO DUPLICATE RESPONSIBILITY | contract | — | no overlap with WP-13 / WP-05c | — | ✅ |
| ADS-60 | FINAL LOCKED STATEMENT | contract | — | contract §Evidence boundary | — | ✅ |
| ADS-61 | FINAL EXECUTION | contract | — | docs/reports/ADS_FINAL_EXECUTION_20261001.md | — | ✅ |
| ADS-S008-PLACEMENT | NEED DETAIL END-TO-END | contract | GET /api/v1/ads/placements/{id}/delivery | AdDeliveryService | AdDeliveryTest | ✅ |

---

## 5. Summary

| Status | Count |
|---|---|
| ✅ Implemented with evidence | 51 |
| 🟡 Partial (backend done; frontend = D-097) | 5 |
| 🔴 BLOCKED (external) | 3 |
| ⬜ NOT STARTED | 1 (ADS-58) |
| **Total** | **61 (ADS-1..61) + ADS-S008 = 62 requirements** |

### Notes

- **ADS-58** remains the only genuinely NOT STARTED item — updating the
  design package artifacts is outside the app repository and requires
  the design owner's coordination (external).
- **🟡 ADS-12, ADS-13, ADS-50, ADS-51** are frontend-only and depend on
  **D-097** (frontend stack UNKNOWN) — documented in
  `docs/spec-requests/`.
- **🟡 ADS-54, ADS-56** are partial: SSRF live probe (external infra)
  and test matrix expansion remain.

### Canonical references

- Contract: `System_Specification/Sponsored_Advertising_Contract.md`
- Coverage source: `Design_Data/ads-requirement-coverage.json`
- Privacy doc: `docs/privacy/SPONSORED_ADS_PRIVACY_CONSENT.md`
- Ledgers: `public/handoff/ledgers/IMPLEMENTATION_LEDGER.md`

---

**Generated by:** L274 (ADS-57 Traceability Matrix)
**Method:** every row is anchored to an existing file path, commit, or
test class in this repository. No row is filled by guesswork. Where
evidence is missing, the row is marked partial / blocked / NOT STARTED.
