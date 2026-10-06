import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

import '../api/api_exception.dart';
import '../api/models/offer.dart';
import '../app_scope.dart';

/// S012 — Offer Detail (single offer view).
class OfferDetailScreen extends StatefulWidget {
  const OfferDetailScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    required this.offerId,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;
  final String offerId;

  @override
  State<OfferDetailScreen> createState() => _OfferDetailScreenState();
}

enum _Phase { loading, ready, error }

class _OfferDetailScreenState extends State<OfferDetailScreen> {
  _Phase _phase = _Phase.loading;
  Offer? _offer;
  String? _error;
  bool _actionInFlight = false;

  String _t(String key) => fgText(widget.localeCode, key);

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _load());
  }

  Future<void> _load() async {
    if (!mounted) return;
    if (widget.offerId.isEmpty) {
      setState(() {
        _error = _t('unknown');
        _phase = _Phase.error;
      });
      return;
    }
    setState(() {
      _phase = _Phase.loading;
      _error = null;
    });
    try {
      final api = AppScope.of(context).offersApi;
      final offer = await api.show(widget.offerId);
      if (!mounted) return;
      setState(() {
        _offer = offer;
        _phase = _Phase.ready;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _error = e.message;
        _phase = _Phase.error;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = '$e';
        _phase = _Phase.error;
      });
    }
  }

  Future<void> _accept() async {
    final api = AppScope.of(context).offersApi;
    await _runAction(() => api.accept(widget.offerId));
  }

  Future<void> _reject() async {
    final api = AppScope.of(context).offersApi;
    await _runAction(() => api.reject(widget.offerId));
  }

  Future<void> _withdraw() async {
    final api = AppScope.of(context).offersApi;
    await _runAction(() => api.withdraw(widget.offerId));
  }

  Future<void> _runAction(Future<Offer> Function() action) async {
    setState(() => _actionInFlight = true);
    try {
      final updated = await action();
      if (!mounted) return;
      setState(() {
        _offer = updated;
        _actionInFlight = false;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => _actionInFlight = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(e.message)),
      );
    } catch (e) {
      if (!mounted) return;
      setState(() => _actionInFlight = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('$e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(_t('screenS012')), actions: [
        IconButton(tooltip: _t('screenS017'), icon: const Icon(Icons.chat_outlined),
          onPressed: () => context.push('/offers/${widget.offerId}/messages')),
      ]),
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 720),
            child: _buildBody(),
          ),
        ),
      ),
    );
  }

  Widget _buildBody() {
    switch (_phase) {
      case _Phase.loading:
        return const Center(child: CircularProgressIndicator());
      case _Phase.error:
        return Padding(
          padding: const EdgeInsets.all(FgTokens.space4),
          child: FgStatePanel(
            label: _t('stateError'),
            message: _error ?? _t('unknown'),
            kind: FgStatusKind.error,
          ),
        );
      case _Phase.ready:
        return _buildReady(_offer!);
    }
  }

  Widget _buildReady(Offer o) {
    final theme = Theme.of(context);
    return SingleChildScrollView(
      padding: const EdgeInsets.all(FgTokens.space4),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          FgStatusBadge(
            label: o.status,
            kind: o.isAccepted
                ? FgStatusKind.success
                : o.isRejected
                    ? FgStatusKind.error
                    : o.isWithdrawn
                        ? FgStatusKind.warning
                        : FgStatusKind.info,
          ),
          const SizedBox(height: FgTokens.space4),
          Text(o.providerName ?? _t('provider'),
              style: theme.textTheme.titleLarge),
          const SizedBox(height: FgTokens.space2),
          Text(o.priceLabel(), style: theme.textTheme.headlineMedium),
          const SizedBox(height: FgTokens.space4),
          if (o.proposalMessage != null && o.proposalMessage!.isNotEmpty)
            _field('Proposal', o.proposalMessage!),
          if (o.deliveryTimeText != null && o.deliveryTimeText!.isNotEmpty)
            _field('Delivery', o.deliveryTimeText!),
          if (o.availabilityText != null && o.availabilityText!.isNotEmpty)
            _field('Availability', o.availabilityText!),
          if (o.additionalNotes != null && o.additionalNotes!.isNotEmpty)
            _field('Notes', o.additionalNotes!),
          const SizedBox(height: FgTokens.space6),
          if (o.isPending) ...[
            FgButton(
              label: 'Accept',
              isLoading: _actionInFlight,
              onPressed: _actionInFlight ? null : _accept,
            ),
            const SizedBox(height: FgTokens.space3),
            FgButton(
              label: 'Reject',
              variant: FgButtonVariant.secondary,
              onPressed: _actionInFlight ? null : _reject,
            ),
            const SizedBox(height: FgTokens.space3),
            FgButton(
              label: 'Withdraw',
              variant: FgButtonVariant.secondary,
              onPressed: _actionInFlight ? null : _withdraw,
            ),
          ],
          const SizedBox(height: FgTokens.space4),
          FgButton(
            label: _t('cancel'),
            variant: FgButtonVariant.secondary,
            onPressed: () => context.pop(),
          ),
        ],
      ),
    );
  }

  Widget _field(String label, String value) {
    final theme = Theme.of(context);
    return Padding(
      padding: const EdgeInsets.only(bottom: FgTokens.space3),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: theme.textTheme.labelMedium),
          const SizedBox(height: 4),
          Text(value, style: theme.textTheme.bodyLarge),
        ],
      ),
    );
  }
}
