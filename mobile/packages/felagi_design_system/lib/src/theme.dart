/* GENERATED: edit template or token source. */
import 'package:flutter/material.dart';
import 'tokens.dart';

abstract final class FgTheme {
  static ThemeData light() => _build(Brightness.light);
  static ThemeData dark() => _build(Brightness.dark);
  static ThemeData _build(Brightness brightness) {
    final dark = brightness == Brightness.dark;
    final primary = dark ? FgTokens.darkNavy : FgTokens.navy;
    final accent = dark ? FgTokens.darkOrange : FgTokens.orange;
    final surface = dark ? FgTokens.darkSurface : FgTokens.surface;
    final foreground = dark ? FgTokens.darkTextPrimary : FgTokens.textPrimary;
    final muted = dark ? FgTokens.darkTextSecondary : FgTokens.textSecondary;
    final border = dark ? FgTokens.darkBorder : FgTokens.border;
    final error = dark ? FgTokens.darkError : FgTokens.error;
    final scheme = ColorScheme(
      brightness: brightness, primary: primary,
      onPrimary: dark ? FgTokens.textPrimary : FgTokens.onNavy,
      secondary: accent, onSecondary: FgTokens.onOrange,
      error: error, onError: dark ? FgTokens.textPrimary : FgTokens.onNavy,
      surface: surface, onSurface: foreground,
      outline: border, onSurfaceVariant: muted,
      primaryContainer: surface, onPrimaryContainer: primary,
      secondaryContainer: surface, onSecondaryContainer: foreground,
      tertiary: primary, onTertiary: dark ? FgTokens.textPrimary : FgTokens.onNavy,
      tertiaryContainer: surface, onTertiaryContainer: foreground,
      errorContainer: surface, onErrorContainer: error,
    );
    final text = TextTheme(
      displayMedium: TextStyle(fontSize: 32, height: 40/32, fontWeight: FontWeight.w700, color: foreground),
      titleLarge: TextStyle(fontSize: 24, height: 32/24, fontWeight: FontWeight.w700, color: foreground),
      titleMedium: TextStyle(fontSize: 18, height: 26/18, fontWeight: FontWeight.w600, color: foreground),
      bodyLarge: TextStyle(fontSize: 16, height: 24/16, color: foreground),
      bodyMedium: TextStyle(fontSize: 14, height: 20/14, color: muted),
      labelMedium: TextStyle(fontSize: 12, height: 16/12, fontWeight: FontWeight.w500, color: muted),
    );
    return ThemeData(
      useMaterial3: true, brightness: brightness, colorScheme: scheme,
      fontFamily: FgTokens.fontFamily, fontFamilyFallback: const ['sans-serif'],
      scaffoldBackgroundColor: dark ? FgTokens.darkCanvas : FgTokens.canvas,
      textTheme: text, focusColor: primary, dividerColor: border,
      materialTapTargetSize: MaterialTapTargetSize.padded,
      inputDecorationTheme: InputDecorationTheme(
        filled: true, fillColor: surface,
        constraints: const BoxConstraints(minHeight: FgTokens.inputMinimumHeight),
        contentPadding: const EdgeInsets.all(FgTokens.space4),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(FgTokens.radiusControl), borderSide: BorderSide(color: border)),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(FgTokens.radiusControl), borderSide: BorderSide(color: border)),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(FgTokens.radiusControl), borderSide: BorderSide(color: primary, width: FgTokens.progressStroke)),
        errorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(FgTokens.radiusControl), borderSide: BorderSide(color: error, width: FgTokens.progressStroke)),
      ),
    );
  }
}
