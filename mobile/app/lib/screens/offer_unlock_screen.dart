import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

/// S023 — Offer Submission Unlock (payment flow).
///
/// States: free · paymentRequired · pending · paymentVerified ·
///         submissionRecovery · submitted · refundPending · failed · unknown
///
/// API: POST /offer-submissions · GET /offer-submissions/{id}
///      POST /offer-submissions/{id}/resume
///
/// Payment truth comes from the server only. Client callback is never proof.
class OfferUnlockScreen extends StatefulWidget {
  const OfferUnlockScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    required this.needId,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;
  final String needId;

  @override
  State<OfferUnlockScreen> createState() => _OfferUnlockScreenState();
}

class _OfferUnlockScreenState extends State<OfferUnlockScreen> {
  String _t(String key) => fgText(widget.localeCode, key);

  // Server-driven state. Demo value; wire to real submission later.
  _UnlockPhase _phase = _UnlockPhase.paymentRequired;
  final int _feeMinor = 5000; // ETB 50.00
  final String _currency = 'ETB';

  String get _feeLabel {
    final whole = _feeMinor ~/ 100;
    final dec = (_feeMinor % 100).toString().padLeft(2, '0');
    return '$_currency $whole.$dec';
  }

  void _setPhase(_UnlockPhase next) => setState(() => _phase = next);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(_t('screenS023')),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.pop(),
        ),
      ),
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 480),
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(FgTokens.space4),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  _buildStatusBadge(),
                  const SizedBox(height: FgTokens.space4),
                  _buildFeeCard(),
                  const SizedBox(height: FgTokens.space4),
                  _buildBody(),
                  const SizedBox(height: FgTokens.space4),
                  _buildPrimaryAction(),
                  const SizedBox(height: FgTokens.space3),
                  _buildSupportLink(),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildStatusBadge() {
    final (label, kind) = switch (_phase) {
      _UnlockPhase.free => (_t('stateFree'), FgStatusKind.success),
      _UnlockPhase.paymentRequired => (_t('statePaymentRequired'), FgStatusKind.warning),
      _UnlockPhase.pending => (_t('statePending'), FgStatusKind.info),
      _UnlockPhase.paymentVerified => (_t('statePaymentVerified'), FgStatusKind.success),
      _UnlockPhase.submissionRecovery => (_t('stateSubmissionRecovery'), FgStatusKind.warning),
      _UnlockPhase.submitted => (_t('stateSubmitted'), FgStatusKind.success),
      _UnlockPhase.refundPending => (_t('stateRefundPending'), FgStatusKind.info),
      _UnlockPhase.failed => (_t('stateFailed'), FgStatusKind.error),
      _UnlockPhase.unknown => (_t('stateUnknown'), FgStatusKind.neutral),
    };
    return Align(
      alignment: Alignment.centerLeft,
      child: FgStatusBadge(label: label, kind: kind),
    );
  }

  Widget _buildFeeCard() {
    return Container(
      padding: const EdgeInsets.all(FgTokens.space6),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF001A33), Color(0xFF0B4D83)],
        ),
        borderRadius: BorderRadius.circular(FgTokens.radiusSheet),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(FgTokens.space2),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(FgTokens.radiusSmall),
                ),
                child: const Icon(Icons.lock_outline, color: Colors.white, size: 22),
              ),
              const SizedBox(width: FgTokens.space3),
              Expanded(
                child: Text(
                  _t('controlOfferUnlock'),
                  style: const TextStyle(
                    color: Colors.white, fontSize: 15, fontWeight: FontWeight.w800,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: FgTokens.space4),
          Text(
            _feeLabel,
            style: const TextStyle(
              color: Colors.white, fontSize: 32, fontWeight: FontWeight.w900,
              letterSpacing: -0.5,
            ),
          ),
          const SizedBox(height: FgTokens.space2),
          Text(
            _t('feeRequiredWhy'),
            style: const TextStyle(color: Colors.white70, fontSize: 12.5, height: 1.5),
          ),
        ],
      ),
    );
  }

  Widget _buildBody() {
    switch (_phase) {
      case _UnlockPhase.free:
        return FgStatePanel(
          label: _t('stateFree'),
          message: _t('offerFeeHelp'),
          kind: FgStatusKind.success,
        );
      case _UnlockPhase.pending:
        return FgStatePanel(
          label: _t('statePending'),
          message: _t('offerFeeHelp'),
          kind: FgStatusKind.info,
        );
      case _UnlockPhase.failed:
        return FgStatePanel(
          label: _t('stateFailed'),
          message: _t('unknownHelp'),
          kind: FgStatusKind.error,
        );
      case _UnlockPhase.refundPending:
        return FgStatePanel(
          label: _t('stateRefundPending'),
          message: _t('offerFeeHelp'),
          kind: FgStatusKind.info,
        );
      case _UnlockPhase.unknown:
        return FgStatePanel(
          label: _t('stateUnknown'),
          message: _t('unknownHelp'),
          kind: FgStatusKind.neutral,
        );
      default:
        return FgStatePanel(
          label: _t('statePaymentRequired'),
          message: _t('offerFeeHelp'),
          kind: FgStatusKind.warning,
        );
    }
  }

  Widget _buildPrimaryAction() {
    return switch (_phase) {
      _UnlockPhase.free => FgButton(
          label: _t('submitOffer'),
          onPressed: () => context.push('/needs/${widget.needId}/offers/new'),
        ),
      _UnlockPhase.paymentRequired => FgButton(
          label: _t('payAndContinue'),
          onPressed: () => _setPhase(_UnlockPhase.pending),
        ),
      _UnlockPhase.pending => FgButton(
          label: _t('checkPaymentStatus'),
          onPressed: () => _setPhase(_UnlockPhase.paymentVerified),
        ),
      _UnlockPhase.paymentVerified || _UnlockPhase.submissionRecovery => FgButton(
          label: _t('resumeSubmission'),
          onPressed: () => context.push('/needs/${widget.needId}/offers/new'),
        ),
      _UnlockPhase.submitted => FgButton(
          label: _t('viewOffer'),
          onPressed: () => context.push('/needs/${widget.needId}/offers'),
        ),
      _UnlockPhase.refundPending => FgButton(
          label: _t('viewRefundStatus'),
          onPressed: () => _setPhase(_UnlockPhase.paymentRequired),
        ),
      _UnlockPhase.failed || _UnlockPhase.unknown => FgButton(
          label: _t('retryOrSupport'),
          onPressed: () => context.push('/support/report'),
        ),
    };
  }

  Widget _buildSupportLink() {
    return TextButton(
      onPressed: () => context.push('/support/report'),
      child: Text(_t('fieldReasonCode')),
    );
  }
}

enum _UnlockPhase {
  free,
  paymentRequired,
  pending,
  paymentVerified,
  submissionRecovery,
  submitted,
  refundPending,
  failed,
  unknown,
}
