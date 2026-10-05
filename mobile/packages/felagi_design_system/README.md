# Felagi aligned Flutter starter 1.4.1

Scope: shared component starter, explicit light/dark theme, complete token/screen registry and ARB localization. It is not the complete45-screen application. Product-specific screens are specified in the final screen contracts. No starter class overrides the canonical spec. Legacy public components retained where possible; FgSearchField now requires clearLabel, FgLoadingSkeleton requires localized label, and confirmation supports destructive.

From this folder run `flutter pub get`, `flutter analyze`, `flutter test`. From `example`, run `flutter pub get` and `flutter run -d <device>` after creating the desired platform runner with `flutter create --platforms=android,ios,web .`. Runner generation is standard deployment scaffolding, not an unresolved product design. SDK>=Flutter3.22/Dart3.4. The provided example inspects canonical screen names/routes and shared components; it does not implement backend screens or permissions.

Amharic/English ARB copies are generated from root Localization. Strings are passed to components by the app using fgText or Flutter gen-l10n. Font family is Noto Sans Ethiopic; approved system fallback remains; device glyph evidence is required. No font binary or external account is necessary for viewing HTML. Production must validate licensed font/fallback.

Flutter SDK is unavailable in the authoring environment; compile/analyze/widget execution is REQUIRES EXTERNAL EVIDENCE. Source-level token/state/string alignment is checked by package tools; it is not compilation evidence.
