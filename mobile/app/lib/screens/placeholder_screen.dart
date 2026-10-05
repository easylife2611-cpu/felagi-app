import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';

/// Generic placeholder for screens not yet implemented.
///
/// Displays: canonical screen ID, title (via title_key), path.
/// Shows explicit "placeholder" notice — no fake success.
class PlaceholderScreen extends StatelessWidget {
  const PlaceholderScreen({
    super.key,
    required this.screenId,
    required this.titleKey,
    required this.path,
    required this.localeCode,
  });

  final String screenId;
  final String titleKey;
  final String path;
  final String localeCode;

  String _t(String key) => fgText(localeCode, key);

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      appBar: AppBar(
        title: Text(_t(titleKey)),
        leading: Navigator.of(context).canPop()
            ? IconButton(
                icon: const Icon(Icons.arrow_back),
                onPressed: () => Navigator.of(context).pop(),
              )
            : null,
      ),
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 480),
            child: Padding(
              padding: const EdgeInsets.all(FgTokens.space4),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Text(
                    screenId,
                    style: theme.textTheme.displayMedium,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 8),
                  Text(
                    _t(titleKey),
                    style: theme.textTheme.titleLarge,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    path,
                    style: theme.textTheme.bodyMedium,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 32),
                  FgStatusBadge(label: _t('statePreview'), kind: FgStatusKind.info),
                  const SizedBox(height: 16),
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
