import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:felagi_design_system/felagi_design_system.dart';

import '../api/api_config.dart';
import '../api/api_exception.dart';
import '../app_scope.dart';

/// S020 — Rating (submit a rating for the other party of a completed need).
///
/// Path: /needs/:id/rating
/// API: POST /needs/{needId}/ratings
class RatingScreen extends StatefulWidget {
  const RatingScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    required this.needId,
    this.toUserId,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;
  final String needId;
  final String? toUserId;

  @override
  State<RatingScreen> createState() => _RatingScreenState();
}

class _RatingScreenState extends State<RatingScreen> {
  int _score = 4;
  final _reviewCtrl = TextEditingController();
  bool _submitting = false;
  String? _error;
  bool _done = false;

  String _t(String key) => fgText(widget.localeCode, key);

  @override
  void dispose() {
    _reviewCtrl.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    final toUser = widget.toUserId?.trim() ?? '';
    if (toUser.isEmpty) {
      setState(() => _error = _t('unknown'));
      return;
    }
    setState(() {
      _submitting = true;
      _error = null;
    });
    try {
      final client = AppScope.of(context).client;
      await client.post(
        ApiConfig.ratingStore(widget.needId),
        body: {
          'to_user_id': toUser,
          'score': _score,
          if (_reviewCtrl.text.trim().isNotEmpty)
            'review': _reviewCtrl.text.trim(),
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
      appBar: AppBar(title: Text(_t('screenS020'))),
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
                      message: _t('screenS020'),
                      kind: FgStatusKind.success,
                    ),
                    const SizedBox(height: FgTokens.space6),
                    FgButton(
                      label: _t('cancel'),
                      onPressed: () => context.pop(),
                    ),
                  ] else ...[
                    Text(
                      _t('screenS020'),
                      style: theme.textTheme.titleLarge,
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: FgTokens.space4),

                    // Star selector
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: List.generate(5, (i) {
                        final star = i + 1;
                        return IconButton(
                          onPressed: _submitting
                              ? null
                              : () => setState(() => _score = star),
                          icon: Icon(
                            star <= _score
                                ? Icons.star_rounded
                                : Icons.star_outline_rounded,
                            color: FgTokens.orange,
                            size: 40,
                          ),
                        );
                      }),
                    ),
                    const SizedBox(height: FgTokens.space3),
                    Text(
                      '$_score ${_t('priceWeight')}',
                      textAlign: TextAlign.center,
                      style: theme.textTheme.bodyLarge,
                    ),
                    const SizedBox(height: FgTokens.space6),

                    FgTextField(
                      label: _t('fieldDescription'),
                      controller: _reviewCtrl,
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
