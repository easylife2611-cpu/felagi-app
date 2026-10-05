// Design policy adapter. Payment truth must come from the authenticated server.
class FgOfferFeePolicy {
 final bool enabled; final int amountMinor; final String currency; final int version;
 const FgOfferFeePolicy({required this.enabled,required this.amountMinor,required this.currency,required this.version});
 bool get paymentRequired {
  if(amountMinor<0 || currency!='ETB') throw StateError('POLICY_UNKNOWN');
  return enabled && amountMinor>0;
 }
}
enum FgSubmissionState { draft, paymentRequired, pending, verified, submitting, submitted, recovery, refundPending, unknown }
// A client callback never creates this receipt; use a server response parser in the app.
class FgSubmissionReceipt {
 final String submissionId; final String? offerId; final FgSubmissionState state;
 final int snapshotAmountMinor; final String currency;
 const FgSubmissionReceipt({required this.submissionId,this.offerId,required this.state,required this.snapshotAmountMinor,required this.currency});
 bool get mayStartSecondCharge => false;
}
