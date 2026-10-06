import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';

import '../api/api_exception.dart';
import '../api/models/need.dart';
import '../app_scope.dart';

/// S008 — Need Details (real API).
///
/// States: loading · content · error · owner-actions
/// API:
///   - GET /needs/{id}
///   - POST /needs/{id}/cancel  (owner)
///   - POST /needs/{id}/complete (owner)
class NeedDetailScreen extends StatefulWidget {
  const NeedDetailScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    required this.needId,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;
  final String needId;

  @override
  State<NeedDetailScreen> createState() => _NeedDetailScreenState();
}

enum _Phase { loading, content, error }

class _NeedDetailScreenState extends State<NeedDetailScreen> {
  _Phase _phase = _Phase.loading;
  Need? _need;
  String? _error;
  bool _busy = false;

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
      final api = AppScope.of(context).needsApi;
      final need = await api.showNeed(widget.needId);
      if (!mounted) return;
      setState(() {
        _need = need;
        _phase = _Phase.content;
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

  Future<void> _cancelNeed() async {
    if (_busy) return;
    setState(() => _busy = true);
    try {
      final api = AppScope.of(context).needsApi;
      final updated = await api.cancelNeed(widget.needId);
      if (!mounted) return;
      setState(() {
        _need = updated;
        _busy = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(_t('simulation'))),
      );
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => _busy = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(e.message)),
      );
    }
  }

  Future<void> _completeNeed() async {
    if (_busy) return;
    setState(() => _busy = true);
    try {
      final api = AppScope.of(context).needsApi;
      final updated = await api.completeNeed(widget.needId);
      if (!mounted) return;
      setState(() {
        _need = updated;
        _busy = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(_t('simulation'))),
      );
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => _busy = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(e.message)),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(_t('screenS008')),
        leading: Navigator.of(context).canPop()
            ? IconButton(
                icon: const Icon(Icons.arrow_back),
                onPressed: () => Navigator.of(context).pop(),
              )
            : null,
        actions: [
          IconButton(tooltip: _t('screenS016'), icon: const Icon(Icons.history),
            onPressed: () => context.push('/needs/${widget.needId}/comparisons')),
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

      case _Phase.content:
        final n = _need!;
        return _content(n);
    }
  }

  Widget _content(Need n) {
    final theme = Theme.of(context);
    return SingleChildScrollView(
      padding: const EdgeInsets.all(FgTokens.space4),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Status badge
          Row(children: [
            FgStatusBadge(
              label: n.status,
              kind: n.isOpen ? FgStatusKind.success : FgStatusKind.neutral,
            ),
            const SizedBox(width: FgTokens.space2),
            FgStatusBadge(
              label: '#${n.id.length >= 8 ? n.id.substring(0, 8) : n.id}',
              kind: FgStatusKind.neutral,
            ),
          ]),
          const SizedBox(height: FgTokens.space4),

          // Title
          Text(n.title, style: theme.textTheme.titleLarge),
          const SizedBox(height: FgTokens.space3),

          // Description
          Text(n.description, style: theme.textTheme.bodyLarge),
          const SizedBox(height: FgTokens.space4),

          // Details
          _detailRow(theme, _t('fieldCategoryId'),
              n.category?.name(widget.localeCode) ?? _t('unknown')),
          _detailRow(theme, _t('budget'), n.hasBudget ? n.budgetLabel() : '—'),
          _detailRow(theme, _t('fieldLocationText'),
              n.locationText ?? _t('unknown')),
          _detailRow(theme, _t('cardOfferCount'), '${n.offerCount}'),
          if (n.daysUntilDeadline() != null)
            _detailRow(
                theme, _t('cardDeadline'), '${n.daysUntilDeadline()}d'),

          // Owner actions
          if (n.isOwner && n.isOpen) ...[
            const SizedBox(height: FgTokens.space6),
            FgButton(
              label: _t('completeNeed'),
              isLoading: _busy,
              onPressed: _busy ? null : _completeNeed,
            ),
            const SizedBox(height: FgTokens.space3),
            FgButton(
              label: _t('cancelNeed'),
              variant: FgButtonVariant.destructive,
              onPressed: _busy ? null : _cancelNeed,
            ),
          ] else if (n.isOwner) ...[
            const SizedBox(height: FgTokens.space6),
            FgStatusBadge(
              label: n.status,
              kind: FgStatusKind.neutral,
            ),
          ],
        ],
      ),
    );
  }

  Widget _detailRow(ThemeData theme, String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: FgTokens.space2),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 140,
            child: Text(label, style: theme.textTheme.bodyMedium),
          ),
          Expanded(
            child: Text(value, style: theme.textTheme.bodyLarge),
          ),
        ],
      ),
    );
  }
}
