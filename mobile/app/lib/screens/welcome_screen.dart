import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

/// S001 — Welcome (canonical Felagi entry screen).
///
/// Design contract:
///   - path: /welcome
///   - title_key: screenS001
///   - primary_action: signIn (Telegram OIDC)
class WelcomeScreen extends StatelessWidget {
  const WelcomeScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;

  String _t(String key) => fgText(localeCode, key);

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      appBar: AppBar(
        title: Text(_t('brand')),
        actions: [
          IconButton(
            tooltip: _t('language'),
            icon: const Icon(Icons.language),
            onPressed: () => onLocaleChange(localeCode == 'am' ? 'en' : 'am'),
          ),
        ],
      ),
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 480),
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(FgTokens.space4),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Text(
                    _t('screenS001'),
                    style: theme.textTheme.displayMedium,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 24),
                  Text(
                    _t('purpose'),
                    style: theme.textTheme.bodyLarge,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 24),
                  FgButton(
                    label: _t('signIn'),
                    onPressed: () => context.push('/auth/telegram'),
                  ),
                  const SizedBox(height: 12),
                  Text(
                    _t('authHelp'),
                    style: theme.textTheme.bodyMedium,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 32),
                  Text(
                    _t('previewNotice'),
                    style: theme.textTheme.labelMedium,
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
