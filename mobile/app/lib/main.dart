import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

import 'api/api_client.dart';
import 'api/auth_api.dart';
import 'api/needs_api.dart';
import 'api/boost_api.dart';
import 'api/offers_api.dart';
import 'api/config_api.dart';
import 'api/token_store.dart';
import 'app_scope.dart';
import 'preview_mode.dart';
import 'router/app_router.dart';
import 'state/auth_state.dart';
import 'state/app_config_state.dart';

void main() {
  PreviewMode.detectFromUrl();
  runApp(const FelagiApp());
}

/// Felagi — demand-first marketplace mobile app.
class FelagiApp extends StatefulWidget {
  const FelagiApp({super.key});

  @override
  State<FelagiApp> createState() => _FelagiAppState();
}

class _FelagiAppState extends State<FelagiApp> {
  late final ApiClient _client;
  late final AuthApi _authApi;
  late final NeedsApi _needsApi;
  late final BoostApi _boostApi;
  late final OffersApi _offersApi;
  late final ConfigApi _configApi;
  late final AppConfigState _appConfig;
  late final AuthState _authState;

  /// Router is created ONCE and kept stable across rebuilds.
  /// Auth changes are handled via `refreshListenable` inside the router.
  /// Only a user-initiated locale change recreates it.
  late GoRouter _router;

  String _localeCode = 'am';
  final ThemeMode _themeMode = ThemeMode.light;

  @override
  void initState() {
    super.initState();
    _client = ApiClient(tokenStore: TokenStore());
    _authApi = AuthApi(_client);
    _needsApi = NeedsApi(_client);
    _boostApi = BoostApi(_client);
    _offersApi = OffersApi(_client);
    _configApi = ConfigApi(_client);
    _appConfig = AppConfigState(api: _configApi);
    _authState = AuthState(api: _authApi, tokenStore: _client.tokenStore);

    // ── Create the router ONCE ──
    // Rationale:
    //   • GoRouter keeps its own navigation state (history + current loc).
    //   • Recreating it on every rebuild resets location → screens "flash".
    //   • Auth changes must NOT recreate the router; instead we wire
    //     `refreshListenable` so GoRouter re-evaluates redirects in place.
    _router = _createRouter();

    // Bootstrap: read token + /auth/me (async — UI shows loading).
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _authState.bootstrap();
      _appConfig.load();
    });
  }

  GoRouter _createRouter() {
    return AppRouter(
      localeCode: _localeCode,
      onLocaleChange: _onLocaleChange,
      authState: _authState,
    ).build();
  }

  void _onLocaleChange(String code) {
    setState(() {
      _localeCode = code;
      // Locale is baked into every screen builder, so we must rebuild
      // the router. This is a user-initiated action and is rare.
      _router = _createRouter();
    });
  }

  @override
  void dispose() {
    _authState.dispose();
    _appConfig.dispose();
    _client.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AppScope(
      client: _client,
      authApi: _authApi,
      needsApi: _needsApi,
      boostApi: _boostApi,
      offersApi: _offersApi,
      configApi: _configApi,
      appConfig: _appConfig,
      authState: _authState,
      // NOTE: MaterialApp.router is now built directly (no AnimatedBuilder).
      // The router listens to AuthState itself via refreshListenable.
      child: MaterialApp.router(
        debugShowCheckedModeBanner: false,
        title: fgText(_localeCode, 'brand'),
        theme: FgTheme.light(),
        darkTheme: FgTheme.dark(),
        themeMode: _themeMode,
        locale: Locale(_localeCode),
        supportedLocales: const [Locale('am'), Locale('en')],
        localizationsDelegates: GlobalMaterialLocalizations.delegates,
        routerConfig: _router,
      ),
    );
  }
}
