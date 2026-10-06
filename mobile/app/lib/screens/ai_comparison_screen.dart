import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import '../api/api_config.dart';
import '../app_scope.dart';

/// Displays only the server-authorized comparison projection; never selects an Offer.
class AiComparisonScreen extends StatefulWidget {
  const AiComparisonScreen({super.key, required this.localeCode, required this.comparisonId});
  final String localeCode, comparisonId;
  @override
  State<AiComparisonScreen> createState() => _AiComparisonScreenState();
}
class _AiComparisonScreenState extends State<AiComparisonScreen> {
  Map<String, dynamic>? _data;
  bool _busy = false;
  String? _error;
  String t(String key) => fgText(widget.localeCode, key);
  @override
  void initState() { super.initState(); WidgetsBinding.instance.addPostFrameCallback((_) => _load()); }
  Future<void> _load() async {
    if (!mounted || _busy) return;
    setState(() { _busy = true; _error = null; });
    try {
      final data = await AppScope.of(context).client.get(ApiConfig.comparisonShow(widget.comparisonId));
      if (mounted) setState(() => _data = Map<String, dynamic>.from(data as Map));
    } catch (_) { if (mounted) setState(() => _error = t('loadRecovery')); }
    finally { if (mounted) setState(() => _busy = false); }
  }
  String value(dynamic v) => v == null ? t('unknown') : v is List ? (v.isEmpty ? t('noneReported') : v.join(' • ')) : v.toString();
  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(title: Text(t('screenS015'))),
    body: SafeArea(child: ListView(padding: const EdgeInsets.all(FgTokens.space4), children: [
      Text(t('advisory')),
      if (_busy) const LinearProgressIndicator(),
      if (_error != null) Text(_error!),
      TextButton(onPressed: _busy ? null : _load, child: Text(t('refresh'))),
      if (_data != null) ...[
        Text('${t('status')}: ${value(_data!['status'])}'),
        Text('${t('updated')}: ${value(_data!['completed_at'])}'),
        if ((_data!['results'] as List? ?? []).isEmpty) Text(t('comparisonWaiting')),
        for (final result in (_data!['results'] as List? ?? [])) Card(child: Padding(
          padding: const EdgeInsets.all(FgTokens.space4),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text('${t('offer')}: ${value(result['comparison_offer_id'])}'),
            Text('${t('scoreLabel')}: ${value(result['score'])}'),
            Text(value(result['fit_explanation'])),
            for (final field in ['criterion_scores', 'strengths', 'weaknesses', 'missing_information', 'risk_notes'])
              Text('${t(field)}: ${value(result[field])}'),
          ]),
        )),
      ],
    ])),
  );
}
