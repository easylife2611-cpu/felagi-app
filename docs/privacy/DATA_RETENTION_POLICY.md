# Data Retention Policy

**Legal Basis:** Proclamation 1321/2024, Art. 18
**Effective:** 2026-10-01

## 1. Principle
Personal data retained only as long as necessary for its purpose.

## 2. Retention Schedule
| Data class | Retention | Reason |
|---|---|---|
| Account profile | Deletion + 30d | Service |
| Needs & Offers | Deletion + 30d | Service |
| Messages | Deletion + 30d | Service |
| Ratings | 24 months | Trust & safety |
| Payment records | 7 years | NBE / tax |
| Audit logs | 7 years | Compliance |
| Ad events | 30 days | Analytics |
| Ad aggregates | 180 days | Reporting |
| Session logs | 90 days | Security |
| Auth attempts | 90 days | Security |
| Consent logs | Revoke + 7 years | Compliance proof |

## 3. Deletion Triggers
- User request (right to erasure)
- Inactivity (24 months, with notice)
- Legal order
- End of retention period

## 4. Deletion Method
- Production: hard delete with audit record
- Backups: rotated within 30 days
- Aggregates: may remain (non-identifying)

## 5. Exceptions
Legal holds, dispute resolution, regulatory requirements.

## 6. Review
Annually or on legal framework change.

**Version:** 1.0
