import 'package:flutter/foundation.dart';

import '../api/config_api.dart';

/// App-level runtime config, fetched once at startup.
///
/// Provides a safe fallback so screens never crash if the config
/// endpoint is unavailable.
class AppConfigState extends ChangeNotifier {
  AppConfigState({required this.api});

  final ConfigApi api;

  Map<String, dynamic>? _config;
  bool _loaded = false;

  /// Fallback used before config loads (or if it fails).
  static const String _fallbackReturnUri =
      'https://zagcreativity.com/auth/mobile-handoff';

  bool get loaded => _loaded;

  /// Telegram OIDC return_uri (falls back to default).
  String get telegramReturnUri =>
      (_config?['telegram_return_uri'] as String?) ?? _fallbackReturnUri;

  /// App version reported by backend.
  String get appVersion =>
      (_config?['app_version'] as String?) ?? '1.4.3';

  /// Feature flags.
  bool featureEnabled(String name) {
    final features = _config?['features'];
    if (features is Map && features[name] is bool) {
      return features[name] as bool;
    }
    return true;
  }

  /// Fetch config from backend. Safe to call multiple times.
  Future<void> load() async {
    try {
      _config = await api.fetch();
      _loaded = true;
      notifyListeners();
    } catch (_) {
      // Silent failure — fallbacks are used.
      _loaded = false;
    }
  }
}
