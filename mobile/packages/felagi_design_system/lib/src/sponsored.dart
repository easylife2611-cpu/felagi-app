import 'package:flutter/material.dart';
import 'components.dart';
import 'tokens.dart';

enum FgSponsoredVariant { card, banner, compact }

/// Text-only canonical composition. Supply authorized/localized content only.
/// Serving, approved media, external navigation and impression measurement are
/// adapters, not inferred by this widget. A rendered widget is not an impression.
class FgSponsoredCard extends StatelessWidget {
  const FgSponsoredCard({required this.sponsoredLabel, required this.sponsor,
    required this.title, required this.body, required this.ctaLabel,
    required this.disclosureLabel, required this.disclosure,
    required this.onActivate, this.variant = FgSponsoredVariant.card, super.key});
  final String sponsoredLabel, sponsor, title, body, ctaLabel;
  final String disclosureLabel, disclosure;
  final VoidCallback? onActivate;
  final FgSponsoredVariant variant;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Semantics(container: true, label: '$sponsoredLabel — $sponsor',
      child: Container(
        padding: const EdgeInsets.all(FgTokens.space4),
        decoration: BoxDecoration(color: theme.brightness == Brightness.dark ? FgTokens.sponsoredSurfaceDark : FgTokens.sponsoredSurface,
          border: Border.all(color: theme.colorScheme.outline),
          borderRadius: BorderRadius.circular(FgTokens.radiusCard)),
        child: Column(mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(sponsoredLabel, style: theme.textTheme.labelLarge),
            Text(sponsor, style: theme.textTheme.bodySmall),
            const SizedBox(height: FgTokens.space3),
            Text(title, style: theme.textTheme.titleMedium, softWrap: true),
            Text(body, softWrap: true),
            const SizedBox(height: FgTokens.space3),
            FgButton(label: ctaLabel, onPressed: onActivate,
              variant: FgButtonVariant.secondary),
            ExpansionTile(title: Text(disclosureLabel),
              children: [Text(disclosure, softWrap: true)]),
          ]),
      ));
  }
}
