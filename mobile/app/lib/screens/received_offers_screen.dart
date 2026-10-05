import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';

import '../api/api_exception.dart';
import '../api/models/offer.dart';
import '../app_scope.dart';

/// S010 — Received Offers (real API, auth + owner only).
///
/// Path: /needs/:id/offers
/// Title key: screenS010
///
/// API: GET /api/v1/needs/{needId}/offers (auth:sanctum, owner only)
///   response: {data: [Offer], message: "Offers retrieved."}
///   NOTE: backend does NOT paginate this endpoint.
///
/// States: loading · list · empty · error
/// Actions: tap offer card → /offers/:id (S012 detail — future)
class ReceivedOffersScreen extends StatefulWidget {
  const ReceivedOffersScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    required this.needId,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;
  final String needId;

  @override
  State<ReceivedOffersScreen> createState() =>
      _ReceivedOffersScreenState();
}

enum _Phase { loading, list, empty, error }

class _ReceivedOffersScreenState extends State<ReceivedOffersScreen> {
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
    if (widget.needId.isEmpty) {
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
      final offers = await api.listForNeed(widget.needId);
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
      appBar: AppBar(
        title: Text(_t('screenS010')),
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
            child: _buildBody(),
          ),
        ),
      ),
    );
  }

  Widget _buildBody() {
    switch (_phase) {
      case _Phase.loading:
        return const Center(
          child: Padding(
            padding: EdgeInsets.all(FgTokens.space6),
            child: CircularProgressIndicator(),
          ),
        );

      case _Phase.empty:
        return Padding(
          padding: const EdgeInsets.all(FgTokens.space4),
          child: FgStatePanel(
            label: _t('stateEmptyOffers'),
            message: _t('emptyHelp'),
            kind: FgStatusKind.neutral,
          ),
        );

      case _Phase.error:
        return Padding(
          padding: const EdgeInsets.all(FgTokens.space4),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              FgStatePanel(
                label: _t('stateError'),
                message: _error ?? _t('unknown'),
                kind: FgStatusKind.error,
              ),
              const SizedBox(height: FgTokens.space3),
              FgButton(label: _t('retry'), onPressed: _load),
            ],
          ),
        );

      case _Phase.list:
        return RefreshIndicator(
          onRefresh: _load,
          child: ListView.builder(
            padding: const EdgeInsets.all(FgTokens.space4),
            itemCount: _offers.length + 1, // +1 for header count
            itemBuilder: (context, i) {
              if (i == 0) {
                return Padding(
                  padding:
                      const EdgeInsets.only(bottom: FgTokens.space3),
                  child: FgStatusBadge(
                    label: '${_offers.length} ${_t('offers')}',
                    kind: FgStatusKind.info,
                  ),
                );
              }
              return _offerCard(_offers[i - 1]);
            },
          ),
        );
    }
  }

  Widget _offerCard(Offer o) {
    // Safe substring (HANDOFF Flutter rule): id may be shorter than 8 chars.
    final shortId = o.id.length >= 8 ? o.id.substring(0, 8) : o.id;

    final meta = <String>[
      o.priceLabel(),
      o.status,
      if (o.deliveryTimeText != null && o.deliveryTimeText!.isNotEmpty)
        o.deliveryTimeText!,
    ].join(' • ');

    return Padding(
      padding: const EdgeInsets.only(bottom: FgTokens.space3),
      child: FgNeedCard(
        title: o.providerName ?? _t('provider'),
        category: _t('offer'),
        location: o.availabilityText ?? _t('unknown'),
        offersSummary: meta,
        budget: null,
        onTap: () {
          // S012 (offer detail) is future work — show a placeholder.
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('${_t('view')} $shortId'),
            ),
          );
        },
      ),
    );
  }
}
