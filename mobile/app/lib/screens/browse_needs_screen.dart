import 'dart:async';

import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

import '../api/api_exception.dart';
import '../api/models/need.dart';
import '../app_scope.dart';

/// S004 — Browse Needs (real API).
///
/// States: loading · list · empty · error · offline
/// API: GET /api/v1/needs (paginated, public)
class BrowseNeedsScreen extends StatefulWidget {
  const BrowseNeedsScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;

  @override
  State<BrowseNeedsScreen> createState() => _BrowseNeedsScreenState();
}

enum _Phase { loading, list, empty, error }

class _BrowseNeedsScreenState extends State<BrowseNeedsScreen> {
  final _searchCtrl = TextEditingController();
  Timer? _debounce;

  _Phase _phase = _Phase.loading;
  List<Need> _needs = const [];
  String? _error;

  String _t(String key) => fgText(widget.localeCode, key);

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _load());
  }

  @override
  void dispose() {
    _debounce?.cancel();
    _searchCtrl.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    if (!mounted) return;
    setState(() {
      _phase = _Phase.loading;
      _error = null;
    });

    try {
      final api = AppScope.of(context).needsApi;
      final result = await api.listNeeds(
        keyword: _searchCtrl.text.trim().isEmpty
            ? null
            : _searchCtrl.text.trim(),
        perPage: 20,
      );
      if (!mounted) return;
      setState(() {
        _needs = result.items;
        _phase = result.items.isEmpty ? _Phase.empty : _Phase.list;
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

  void _onSearchChanged(String _) {
    _debounce?.cancel();
    _debounce = Timer(const Duration(milliseconds: 400), _load);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(_t('screenS004')),
        actions: [
          IconButton(tooltip: _t('screenS018'), icon: const Icon(Icons.notifications_outlined),
            onPressed: () => context.push('/notifications')),
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
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Padding(
                  padding: const EdgeInsets.all(FgTokens.space4),
                  child: FgSearchField(
                    label: _t('fieldKeyword'),
                    controller: _searchCtrl,
                    onChanged: _onSearchChanged,
                    clearLabel: _t('clear'),
                    onClear: () {
                      _searchCtrl.clear();
                      _load();
                    },
                  ),
                ),
                Expanded(child: _buildBody()),
              ],
            ),
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
            label: _t('stateEmpty'),
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
            padding: const EdgeInsets.symmetric(horizontal: FgTokens.space4),
            itemCount: _needs.length,
            itemBuilder: (context, i) => _needCard(_needs[i]),
          ),
        );
    }
  }

  Widget _needCard(Need n) {
    final days = n.daysUntilDeadline();
    final meta = <String>[
      if (days != null) '${_t('cardDeadline')}: ${days}d',
      '${n.offerCount} ${_t('offers')}',
    ].join(' • ');

    return Padding(
      padding: const EdgeInsets.only(bottom: FgTokens.space3),
      child: FgNeedCard(
        title: n.title,
        category: n.category?.name(widget.localeCode) ?? _t('unknown'),
        location: n.locationText ?? _t('unknown'),
        offersSummary: meta,
        budget: n.hasBudget ? n.budgetLabel() : null,
        onTap: () => context.push('/needs/${n.id}'),
      ),
    );
  }
}
