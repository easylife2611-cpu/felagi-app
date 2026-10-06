import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';

import '../api/api_config.dart';
import '../api/api_exception.dart';
import '../app_scope.dart';

/// S022 — Telegram Publication Status (owner-only).
///
/// Path: /needs/:id/publications
/// API: GET  /needs/{needId}/telegram-publications
///      POST /needs/{needId}/telegram-publication/stop
class TelegramStatusScreen extends StatefulWidget {
  const TelegramStatusScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    required this.needId,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;
  final String needId;

  @override
  State<TelegramStatusScreen> createState() => _TelegramStatusScreenState();
}

enum _Phase { loading, list, empty, error }

class _TelegramStatusScreenState extends State<TelegramStatusScreen> {
  _Phase _phase = _Phase.loading;
  List<Map<String, dynamic>> _items = const [];
  String? _error;
  bool _stopping = false;

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
      final client = AppScope.of(context).client;
      final data = await client.get(
        ApiConfig.telegramPublications(widget.needId),
        authenticated: true,
      );
      if (!mounted) return;
      final list = (data as List)
          .map((e) => (e as Map).cast<String, dynamic>())
          .toList(growable: false);
      setState(() {
        _items = list;
        _phase = list.isEmpty ? _Phase.empty : _Phase.list;
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

  Future<void> _stop(Map<String, dynamic> pub) async {
    final id = pub['id']?.toString() ?? '';
    if (id.isEmpty) return;
    setState(() => _stopping = true);
    try {
      final client = AppScope.of(context).client;
      await client.post(
        ApiConfig.telegramPublicationStop(widget.needId),
        body: {'publication_id': id},
      );
      if (!mounted) return;
      await _load();
    } on ApiException catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(e.message)),
      );
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('$e')),
      );
    } finally {
      if (mounted) setState(() => _stopping = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(_t('screenS022'))),
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
            message: _t('unknown'),
            kind: FgStatusKind.neutral,
          ),
        );
      case _Phase.list:
        return Column(
          children: [
            Padding(
              padding: const EdgeInsets.all(FgTokens.space4),
              child: FgStatusBadge(
                label: '${_items.length}',
                kind: FgStatusKind.info,
              ),
            ),
            Expanded(
              child: ListView.separated(
                padding: const EdgeInsets.symmetric(
                  horizontal: FgTokens.space4,
                ),
                itemCount: _items.length,
                separatorBuilder: (_, _) =>
                    const SizedBox(height: FgTokens.space3),
                itemBuilder: (context, i) => _row(_items[i]),
              ),
            ),
          ],
        );
    }
  }

  Widget _row(Map<String, dynamic> p) {
    final channel = p['channel_handle']?.toString() ?? '';
    final title = p['title']?.toString() ?? _t('unknown');
    final status = p['status']?.toString() ?? _t('unknown');
    final stopped = status.toUpperCase() == 'STOPPED';

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(FgTokens.space4),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(title, style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: FgTokens.space2),
            Text('@$channel', style: Theme.of(context).textTheme.bodyMedium),
            const SizedBox(height: FgTokens.space2),
            FgStatusBadge(
              label: status,
              kind: stopped ? FgStatusKind.warning : FgStatusKind.success,
            ),
            const SizedBox(height: FgTokens.space3),
            if (!stopped)
              FgButton(
                label: _t('cancel'),
                variant: FgButtonVariant.secondary,
                isLoading: _stopping,
                onPressed: _stopping ? null : () => _stop(p),
              ),
          ],
        ),
      ),
    );
  }
}
