import 'package:flutter/widgets.dart';

import 'api/api_client.dart';
import 'api/auth_api.dart';
import 'api/needs_api.dart';
import 'api/boost_api.dart';
import 'api/offers_api.dart';
import 'state/auth_state.dart';

/// Lightweight DI container — no external package.
class AppScope extends InheritedWidget {
  const AppScope({
    super.key,
    required this.client,
    required this.authApi,
    required this.needsApi,
    required this.boostApi,
    required this.offersApi,
    required this.authState,
    required super.child,
  });

  final ApiClient client;
  final AuthApi authApi;
  final NeedsApi needsApi;
  final BoostApi boostApi;
  final OffersApi offersApi;
  final AuthState authState;

  /// Fetch nearest scope.
  static AppScope of(BuildContext context) {
    final scope = context.dependOnInheritedWidgetOfExactType<AppScope>();
    assert(scope != null, 'AppScope not found in widget tree');
    return scope!;
  }

  /// Convenience: listen to AuthState changes.
  static AuthState auth(BuildContext context) => of(context).authState;

  @override
  bool updateShouldNotify(AppScope oldWidget) =>
      authState != oldWidget.authState ||
      authApi != oldWidget.authApi ||
      needsApi != oldWidget.needsApi ||
      boostApi != oldWidget.boostApi ||
      offersApi != oldWidget.offersApi ||
      client != oldWidget.client;
}
