import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

import '../api/api_exception.dart';
import '../api/api_config.dart';
import '../api/models/offer.dart';
import '../app_scope.dart';

/// S014 — Compare Confirmation.
class CompareConfirmScreen extends StatefulWidget {
  const CompareConfirmScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    required this.needId,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;
  final String needId;

  @override
  State<CompareConfirmScreen> createState() => _CompareConfirmScreenState();
}

enum _Phase { loading, ready, error }

class _CompareConfirmScreenState extends State<CompareConfirmScreen> {
  _Phase _phase = _Phase.loading;
  List<Offer> _offers = const [];
  String? _error;
  bool _confirming = false;

  String _t(String key) => fgText(widget.localeCode, key);

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _load());
  }

  Future<void> _load() async {
    if (!mounted) return;
    setState(() {
      _phase = _Phase.loading;
      _error = null;
    });
    try {
      final api = AppScope.of(context).offersApi;
      final offers = await api.listForNeed(widget.needId);
      if (!mounted) return;
      setState(() {
        _offers = offers;
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

  Future<void> _confirm() async {
    if (_confirming) return;
    setState(() => _confirming = true);
    try {
      final result = await AppScope.of(context).client.post(ApiConfig.needComparisons(widget.needId));
      if (!mounted) return;
      final id = result['comparison_id'];
      if (id == null) throw const FormatException('Missing comparison id');
      context.push('/comparisons/${Uri.encodeComponent(id.toString())}');
    } catch (_) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(_t('loadRecovery'))));
    } finally { if (mounted) setState(() => _confirming = false); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(_t('screenS014'))),
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
        return _buildReady();
    }
  }

  Widget _buildReady() {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.all(FgTokens.space4),
          child: FgStatusBadge(
            label: _t('compareAllEligible'),
            kind: FgStatusKind.info,
          ),
        ),
        Expanded(
          child: ListView.builder(
            padding: const EdgeInsets.symmetric(horizontal: FgTokens.space4),
            itemCount: _offers.length,
            itemBuilder: (context, i) => _offerRow(_offers[i]),
          ),
        ),
        Padding(
          padding: const EdgeInsets.all(FgTokens.space4),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              FgButton(
                label: _t('confirm'),
                isLoading: _confirming,
                onPressed: _offers.where((o) => o.status == 'PENDING').isEmpty || _confirming ? null : _confirm,
              ),
              const SizedBox(height: FgTokens.space3),
              FgButton(
                label: _t('cancel'),
                variant: FgButtonVariant.secondary,
                onPressed: _confirming ? null : () => context.pop(),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _offerRow(Offer o) {
    return ListTile(
      title: Text(o.providerName ?? _t('provider')),
      subtitle: Text('${o.priceLabel()} • ${o.status}'),
    );
  }
}
