import 'package:flutter/material.dart';
import 'tokens.dart';

enum FgButtonVariant { primary, secondary, text, destructive }
enum FgStatusKind { neutral, info, success, warning, error }
enum FgTelegramState { queued, sending, posted, retry, uncertain, failed, skipped, removalPending, removed, paused }

class FgButton extends StatelessWidget {
  const FgButton({required this.label, required this.onPressed, this.variant = FgButtonVariant.primary, this.isLoading = false, this.isEnabled = true, this.icon, super.key});
  final String label;
  final VoidCallback? onPressed;
  final FgButtonVariant variant;
  final bool isLoading, isEnabled;
  final IconData? icon;
  @override
  Widget build(BuildContext context) {
    final cs = Theme.of(context).colorScheme;
    final enabled = isEnabled && !isLoading && onPressed != null;
    final primary = variant == FgButtonVariant.primary;
    final destructive = variant == FgButtonVariant.destructive;
    final foreground = primary ? cs.onSecondary : destructive ? cs.onError : cs.primary;
    final background = primary ? cs.secondary : destructive ? cs.error : cs.surface;
    final minHeight = primary ? FgTokens.controlProminent : FgTokens.controlStandard;
    return Semantics(button: true, enabled: enabled, liveRegion: isLoading, child: ConstrainedBox(
      constraints: BoxConstraints(minHeight: minHeight, minWidth: FgTokens.minTarget),
      child: OutlinedButton(
        onPressed: enabled ? onPressed : null,
        style: OutlinedButton.styleFrom(
          backgroundColor: background, foregroundColor: foreground,
          minimumSize: Size(FgTokens.minTarget, minHeight),
          side: BorderSide(color: primary ? cs.primary : destructive ? cs.error : cs.outline, width: FgTokens.progressStroke),
          padding: const EdgeInsets.all(FgTokens.space3),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(FgTokens.radiusControl)),
        ),
        child: Row(mainAxisSize: MainAxisSize.min, children: [
          if (isLoading) ...[SizedBox(width: FgTokens.iconMedium, height: FgTokens.iconMedium, child: CircularProgressIndicator(strokeWidth: FgTokens.progressStroke, color: foreground)), const SizedBox(width: FgTokens.space2)]
          else if (icon != null) ...[Icon(icon, size: FgTokens.iconMedium), const SizedBox(width: FgTokens.space2)],
          Flexible(child: Text(label, softWrap: true)),
        ]),
      ),
    ));
  }
}

class FgStatusBadge extends StatelessWidget {
  const FgStatusBadge({required this.label, required this.kind, this.icon, super.key});
  final String label;
  final FgStatusKind kind;
  final IconData? icon;
  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final color = switch (kind) {
      FgStatusKind.neutral => dark ? FgTokens.darkTextSecondary : FgTokens.textSecondary,
      FgStatusKind.info => dark ? FgTokens.darkInfo : FgTokens.info,
      FgStatusKind.success => dark ? FgTokens.darkSuccess : FgTokens.success,
      FgStatusKind.warning => dark ? FgTokens.darkWarning : FgTokens.warning,
      FgStatusKind.error => dark ? FgTokens.darkError : FgTokens.error,
    };
    return Semantics(container: true, child: Container(
      padding: const EdgeInsets.symmetric(horizontal: FgTokens.space3, vertical: FgTokens.space2),
      decoration: BoxDecoration(color: Theme.of(context).colorScheme.surface, borderRadius: BorderRadius.circular(FgTokens.radiusPill)),
      child: Wrap(spacing: FgTokens.space2, crossAxisAlignment: WrapCrossAlignment.center, children: [Icon(icon ?? Icons.circle, size: FgTokens.skeletonLine, color: color), const SizedBox(width: FgTokens.space2), Text(label, style: TextStyle(color: color, fontWeight: FontWeight.w600))]),
    ));
  }
}

class FgCategoryChip extends StatelessWidget {
  const FgCategoryChip({required this.label, required this.selected, required this.onSelected, super.key});
  final String label;
  final bool selected;
  final ValueChanged<bool> onSelected;
  @override
  Widget build(BuildContext context) => Semantics(button: true, selected: selected, child: FilterChip(label: Text(label), selected: selected, onSelected: onSelected, showCheckmark: true, materialTapTargetSize: MaterialTapTargetSize.padded));
}

class FgSectionHeader extends StatelessWidget {
  const FgSectionHeader({required this.title, this.actionLabel, this.onAction, super.key});
  final String title;
  final String? actionLabel;
  final VoidCallback? onAction;
  @override
  Widget build(BuildContext context) => Row(children: [
    Expanded(child: Text(title, style: Theme.of(context).textTheme.titleMedium)),
    if (actionLabel != null && onAction != null) TextButton(onPressed: onAction, child: Text(actionLabel!)),
  ]);
}

class FgNeedCard extends StatelessWidget {
  const FgNeedCard({required this.title, required this.category, required this.location, required this.offersSummary, this.budget, this.isBoosted = false, this.boostLabel, this.onTap, super.key});
  final String title;
  final String category;
  final String location;
  final String offersSummary;
  final String? budget;
  final bool isBoosted;
  final String? boostLabel;
  final VoidCallback? onTap;
  @override
  Widget build(BuildContext context) => Card(
    color: Theme.of(context).colorScheme.surface,
    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(FgTokens.radiusCard), side: BorderSide(color: Theme.of(context).colorScheme.outline)),
    child: InkWell(onTap: onTap, borderRadius: BorderRadius.circular(FgTokens.radiusCard), child: Padding(
      padding: const EdgeInsets.all(FgTokens.space4),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(title, style: Theme.of(context).textTheme.titleMedium), if (isBoosted && boostLabel != null) FgStatusBadge(label: boostLabel!, kind: FgStatusKind.info, icon: Icons.bolt)]),
        const SizedBox(height: FgTokens.space2),
        Text(category + ' · ' + location, style: Theme.of(context).textTheme.bodyMedium),
        if (budget != null) ...[const SizedBox(height: FgTokens.space2), Text(budget!, style: Theme.of(context).textTheme.bodyLarge)],
        const SizedBox(height: FgTokens.space2),
        Text(offersSummary, style: Theme.of(context).textTheme.bodyMedium),
      ]),
    )),
  );
}

class FgEmptyState extends StatelessWidget {
  const FgEmptyState({required this.title, required this.message, this.actionLabel, this.onAction, super.key});
  final String title;
  final String message;
  final String? actionLabel;
  final VoidCallback? onAction;
  @override
  Widget build(BuildContext context) => Center(child: Padding(padding: const EdgeInsets.all(FgTokens.space6), child: Column(mainAxisSize: MainAxisSize.min, children: [
    Icon(Icons.inbox_outlined, size: FgTokens.iconHero, color: Theme.of(context).colorScheme.onSurfaceVariant),
    const SizedBox(height: FgTokens.space3),
    Text(title, textAlign: TextAlign.center, style: Theme.of(context).textTheme.titleMedium),
    const SizedBox(height: FgTokens.space2),
    Text(message, textAlign: TextAlign.center, style: Theme.of(context).textTheme.bodyMedium),
    if (actionLabel != null && onAction != null) ...[const SizedBox(height: FgTokens.space4), FgButton(label: actionLabel!, onPressed: onAction, variant: FgButtonVariant.secondary)],
  ])));
}

class FgOfflineBanner extends StatelessWidget {
  const FgOfflineBanner({required this.message, super.key});
  final String message;
  @override
  Widget build(BuildContext context) => Semantics(liveRegion: true, child: Material(color: Theme.of(context).colorScheme.surface, child: Padding(padding: const EdgeInsets.all(FgTokens.space3), child: Row(children: [
    Icon(Icons.cloud_off_outlined, color: Theme.of(context).brightness == Brightness.dark ? FgTokens.darkWarning : FgTokens.warning), const SizedBox(width: FgTokens.space2), Expanded(child: Text(message)),
  ]))));
}

class FgTelegramPreview extends StatelessWidget {
  const FgTelegramPreview({required this.title, required this.category, required this.coarseLocation, required this.publicLink, this.budget, required this.previewLabel, required this.exclusionDisclosure, required this.acknowledgementLabel, required this.acknowledged, required this.onAcknowledged, super.key});
  final String title;
  final String category;
  final String coarseLocation;
  final String publicLink;
  final String? budget;
  final String previewLabel;
  final String exclusionDisclosure;
  final String acknowledgementLabel;
  final bool acknowledged;
  final ValueChanged<bool> onAcknowledged;
  @override
  Widget build(BuildContext context) => Card(child: Padding(padding: const EdgeInsets.all(FgTokens.space4), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
    FgStatusBadge(label: previewLabel, kind: FgStatusKind.info, icon: Icons.public),
    const SizedBox(height: FgTokens.space3),
    Text(title, style: Theme.of(context).textTheme.titleMedium),
    Text(category + ' · ' + coarseLocation),
    if (budget != null) Text(budget!),
    SelectableText(publicLink),
    const SizedBox(height: FgTokens.space2),
    Text(exclusionDisclosure),
    CheckboxListTile(contentPadding: EdgeInsets.zero, value: acknowledged, onChanged: (value) => onAcknowledged(value ?? false), title: Text(acknowledgementLabel), controlAffinity: ListTileControlAffinity.leading),
  ])));
}

class FgTelegramDeliveryBadge extends StatelessWidget {
  const FgTelegramDeliveryBadge({required this.state, required this.label, super.key});
  final FgTelegramState state;
  final String label;
  @override
  Widget build(BuildContext context) {
    final kind = switch (state) {
      FgTelegramState.posted || FgTelegramState.removed => FgStatusKind.success,
      FgTelegramState.queued || FgTelegramState.sending => FgStatusKind.info,
      FgTelegramState.retry || FgTelegramState.removalPending || FgTelegramState.skipped || FgTelegramState.paused => FgStatusKind.warning,
      FgTelegramState.uncertain || FgTelegramState.failed => FgStatusKind.error,
    };
    return FgStatusBadge(label: label, kind: kind, icon: Icons.send_outlined);
  }
}

class FgIconButton extends StatelessWidget {
  const FgIconButton({required this.icon, required this.semanticLabel, required this.onPressed, this.tooltip, super.key});
  final IconData icon;
  final String semanticLabel;
  final String? tooltip;
  final VoidCallback? onPressed;

  @override
  Widget build(BuildContext context) => IconButton(
        onPressed: onPressed,
        tooltip: tooltip ?? semanticLabel,
        icon: Icon(icon),
        constraints: const BoxConstraints(minWidth: FgTokens.minTarget, minHeight: FgTokens.minTarget),
        style: IconButton.styleFrom(foregroundColor: Theme.of(context).colorScheme.primary),
      );
}


class FgTextField extends StatelessWidget {
  const FgTextField({required this.label, this.controller, this.onChanged, this.hint, this.helper, this.errorText, this.validator, this.keyboardType, this.maxLines = 1, this.enabled = true, this.readOnly = false, super.key});
  final String label;
  final TextEditingController? controller;
  final ValueChanged<String>? onChanged;
  final String? hint;
  final String? helper;
  final String? errorText;
  final FormFieldValidator<String>? validator;
  final TextInputType? keyboardType;
  final int maxLines;
  final bool enabled;
  final bool readOnly;

  @override
  Widget build(BuildContext context) => TextFormField(
        controller: controller,
        onChanged: onChanged,
        validator: validator,
        keyboardType: keyboardType,
        maxLines: maxLines,
        enabled: enabled,
        readOnly: readOnly,
        decoration: InputDecoration(labelText: label, hintText: hint, helperText: helper, errorText: errorText),
      );
}

class FgSearchField extends StatelessWidget {
  const FgSearchField({required this.controller, required this.label, required this.onChanged, required this.clearLabel, this.onClear, super.key});
  final TextEditingController controller;
  final String label;
  final ValueChanged<String> onChanged;
  final VoidCallback? onClear;
  final String clearLabel;

  @override
  Widget build(BuildContext context) => TextField(
        controller: controller,
        onChanged: onChanged,
        textInputAction: TextInputAction.search,
        decoration: InputDecoration(
          prefixIcon: const Icon(Icons.search),
          labelText: label,
          suffixIcon: controller.text.isEmpty
              ? null
              : IconButton(tooltip: clearLabel, onPressed: onClear, icon: const Icon(Icons.close)),
        ),
      );
}

class FgOfferSummaryCard extends StatelessWidget {
  const FgOfferSummaryCard({required this.providerName, required this.summary, required this.status, this.price, this.onTap, super.key});
  final String providerName;
  final String summary;
  final String status;
  final String? price;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) => Card(
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(FgTokens.radiusCard),
          child: Padding(
            padding: const EdgeInsets.all(FgTokens.space4),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(providerName, style: Theme.of(context).textTheme.titleMedium), FgStatusBadge(label: status, kind: FgStatusKind.neutral)]),
              const SizedBox(height: FgTokens.space2),
              Text(summary, style: Theme.of(context).textTheme.bodyMedium),
              if (price != null) ...[const SizedBox(height: FgTokens.space2), Text(price!, style: Theme.of(context).textTheme.titleMedium)],
            ]),
          ),
        ),
      );
}

class FgComparisonCriterionTile extends StatelessWidget {
  const FgComparisonCriterionTile({required this.label, required this.scoreLabel, required this.evidence, required this.isMissing, this.weightLabel, this.coverageLabel, this.explanation, this.missingInformation, this.riskText, super.key});
  final String label;
  final String scoreLabel;
  final String evidence;
  final bool isMissing;
  final String? weightLabel, coverageLabel, explanation, missingInformation, riskText;

  @override
  Widget build(BuildContext context) => Card(
        child: Padding(
          padding: const EdgeInsets.all(FgTokens.space4),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(label, style: Theme.of(context).textTheme.titleMedium), Text(scoreLabel, style: Theme.of(context).textTheme.titleMedium)]),
            const SizedBox(height: FgTokens.space2),
            if (weightLabel != null) Text(weightLabel!),
            if (coverageLabel != null) Text(coverageLabel!),
            Text(evidence, style: Theme.of(context).textTheme.bodyMedium),
            if (explanation != null) Text(explanation!),
            if (missingInformation != null) Text(missingInformation!),
            if (riskText != null) Text(riskText!),
            if (isMissing) ...[const SizedBox(height: FgTokens.space2), FgStatusBadge(label: evidence, kind: FgStatusKind.warning, icon: Icons.info_outline)],
          ]),
        ),
      );
}

class FgErrorState extends StatelessWidget {
  const FgErrorState({required this.title, required this.message, this.retryLabel, this.onRetry, super.key});
  final String title;
  final String message;
  final String? retryLabel;
  final VoidCallback? onRetry;

  @override
  Widget build(BuildContext context) => Center(
        child: Padding(
          padding: const EdgeInsets.all(FgTokens.space6),
          child: Column(mainAxisSize: MainAxisSize.min, children: [
            Icon(Icons.error_outline, color: Theme.of(context).colorScheme.error, size: FgTokens.iconHero),
            const SizedBox(height: FgTokens.space3),
            Text(title, textAlign: TextAlign.center, style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: FgTokens.space2),
            Text(message, textAlign: TextAlign.center),
            if (retryLabel != null && onRetry != null) ...[const SizedBox(height: FgTokens.space4), FgButton(label: retryLabel!, onPressed: onRetry, variant: FgButtonVariant.secondary)],
          ]),
        ),
      );
}

class FgLoadingSkeleton extends StatelessWidget {
  const FgLoadingSkeleton({required this.label, this.lines = 3, super.key});
  final String label;
  final int lines;

  @override
  Widget build(BuildContext context) {
    final reduceMotion = MediaQuery.of(context).disableAnimations;
    return Semantics(
      liveRegion: true,
      label: label,
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: List.generate(lines, (index) => Container(
        height: FgTokens.skeletonLine,
        width: index == lines - 1 ? FgTokens.skeletonShort : double.infinity,
        margin: const EdgeInsets.only(bottom: FgTokens.space3),
        decoration: BoxDecoration(color: (Theme.of(context).brightness == Brightness.dark ? FgTokens.darkRaised : FgTokens.canvas), borderRadius: BorderRadius.circular(FgTokens.radiusSmall)),
        child: reduceMotion ? null : const SizedBox.shrink(),
      ))),
    );
  }
}

class FgNavigationBar extends StatelessWidget {
  const FgNavigationBar({required this.selectedIndex, required this.onDestinationSelected, required this.labels, super.key}) : assert(labels.length == 5);
  final int selectedIndex;
  final ValueChanged<int> onDestinationSelected;
  final List<String> labels;

  @override
  Widget build(BuildContext context) => NavigationBar(
        selectedIndex: selectedIndex,
        onDestinationSelected: onDestinationSelected,
        destinations: [
          NavigationDestination(icon: const Icon(Icons.search), label: labels[0]),
          NavigationDestination(icon: const Icon(Icons.assignment_outlined), label: labels[1]),
          NavigationDestination(icon: const Icon(Icons.local_offer_outlined), label: labels[2]),
          NavigationDestination(icon: const Icon(Icons.notifications_outlined), label: labels[3]),
          NavigationDestination(icon: const Icon(Icons.person_outline), label: labels[4]),
        ],
      );
}

class FgConfirmationSheet extends StatelessWidget {
  const FgConfirmationSheet({required this.title, required this.message, required this.confirmLabel, required this.cancelLabel, required this.onConfirm, required this.onCancel, this.isLoading = false, this.destructive = false, super.key});
  final String title;
  final String message;
  final String confirmLabel;
  final String cancelLabel;
  final VoidCallback onConfirm;
  final VoidCallback onCancel;
  final bool isLoading;
  final bool destructive;

  @override
  Widget build(BuildContext context) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(FgTokens.space6),
          child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.stretch, children: [
            Text(title, style: Theme.of(context).textTheme.titleLarge),
            const SizedBox(height: FgTokens.space3),
            Text(message, style: Theme.of(context).textTheme.bodyLarge),
            const SizedBox(height: FgTokens.space6),
            FgButton(label: confirmLabel, onPressed: onConfirm, isLoading: isLoading, variant: destructive ? FgButtonVariant.destructive : FgButtonVariant.primary),
            TextButton(onPressed: isLoading ? null : onCancel, child: Text(cancelLabel)),
          ]),
        ),
      );
}

/// Native dialog focus scope; localized strings remain caller-owned.
Future<bool?> fgShowConfirmation({required BuildContext context, required String title, required String message, required String confirmLabel, required String cancelLabel, bool destructive = false}) => showDialog<bool>(
  context: context,
  builder: (dialogContext) => AlertDialog(
    title: Text(title),
    content: SingleChildScrollView(child: Text(message)),
    actions: [
      TextButton(autofocus: true, onPressed: () => Navigator.of(dialogContext).pop(false), child: Text(cancelLabel)),
      FgButton(label: confirmLabel, variant: destructive ? FgButtonVariant.destructive : FgButtonVariant.primary, onPressed: () => Navigator.of(dialogContext).pop(true)),
    ],
  ),
);

/// Shared semantic runtime/evidence panel. Label and message must be localized.
class FgStatePanel extends StatelessWidget {
  final String label;
  final String message;
  final FgStatusKind kind;
  final String? actionLabel;
  final VoidCallback? onAction;
  const FgStatePanel({required this.label, required this.message,
    this.kind = FgStatusKind.info, this.actionLabel, this.onAction, super.key});
  @override
  Widget build(BuildContext context) => Semantics(
    liveRegion: true,
    child: Card(child: Padding(
      padding: const EdgeInsets.all(FgTokens.space4),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        FgStatusBadge(label: label, kind: kind),
        const SizedBox(height: FgTokens.space3),
        Text(message, style: Theme.of(context).textTheme.bodyLarge),
        if (actionLabel != null && onAction != null) ...[
          const SizedBox(height: FgTokens.space4),
          FgButton(label: actionLabel!, onPressed: onAction),
        ],
      ]),
    )),
  );
}

// One canonical criterion component, compatibility name retained.
typedef FgComparisonCriterion = FgComparisonCriterionTile;
