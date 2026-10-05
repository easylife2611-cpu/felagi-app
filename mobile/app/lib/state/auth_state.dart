import 'package:flutter/foundation.dart';

import '../api/auth_api.dart';
import '../api/models/user.dart';
import '../api/token_store.dart';

/// Global auth state — single source of truth for the current user.
///
/// Lifecycle:
///   1. `bootstrap()` — reads token from secure storage, calls `/auth/me`
///   2. `signIn(AuthResult)` — stores token + user
///   3. `signOut()` — calls `/auth/logout`, clears token
class AuthState extends ChangeNotifier {
  AuthState({
    required this.api,
    required this.tokenStore,
  });

  final AuthApi api;
  final TokenStore tokenStore;

  User? _user;
  bool _ready = false;
  bool _busy = false;
  String? _error;

  User? get user => _user;
  bool get isAuthenticated => _user != null;
  bool get isReady => _ready;
  bool get isBusy => _busy;
  String? get error => _error;

  /// Read persisted token + fetch `/auth/me`.
  Future<void> bootstrap() async {
    _busy = true;
    _error = null;
    notifyListeners();

    try {
      final token = await tokenStore.readToken();
      if (token == null || token.isEmpty) {
        _ready = true;
        _busy = false;
        notifyListeners();
        return;
      }
      final me = await api.me();
      _user = me;
    } catch (e) {
      // Token stale or invalid — clear it (server also clears on 401)
      await tokenStore.clear();
      _user = null;
      _error = e.toString();
    } finally {
      _ready = true;
      _busy = false;
      notifyListeners();
    }
  }

  /// Called after exchange completes.
  Future<void> signIn({
    required String accessToken,
    required User user,
  }) async {
    await tokenStore.save(token: accessToken, userId: user.id);
    _user = user;
    _error = null;
    notifyListeners();
  }

  /// Sign out — best-effort server revoke, always clear local.
  Future<void> signOut() async {
    _busy = true;
    notifyListeners();

    try {
      await api.logout();
    } catch (_) {
      // Ignore — local clear is authoritative
    } finally {
      await tokenStore.clear();
      _user = null;
      _busy = false;
      notifyListeners();
    }
  }

  /// Force clear (e.g. on 401 from ApiClient).
  Future<void> forceSignOut() async {
    await tokenStore.clear();
    _user = null;
    notifyListeners();
  }
}
