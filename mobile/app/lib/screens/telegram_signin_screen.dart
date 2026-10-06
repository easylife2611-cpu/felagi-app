import 'dart:async';
import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:url_launcher/url_launcher.dart';

import '../api/api_exception.dart';
import '../api/models/auth_result.dart';
import '../app_scope.dart';

/// S002 — Telegram sign-in (OIDC authorization-code + PKCE).
///
/// Flow:
///   1. POST /auth/telegram/start → {auth_url, attempt_id, expires_at}
///   2. url_launcher → system browser opens auth_url
///   3. User authorizes → backend captures callback → redirects with handoff_code
///   4. App captures handoff_code (web: Uri.base, mobile: manual paste for now)
///   5. POST /auth/telegram/exchange {handoff_code} → {access_token, user}
///   6. AuthState.signIn → router redirects to /browse
class TelegramSignInScreen extends StatefulWidget {
  const TelegramSignInScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;

  @override
  State<TelegramSignInScreen> createState() => _TelegramSignInScreenState();
}

enum _AuthState { ready, opening, awaiting, exchanging, denied, expired }

class _TelegramSignInScreenState extends State<TelegramSignInScreen> {
  /// Static set survives screen rebuilds (router is rebuilt on auth state change).
  /// Prevents double-exchange of the same handoff_code → 'already used' error.
  static final Set<String> _globallyExchangedCodes = {};
  _AuthState _state = _AuthState.ready;
  String? _error;
  final _handoffCtrl = TextEditingController();
  String? _widgetState;
  Timer? _readyTimer;

  String _t(String key) => fgText(widget.localeCode, key);

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _captureWhenReady();
    });
  }

  /// Wait for authState bootstrap via listener (non-blocking).
  ///
  /// Web-only: URL-based capture (handoff_code/tgAuthResult in URI).
  /// Mobile: manual paste fallback — no URI capture needed.
  void _captureWhenReady() {
    if (!mounted) return;

    // Mobile/tests: no URL capture — skip the timer entirely.
    if (!kIsWeb) {
      return;
    }

    try {
      final authState = AppScope.of(context).authState;
      if (authState.isReady) {
        _captureFromUri();
        return;
      }
      void onReady() {
        if (authState.isReady && mounted) {
          authState.removeListener(onReady);
          _readyTimer?.cancel();
          _captureFromUri();
        }
      }
      authState.addListener(onReady);
      // Safety fallback — Timer (cancellable in dispose)
      _readyTimer = Timer(const Duration(seconds: 2), () {
        authState.removeListener(onReady);
        if (mounted && !authState.isReady) {
          _captureFromUri();
        }
      });
    } catch (_) {
      // AppScope unavailable — try immediately
      _captureFromUri();
    }
  }

  @override
  void dispose() {
    _handoffCtrl.dispose();
    super.dispose();
  }

  void _captureFromUri() {
    final uri = Uri.base;
    final fullUrl = uri.toString();

    // 1. Telegram Widget result — fragment OR full URL contains tgAuthResult
    if (fullUrl.contains('tgAuthResult=')) {
      final idx = fullUrl.indexOf('tgAuthResult=');
      final fragment = fullUrl.substring(idx);
      _handleWidgetResult(fragment);
      return;
    }

    // 2. Back from widget callback: query contains handoff_code=X
    final code = uri.queryParameters['handoff_code'];
    if (code != null && code.isNotEmpty) {
      // authState is now bootstrapped (waited in initState) — safe to check.
      try {
        final authState = AppScope.of(context).authState;
        if (authState.isAuthenticated) {
          // Already logged in — nothing to do; URL will be cleaned on next nav.
          return;
        }
      } catch (_) {}
      // Skip if already exchanged in this VM
      if (_globallyExchangedCodes.contains(code)) {
        return;
      }
      _globallyExchangedCodes.add(code);
      _handoffCtrl.text = code;
      _exchange(code);
      return;
    }

    // 3. Widget state carried through the flow
    final ws = uri.queryParameters['widget_state'];
    if (ws != null && ws.isNotEmpty) {
      _widgetState = ws;
    }
  }

  /// Decode Telegram Widget fragment (#tgAuthResult=`<base64url-json>`)
  /// and navigate to backend widget callback with query params.
  Future<void> _handleWidgetResult(String fragment) async {
    try {
      final idx = fragment.indexOf('tgAuthResult=');
      final b64 = fragment
          .substring(idx + 'tgAuthResult='.length)
          .split('&')
          .first;
      final normalized = base64Url.normalize(b64);
      final decoded = utf8.decode(base64Url.decode(normalized));
      final payload = jsonDecode(decoded) as Map<String, dynamic>;

      final state = _widgetState ?? Uri.base.queryParameters['widget_state'];
      if (state == null || state.isEmpty) {
        setState(() {
          _error = 'Missing widget state';
          _state = _AuthState.ready;
        });
        return;
      }

      final callbackUrl = Uri.parse(
        'https://zagcreativity.com/api/v1/auth/telegram/widget/callback',
      ).replace(queryParameters: {
        'state': state,
        'id': payload['id'].toString(),
        'first_name': (payload['first_name'] ?? '').toString(),
        'last_name': (payload['last_name'] ?? '').toString(),
        'username': (payload['username'] ?? '').toString(),
        'photo_url': (payload['photo_url'] ?? '').toString(),
        'auth_date': (payload['auth_date'] ?? '').toString(),
        'hash': (payload['hash'] ?? '').toString(),
      });

      // Same-tab navigation so backend 302 returns to /test/?handoff_code=X
      await launchUrl(
        callbackUrl,
        mode: LaunchMode.platformDefault,
        webOnlyWindowName: '_self',
      );
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = 'Widget parse error: $e';
        _state = _AuthState.ready;
      });
    }
  }

  Future<void> _startSignIn() async {
    setState(() {
      _state = _AuthState.opening;
      _error = null;
    });

    try {
      final scope = AppScope.of(context);
      // Login Widget flow (BotFather "Web Login" currently Widget mode).
      // 1. Get callback_url + state from backend
      // 2. Navigate to oauth.telegram.org/auth with return_to=<frontend?widget_state=X>
      // 3. Telegram redirects back with #tgAuthResult=<base64>
      // 4. _captureFromUri decodes it, navigates to backend callback
      // 5. Backend redirects to /test/?handoff_code=X
      // 6. _captureFromUri exchanges handoff_code for token
      final baseReturnUri = kIsWeb
          ? Uri.base.toString().split('#').first.split('?').first
          : scope.appConfig.telegramReturnUri;

      final start = await scope.authApi.startTelegramWidget(
        returnUri: baseReturnUri,
      );

      final callbackUrl = start['callback_url'] as String;
      final cbUri = Uri.parse(callbackUrl);
      final state = cbUri.queryParameters['state'];
      _widgetState = state;

      // Build frontend return URL with widget_state carried forward
      final frontendReturn = '$baseReturnUri?widget_state=$state';

      // Build Telegram Widget redirect URL
      // bot_id = numeric bot id from .env TELEGRAM_CLIENT_ID
      final telegramUrl = Uri.https('oauth.telegram.org', '/auth', {
        'bot_id': '8629327448',
        'origin': 'zagcreativity.com',
        'return_to': frontendReturn,
      });

      final ok = await launchUrl(
        telegramUrl,
        mode: kIsWeb ? LaunchMode.platformDefault : LaunchMode.externalApplication,
        webOnlyWindowName: '_self',
      );
      if (!mounted) return;

      if (!ok) {
        setState(() {
          _state = _AuthState.ready;
          _error = 'Failed to open browser';
        });
        return;
      }

      setState(() => _state = _AuthState.awaiting);
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _state = _AuthState.ready;
        _error = e.message;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _state = _AuthState.ready;
        _error = '$e';
      });
    }
  }

  Future<void> _exchange(String code) async {
    if (code.isEmpty) return;

    // If already authenticated (e.g. from a prior exchange in a previous VM),
    // skip the network call — the token is already stored.
    final authState = AppScope.of(context).authState;
    if (authState.isAuthenticated) {
      return;
    }

    setState(() {
      _state = _AuthState.exchanging;
      _error = null;
    });

    try {
      final scope = AppScope.of(context);
      final AuthResult result = await scope.authApi.exchangeHandoff(
        handoffCode: code,
        deviceName: 'mobile-app',
      );
      await scope.authState.signIn(
        accessToken: result.accessToken,
        user: result.user,
      );
      // Router will redirect — no manual navigation needed here.
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _state = _AuthState.awaiting;
        _error = e.message;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _state = _AuthState.awaiting;
        _error = '$e';
      });
    }
  }

  String _stateLabel() => switch (_state) {
        _AuthState.ready => _t('stateReady'),
        _AuthState.opening => _t('stateLoading'),
        _AuthState.awaiting => _t('statePending'),
        _AuthState.exchanging => _t('stateLoading'),
        _AuthState.denied => _t('stateDenied'),
        _AuthState.expired => _t('stateExpired'),
      };

  FgStatusKind _stateKind() => switch (_state) {
        _AuthState.ready => FgStatusKind.neutral,
        _AuthState.opening ||
        _AuthState.awaiting ||
        _AuthState.exchanging =>
          FgStatusKind.info,
        _AuthState.denied || _AuthState.expired => FgStatusKind.warning,
      };

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      appBar: AppBar(
        title: Text(_t('screenS002')),
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
            constraints: const BoxConstraints(maxWidth: 480),
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(FgTokens.space4),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  FgStatusBadge(label: _stateLabel(), kind: _stateKind()),
                  const SizedBox(height: FgTokens.space4),
                  Text(_t('authHelp'), style: theme.textTheme.bodyLarge),
                  const SizedBox(height: FgTokens.space6),

                  // Primary: start sign-in
                  FgButton(
                    label: _t('signIn'),
                    isLoading: _state == _AuthState.opening ||
                        _state == _AuthState.exchanging,
                    onPressed: _state == _AuthState.opening ||
                            _state == _AuthState.exchanging
                        ? null
                        : _startSignIn,
                  ),
                  const SizedBox(height: FgTokens.space3),

                  // Secondary: cancel
                  FgButton(
                    label: _t('cancel'),
                    variant: FgButtonVariant.secondary,
                    onPressed: () => Navigator.of(context).maybePop(),
                  ),

                  // Error
                  if (_error != null) ...[
                    const SizedBox(height: FgTokens.space4),
                    FgStatePanel(
                      label: _t('stateError'),
                      message: _error!,
                      kind: FgStatusKind.error,
                    ),
                  ],

                  // Manual handoff paste (mobile fallback until deep-link wired)
                  if (_state == _AuthState.awaiting ||
                      _state == _AuthState.exchanging) ...[
                    const SizedBox(height: FgTokens.space6),
                    Text(
                      'Paste handoff_code (mobile)',
                      style: theme.textTheme.labelMedium,
                    ),
                    const SizedBox(height: FgTokens.space2),
                    FgTextField(
                      label: 'handoff_code',
                      controller: _handoffCtrl,
                    ),
                    const SizedBox(height: FgTokens.space3),
                    FgButton(
                      label: _t('confirm'),
                      variant: FgButtonVariant.secondary,
                      onPressed: () {
                        final c = _handoffCtrl.text.trim();
                        if (c.isNotEmpty && !_globallyExchangedCodes.contains(c)) {
                          _globallyExchangedCodes.add(c);
                          _exchange(c);
                        }
                      },
                    ),
                  ],
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
