import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import '../app_scope.dart';

/// Server-backed collection, preserving loaded rows on a pagination failure.
class RemoteCollection extends StatefulWidget {
  const RemoteCollection({super.key, required this.path, required this.localeCode,
    required this.row, this.header, this.paginated = false});
  final String path, localeCode;
  final Widget Function(BuildContext, Map<String, dynamic>, VoidCallback) row;
  final Widget? header;
  final bool paginated;
  @override
  State<RemoteCollection> createState() => _RemoteCollectionState();
}
class _RemoteCollectionState extends State<RemoteCollection> {
  List<Map<String, dynamic>> _rows = [];
  bool _busy = false, _more = false;
  int _page = 0;
  String? _error;
  String t(String key) => fgText(widget.localeCode, key);
  @override
  void initState() { super.initState(); WidgetsBinding.instance.addPostFrameCallback((_) => _load()); }
  Future<void> _load({bool more = false}) async {
    if (!mounted || _busy) return;
    setState(() { _busy = true; _error = null; });
    try {
      final page = more ? _page + 1 : 1;
      final envelope = await AppScope.of(context).client.getEnvelope(widget.path,
        query: widget.paginated ? {'page': '$page'} : null);
      final rows = (envelope['data'] as List).map((e) => Map<String, dynamic>.from(e as Map)).toList();
      if (!mounted) return;
      setState(() {
        _rows = more ? [..._rows, ...rows] : rows;
        _page = page;
        _more = widget.paginated && _rows.length < ((envelope['meta']?['total'] as num?) ?? _rows.length);
      });
    } catch (_) {
      if (mounted) setState(() => _error = t('loadRecovery'));
    } finally { if (mounted) setState(() => _busy = false); }
  }
  @override
  Widget build(BuildContext context) => ListView(
    key: PageStorageKey(widget.path),
    padding: const EdgeInsets.all(FgTokens.space4),
    children: [
      if (widget.header != null) widget.header!,
      if (_busy) const LinearProgressIndicator(),
      if (_error != null) Text(_error!, semanticsLabel: _error),
      TextButton(onPressed: _busy ? null : () => _load(), child: Text(t('refresh'))),
      if (!_busy && _error == null && _rows.isEmpty) Text(t('collectionEmpty')),
      for (final row in _rows) widget.row(context, row, () => _load()),
      if (_more) TextButton(onPressed: _busy ? null : () => _load(more: true), child: Text(t('loadMore'))),
    ],
  );
}
