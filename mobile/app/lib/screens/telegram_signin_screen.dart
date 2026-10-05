import 'dart:async';

import 'package:flutter/material.dart';
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
  _AuthState _state = _AuthState.ready;
  String? _error;
  final _handoffCtrl = TextEditingController();

  String _t(String key) => fgText(widget.localeCode, key);

  @override
  void initState() {
    super.initState();
    // Web: capture handoff_code from current URL if present
    WidgetsBinding.instance.addPostFrameCallback((_) => _captureFromUri());
  }

  @override
  void dispose() {
    _handoffCtrl.dispose();
    super.dispose();
  }

  void _captureFromUri() {
    final uri = Uri.base;
    final code = uri.queryParameters['handoff_code'];
    if (code != null && code.isNotEmpty) {
      _handoffCtrl.text = code;
      _exchange(code);
    }
  }

  Future<void> _startSignIn() async {
    setState(() {
      _state = _AuthState.opening;
      _error = null;
    });

    try {
      final scope = AppScope.of(context);
      final start = await scope.authApi.startTelegram(
        returnUri: Uri.base.toString(),
        scope: 'openid profile',
      );

      final uri = Uri.parse(start.authUrl);
      final ok = await launchUrl(uri, mode: LaunchMode.externalApplication);
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
                      onPressed: () => _exchange(_handoffCtrl.text.trim()),
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
