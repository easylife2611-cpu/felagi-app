import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

import '../api/api_exception.dart';
import '../api/models/offer.dart';
import '../app_scope.dart';

/// S013 — My Offers (offers I submitted as a provider).
class MyOffersScreen extends StatefulWidget {
  const MyOffersScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;

  @override
  State<MyOffersScreen> createState() => _MyOffersScreenState();
}

enum _Phase { loading, list, empty, error }

class _MyOffersScreenState extends State<MyOffersScreen> {
  _Phase _phase = _Phase.loading;
  List<Offer> _offers = const [];
  String? _error;

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
      final offers = await api.listMyOffers();
      if (!mounted) return;
      setState(() {
        _offers = offers;
        _phase = offers.isEmpty ? _Phase.empty : _Phase.list;
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(_t('screenS013'))),
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
      case _Phase.empty:
        return Padding(
          padding: const EdgeInsets.all(FgTokens.space4),
          child: FgStatePanel(
            label: _t('stateEmpty'),
            message: _t('stateEmptyOffers'),
            kind: FgStatusKind.neutral,
          ),
        );
      case _Phase.list:
        return ListView.separated(
          padding: const EdgeInsets.all(FgTokens.space4),
          itemCount: _offers.length,
          separatorBuilder: (_, _) => const SizedBox(height: FgTokens.space3),
          itemBuilder: (context, i) => _offerCard(_offers[i]),
        );
    }
  }

  Widget _offerCard(Offer o) {
    final shortId = o.id.length >= 8 ? o.id.substring(0, 8) : o.id;
    final title = o.needTitle ?? '${_t('offer')} $shortId';
    final meta = <String>[
      o.priceLabel(),
      o.status,
      if (o.deliveryTimeText != null && o.deliveryTimeText!.isNotEmpty)
        o.deliveryTimeText!,
    ].join(' • ');

    return FgNeedCard(
      title: title,
      category: _t('offer'),
      location: o.availabilityText ?? _t('unknown'),
      offersSummary: meta,
      budget: null,
      onTap: () => context.push('/offers/${o.id}'),
    );
  }
}
