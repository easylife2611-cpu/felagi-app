import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:felagi_design_system/felagi_design_system.dart';

import '../api/api_config.dart';
import '../api/api_exception.dart';
import '../app_scope.dart';

/// S021 — Report (submit a report on a Need/Offer/Message/User).
///
/// Path: /support/report (query: entity_type, entity_id)
/// API: POST /reports
class ReportScreen extends StatefulWidget {
  const ReportScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    this.entityType,
    this.entityId,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;
  final String? entityType; // NEED | OFFER | MESSAGE | USER
  final String? entityId;

  @override
  State<ReportScreen> createState() => _ReportScreenState();
}

class _ReportScreenState extends State<ReportScreen> {
  static const _reasonCodes = ['SPAM', 'HARASSMENT', 'FRAUD', 'INAPPROPRIATE', 'OTHER'];
  static const _entityTypes = ['NEED', 'OFFER', 'MESSAGE', 'USER'];

  String _reasonCode = _reasonCodes.first;
  String _entityType = 'NEED';
  final _detailsCtrl = TextEditingController();
  bool _submitting = false;
  String? _error;
  bool _done = false;

  String _t(String key) => fgText(widget.localeCode, key);

  @override
  void initState() {
    super.initState();
    if (widget.entityType != null && _entityTypes.contains(widget.entityType)) {
      _entityType = widget.entityType!;
    }
  }

  @override
  void dispose() {
    _detailsCtrl.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    final entityId = widget.entityId?.trim() ?? '';
    final details = _detailsCtrl.text.trim();
    if (entityId.isEmpty) {
      setState(() => _error = _t('unknown'));
      return;
    }
    if (details.length < 20) {
      setState(() => _error = _t('fieldDescription'));
      return;
    }
    setState(() {
      _submitting = true;
      _error = null;
    });
    try {
      final client = AppScope.of(context).client;
      await client.post(
        ApiConfig.reportStore,
        body: {
          'reason_code': _reasonCode,
          'entity_type': _entityType,
          'entity_id': entityId,
          'details': details,
        },
      );
      if (!mounted) return;
      setState(() {
        _submitting = false;
        _done = true;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _error = e.message;
        _submitting = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = '$e';
        _submitting = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      appBar: AppBar(title: Text(_t('screenS021'))),
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 720),
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(FgTokens.space4),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  if (_done) ...[
                    FgStatePanel(
                      label: _t('save'),
                      message: _t('screenS021'),
                      kind: FgStatusKind.success,
                    ),
                    const SizedBox(height: FgTokens.space6),
                    FgButton(
                      label: _t('cancel'),
                      onPressed: () => context.pop(),
                    ),
                  ] else ...[
                    Text(
                      _t('screenS021'),
                      style: theme.textTheme.titleLarge,
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: FgTokens.space4),
                    Text(_t('reportType'), style: theme.textTheme.labelLarge),
                    const SizedBox(height: FgTokens.space2),
                    DropdownButton<String>(
                      value: _reasonCode,
                      isExpanded: true,
                      onChanged: _submitting
                          ? null
                          : (v) => setState(() => _reasonCode = v ?? _reasonCode),
                      items: _reasonCodes
                          .map((c) => DropdownMenuItem(value: c, child: Text(c)))
                          .toList(),
                    ),
                    const SizedBox(height: FgTokens.space4),
                    FgTextField(
                      label: _t('fieldDescription'),
                      controller: _detailsCtrl,
                    ),
                    if (_error != null) ...[
                      const SizedBox(height: FgTokens.space4),
                      FgStatePanel(
                        label: _t('stateError'),
                        message: _error!,
                        kind: FgStatusKind.error,
                      ),
                    ],
                    const SizedBox(height: FgTokens.space6),
                    FgButton(
                      label: _t('save'),
                      isLoading: _submitting,
                      onPressed: _submitting ? null : _submit,
                    ),
                    const SizedBox(height: FgTokens.space3),
                    FgButton(
                      label: _t('cancel'),
                      variant: FgButtonVariant.secondary,
                      onPressed: _submitting ? null : () => context.pop(),
                    ),
                  ],
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
