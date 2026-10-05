import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

/// S007 — Need-created confirmation (canonical user screen).
///
/// Path: /needs/:id/created
/// Title key: screenS007
///
/// Purpose: post-create confirmation. Shows success, offers next steps.
///
/// Data flow:
///   S005 (Create) → POST /needs → navigate S007 with needId
///   S007 → "View need" → S008 (/needs/:id)
///   S007 → "Browse"    → S004 (/browse)
///
/// Safety:
///   - No API call. Need was already created by S005.
///   - needId comes from route param (may be empty if deep-linked).
///   - Read-only confirmation screen.
class NeedCreatedScreen extends StatelessWidget {
  const NeedCreatedScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    required this.needId,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;
  final String needId;

  String _t(String key) => fgText(localeCode, key);

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final hasId = needId.isNotEmpty;

    return Scaffold(
      appBar: AppBar(
        title: Text(_t('screenS007')),
        automaticallyImplyLeading: false,
        actions: [
          IconButton(
            tooltip: _t('language'),
            icon: const Icon(Icons.language),
            onPressed: () =>
                onLocaleChange(localeCode == 'am' ? 'en' : 'am'),
          ),
        ],
      ),
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 720),
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(FgTokens.space4),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  FgStatusBadge(
                    label: _t('stateSuccess'),
                    kind: FgStatusKind.success,
                  ),
                  const SizedBox(height: FgTokens.space4),

                  Center(
                    child: Icon(
                      Icons.check_circle_outline,
                      size: 72,
                      color: theme.colorScheme.primary,
                    ),
                  ),
                  const SizedBox(height: FgTokens.space4),

                  Text(
                    _t('screenS007'),
                    style: theme.textTheme.headlineSmall,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: FgTokens.space3),

                  Text(
                    _t('needSuccessNext'),
                    style: theme.textTheme.bodyLarge,
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: FgTokens.space4),

                  if (hasId)
                    FgStatePanel(
                      label: _t('fieldRecordId'),
                      message: needId,
                      kind: FgStatusKind.neutral,
                    ),
                  if (hasId) const SizedBox(height: FgTokens.space4),

                  FgButton(
                    label: _t('viewNeed'),
                    onPressed: hasId
                        ? () => context.go('/needs/$needId')
                        : null,
                  ),
                  const SizedBox(height: FgTokens.space3),

                  FgButton(
                    label: _t('createNeed'),
                    variant: FgButtonVariant.secondary,
                    onPressed: () => context.go('/needs/new'),
                  ),
                  const SizedBox(height: FgTokens.space3),

                  FgButton(
                    label: _t('back'),
                    variant: FgButtonVariant.secondary,
                    onPressed: () => context.go('/browse'),
                  ),
                  const SizedBox(height: FgTokens.space4),

                  FgStatePanel(
                    label: _t('statePreview'),
                    message: _t('noFakeSuccess'),
                    kind: FgStatusKind.neutral,
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
