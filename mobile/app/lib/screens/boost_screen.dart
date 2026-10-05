import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:url_launcher/url_launcher.dart';

import '../api/api_exception.dart';
import '../api/models/boost_package.dart';
import '../app_scope.dart';

/// S019 — Boost/Payments (real · L352).
///
/// Flow:
///   GET /boost-packages → select → POST /needs/{id}/boosts
///   → 201 { boost, payment.checkout_url } → launch browser
class BoostScreen extends StatefulWidget {
  const BoostScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    required this.needId,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;
  final String needId;

  @override
  State<BoostScreen> createState() => _BoostScreenState();
}

enum _BoostPhase { loading, packages, checkout, pending, failed, disabled }

class _BoostScreenState extends State<BoostScreen> {
  _BoostPhase _phase = _BoostPhase.loading;
  List<BoostPackage> _packages = const [];
  BoostPackage? _selected;
  PaymentInfo? _payment;
  String? _error;

  String _t(String key) => fgText(widget.localeCode, key);

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _loadPackages());
  }

  Future<void> _loadPackages() async {
    if (!mounted) return;
    setState(() {
      _phase = _BoostPhase.loading;
      _error = null;
    });

    try {
      final api = AppScope.of(context).boostApi;
      final list = await api.listPackages();
      if (!mounted) return;
      setState(() {
        _packages = list.where((p) => p.active).toList(growable: false);
        _phase = _packages.isEmpty ? _BoostPhase.disabled : _BoostPhase.packages;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _error = e.message;
        _phase = _BoostPhase.failed;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = '$e';
        _phase = _BoostPhase.failed;
      });
    }
  }

  Future<void> _checkout() async {
    final pkg = _selected;
    if (pkg == null) return;

    setState(() {
      _phase = _BoostPhase.checkout;
      _error = null;
    });

    // Generate a stable idempotency key (timestamp+package = unique per attempt)
    final idemKey =
        'boost-${widget.needId}-${pkg.id}-${DateTime.now().millisecondsSinceEpoch}';

    try {
      final api = AppScope.of(context).boostApi;
      final result = await api.initiateBoost(
        needId: widget.needId,
        packageId: pkg.id,
        idempotencyKey: idemKey,
      );

      if (!mounted) return;
      _payment = result.payment;

      // If a checkout URL was returned, open it in the browser.
      final url = result.payment?.checkoutUrl;
      if (url != null && url.isNotEmpty) {
        await launchUrl(
          Uri.parse(url),
          mode: LaunchMode.externalApplication,
        );
      }

      if (!mounted) return;
      setState(() => _phase = _BoostPhase.pending);
    } on ApiException catch (e) {
      if (!mounted) return;
      // Map canonical backend errors to friendly messages.
      String message = e.message;
      if (e.code == ApiException.boostActive) {
        message = _t('boostActive');
      } else if (e.code == ApiException.paymentsDisabled) {
        message = _t('featurePendingBody');
      }
      setState(() {
        _error = message;
        _phase = _BoostPhase.failed;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = '$e';
        _phase = _BoostPhase.failed;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(_t('screenS019')),
        leading: Navigator.of(context).canPop()
            ? IconButton(
                icon: const Icon(Icons.arrow_back),
                onPressed: () => Navigator.of(context).pop(),
              )
            : null,
        actions: [
          IconButton(
            tooltip: _t('language'),
            icon: const Icon(Icons.language),
            onPressed: () => widget.onLocaleChange(
              widget.localeCode == 'am' ? 'en' : 'am',
            ),
          ),
        ],
      ),
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 720),
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(FgTokens.space4),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  FgStatusBadge(label: _phaseLabel(), kind: _phaseKind()),
                  const SizedBox(height: FgTokens.space4),
                  Text(
                    _t('paymentHelp'),
                    style: Theme.of(context).textTheme.bodyMedium,
                  ),
                  const SizedBox(height: FgTokens.space4),
                  ..._buildBody(),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  String _phaseLabel() => switch (_phase) {
        _BoostPhase.loading => _t('stateLoading'),
        _BoostPhase.packages => _t('statePackages'),
        _BoostPhase.checkout => _t('stateCheckout'),
        _BoostPhase.pending => _t('statePending'),
        _BoostPhase.failed => _t('stateFailed'),
        _BoostPhase.disabled => _t('stateDisabled'),
      };

  FgStatusKind _phaseKind() => switch (_phase) {
        _BoostPhase.loading => FgStatusKind.neutral,
        _BoostPhase.packages => FgStatusKind.info,
        _BoostPhase.checkout || _BoostPhase.pending => FgStatusKind.warning,
        _BoostPhase.failed => FgStatusKind.error,
        _BoostPhase.disabled => FgStatusKind.neutral,
      };

  List<Widget> _buildBody() {
    switch (_phase) {
      case _BoostPhase.loading:
        return const [
          Center(
            child: Padding(
              padding: EdgeInsets.all(FgTokens.space6),
              child: CircularProgressIndicator(),
            ),
          ),
        ];

      case _BoostPhase.packages:
        return [
          ..._packages.map((p) => Padding(
                padding: const EdgeInsets.only(bottom: FgTokens.space3),
                child: _packageCard(p),
              )),
          const SizedBox(height: FgTokens.space3),
          FgButton(
            label: _t('checkout'),
            onPressed: _selected != null ? _checkout : null,
          ),
          const SizedBox(height: FgTokens.space3),
          FgButton(
            label: _t('cancel'),
            variant: FgButtonVariant.secondary,
            onPressed: () => Navigator.of(context).maybePop(),
          ),
        ];

      case _BoostPhase.checkout:
        return [
          FgStatePanel(
            label: _t('stateCheckout'),
            message: _t('paymentHelp'),
            kind: FgStatusKind.info,
          ),
        ];

      case _BoostPhase.pending:
        return [
          FgStatePanel(
            label: _t('statePending'),
            message: _payment?.hasCheckout == true
                ? _t('paymentHelp')
                : _t('simulation'),
            kind: FgStatusKind.warning,
          ),
          const SizedBox(height: FgTokens.space3),
          if (_payment?.hasCheckout == true)
            FgButton(
              label: _t('checkout'),
              onPressed: () async {
                final url = _payment!.checkoutUrl!;
                await launchUrl(Uri.parse(url),
                    mode: LaunchMode.externalApplication);
              },
            ),
          const SizedBox(height: FgTokens.space3),
          FgButton(
            label: _t('back'),
            variant: FgButtonVariant.secondary,
            onPressed: () => Navigator.of(context).maybePop(),
          ),
        ];

      case _BoostPhase.failed:
      case _BoostPhase.disabled:
        return [
          FgStatePanel(
            label: _phaseLabel(),
            message: _error ?? _t('unknown'),
            kind: _phase == _BoostPhase.failed
                ? FgStatusKind.error
                : FgStatusKind.neutral,
          ),
          const SizedBox(height: FgTokens.space3),
          FgButton(
            label: _t('retry'),
            onPressed: _loadPackages,
          ),
        ];
    }
  }

  Widget _packageCard(BoostPackage p) {
    final isSelected = _selected?.id == p.id;
    final theme = Theme.of(context);
    return Card(
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(FgTokens.radiusControl),
        side: BorderSide(
          color:
              isSelected ? theme.colorScheme.primary : theme.dividerColor,
          width: isSelected ? 2 : 1,
        ),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(FgTokens.radiusControl),
        onTap: () => setState(() => _selected = p),
        child: Padding(
          padding: const EdgeInsets.all(FgTokens.space4),
          child: Row(
            children: [
              Icon(
                isSelected
                    ? Icons.radio_button_checked
                    : Icons.radio_button_unchecked,
                color: isSelected
                    ? theme.colorScheme.primary
                    : theme.colorScheme.outline,
              ),
              const SizedBox(width: FgTokens.space3),
              Expanded(
                child: Text(
                  '${p.durationDays} ${_t('packageDays')}',
                  style: theme.textTheme.titleMedium,
                ),
              ),
              Text(p.priceLabel(), style: theme.textTheme.titleLarge),
            ],
          ),
        ),
      ),
    );
  }
}
