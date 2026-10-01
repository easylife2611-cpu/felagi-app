# Data Protection Impact Assessment (DPIA)

**Legal Basis:** Proclamation 1321/2024, Art. 26
**Date:** 2026-10-01

## 1. Processing Activities
| Activity | Risk | Mitigation |
|---|---|---|
| Account creation | Low | Minimum data |
| Marketplace | Medium | Access controls |
| AI comparison (Gemini) | High | Consent + minimization |
| Telegram notifications | Medium | Consent |
| Sponsored ads | Medium | Contextual only |
| Payment | High | PCI-DSS, NBE |

## 2. High-Risk Activities

### AI Comparison (Gemini)
- Data: Offer text only (no PII)
- Risk: Third-country transfer (USA)
- Mitigation: Consent, DPA, anonymization, opt-out
- Residual: Medium

### Payments
- Data: Transaction records
- Risk: Financial
- Mitigation: NBE-licensed processor, encryption
- Residual: Low

### Sponsored Ads
- Data: Contextual signals only
- Risk: Re-identification
- Mitigation: No cross-service identity
- Residual: Low

## 3. Consultation
High residual risk requires DPO to consult ECA.

## 4. Review
Material change, annually, or ECA request.

**Version:** 1.0
