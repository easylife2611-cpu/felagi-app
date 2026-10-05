import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';

/// S006 — Public-post preview (canonical user screen).
///
/// Path: /needs/new/public-preview
/// Title key: screenS006
///
/// Purpose: show what will be posted publicly (Telegram + public link)
/// before the requester confirms consent to publish.
///
/// Data flow:
///   S005 (Create/Edit Need) → S006 (preview + consent) → back to S005 save
///   All preview fields are optional constructor params. When absent, the
///   screen renders demo defaults so it can be viewed in isolation (tests,
///   deep links, dev preview).
///
/// Safety:
///   - No server call. No submission. No mutation.
///   - The consent checkbox is local-only.
///   - "Continue" simply pops back; the real submit is S005::_save().
///
/// Excludes (per `publicHelp`): phone, private address, attachments, messages.
class PublicPreviewScreen extends StatefulWidget {
  const PublicPreviewScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    this.previewTitle,
    this.previewDescription,
    this.previewCategoryName,
    this.previewLocation,
    this.previewBudget,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;

  // Optional preview payload (supplied by S005 in future iterations).
  final String? previewTitle;
  final String? previewDescription;
  final String? previewCategoryName;
  final String? previewLocation;
  final String? previewBudget;

  @override
  State<PublicPreviewScreen> createState() => _PublicPreviewScreenState();
}

class _PublicPreviewScreenState extends State<PublicPreviewScreen> {
  bool _acknowledged = false;

  String _t(String key) => fgText(widget.localeCode, key);

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      appBar: AppBar(
        title: Text(_t('screenS006')),
        actions: [
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
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(FgTokens.space4),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  FgStatusBadge(
                    label: _t('statePreview'),
                    kind: FgStatusKind.info,
                  ),
                  const SizedBox(height: FgTokens.space3),

                  FgStatePanel(
                    label: _t('statePreview'),
                    message: _t('previewNotice'),
                    kind: FgStatusKind.info,
                  ),
                  const SizedBox(height: FgTokens.space4),

                  _publicPostCard(theme),
                  const SizedBox(height: FgTokens.space4),

                  FgStatePanel(
                    label: _t('screenS006'),
                    message: _t('publicHelp'),
                    kind: FgStatusKind.neutral,
                  ),
                  const SizedBox(height: FgTokens.space4),

                  CheckboxListTile(
                    value: _acknowledged,
                    onChanged: (v) =>
                        setState(() => _acknowledged = v ?? false),
                    title: Text(_t('consent')),
                    controlAffinity: ListTileControlAffinity.leading,
                    contentPadding: EdgeInsets.zero,
                  ),
                  const SizedBox(height: FgTokens.space4),

                  FgStatePanel(
                    label: _t('statePreview'),
                    message: _t('publicLinkPending'),
                    kind: FgStatusKind.neutral,
                  ),
                  const SizedBox(height: FgTokens.space4),

                  FgButton(
                    label: _t('continue'),
                    onPressed:
                        _acknowledged ? () => _onContinue(context) : null,
                  ),
                  const SizedBox(height: FgTokens.space3),
                  FgButton(
                    label: _t('back'),
                    variant: FgButtonVariant.secondary,
                    onPressed: () => Navigator.of(context).maybePop(),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _publicPostCard(ThemeData theme) {
    final title = widget.previewTitle ?? _t('sampleNeed');
    final description =
        widget.previewDescription ?? _t('sampleDescription');
    final category = widget.previewCategoryName ?? _t('choose');
    final location = widget.previewLocation ?? _t('sampleLocation');
    final budget = widget.previewBudget ?? _t('unknown');

    return Container(
      padding: const EdgeInsets.all(FgTokens.space4),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: theme.dividerColor),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: theme.textTheme.titleLarge,
          ),
          const SizedBox(height: FgTokens.space2),
          Text(
            description,
            style: theme.textTheme.bodyMedium,
          ),
          const SizedBox(height: FgTokens.space3),
          _metaRow(theme, _t('fieldCategoryId'), category),
          _metaRow(theme, _t('fieldLocationText'), location),
          _metaRow(theme, _t('budget'), budget),
          const SizedBox(height: FgTokens.space3),
          Text(
            _t('publicLinkPending'),
            style: theme.textTheme.bodySmall,
          ),
        ],
      ),
    );
  }

  Widget _metaRow(ThemeData theme, String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: FgTokens.space2),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 140,
            child: Text(
              label,
              style: theme.textTheme.bodySmall,
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: theme.textTheme.bodyMedium,
            ),
          ),
        ],
      ),
    );
  }

  void _onContinue(BuildContext context) {
    // Preview only — the real submission remains in S005 (save action).
    // Pop back and signal with a local snackbar so the demo is honest.
    Navigator.of(context).maybePop();
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(_t('simulation'))),
    );
  }
}
