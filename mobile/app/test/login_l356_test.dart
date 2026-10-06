import 'dart:async';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:felagi_app/api/api_client.dart';
import 'package:felagi_app/api/auth_api.dart';
import 'package:felagi_app/api/token_store.dart';
import 'package:felagi_app/api/needs_api.dart';
import 'package:felagi_app/api/boost_api.dart';
import 'package:felagi_app/api/offers_api.dart';
import 'package:felagi_app/api/config_api.dart';
import 'package:felagi_app/app_scope.dart';
import 'package:felagi_app/state/auth_state.dart';
import 'package:felagi_app/state/app_config_state.dart';
import 'package:felagi_app/router/app_router.dart';
import 'package:felagi_app/screens/telegram_signin_screen.dart';
import 'package:felagi_app/screens/browse_needs_screen.dart';
import 'package:felagi_app/screens/ai_comparison_screen.dart';
import 'package:felagi_app/screens/comparison_history_screen.dart';
import 'package:felagi_app/screens/messages_screen.dart';
import 'package:felagi_app/screens/notifications_screen.dart';

class MemoryTokens extends TokenStore {
  String? token;
  @override Future<String?> readToken() async => token;
  @override Future<void> save({required String token, required String userId}) async { this.token = token; }
  @override Future<void> clear() async { token = null; }
}
class Harness {
  Harness(Future<http.Response> Function(http.Request) handler) {
    client = ApiClient(httpClient: MockClient(handler), tokenStore: MemoryTokens());
    authApi = AuthApi(client);
    auth = AuthState(api: authApi, tokenStore: client.tokenStore);
    config = ConfigApi(client);
  }
  late final ApiClient client;
  late final AuthApi authApi;
  late final AuthState auth;
  late final ConfigApi config;
  Widget wrap(Widget child) => AppScope(client: client, authApi: authApi,
    needsApi: NeedsApi(client), boostApi: BoostApi(client), offersApi: OffersApi(client),
    configApi: config, appConfig: AppConfigState(api: config), authState: auth, child: child);
}
http.Response ok(Object data) => http.Response(jsonEncode({'success': true, 'data': data, 'meta': {'total': 1}}), 200);
http.Response unavailable() => http.Response(jsonEncode({'success': false, 'error': {'code': 'UNAVAILABLE', 'message': 'Try again'}}), 503);
final loginData = {'access_token': 'test-token', 'user': {'id': 'user-1', 'full_name': 'Test', 'status': 'ACTIVE'}};
void noop(String _) {}
void main() {
  testWidgets('launch handoff survives router redirect and bootstrap; enters Browse once', (tester) async {
    var exchanges = 0;
    final pending = Completer<http.Response>();
    final h = Harness((r) async {
      if (r.url.path.endsWith('/exchange')) { exchanges++; return pending.future; }
      return ok([]);
    });
    final router = AppRouter(localeCode: 'en', onLocaleChange: noop, authState: h.auth,
      launchUri: Uri.parse('https://example.test/test/?handoff_code=test-code')).build();
    await tester.pumpWidget(h.wrap(MaterialApp.router(theme: FgTheme.light(), routerConfig: router)));
    await tester.pump();
    expect(exchanges, 0);
    await h.auth.bootstrap();
    await tester.pump();
    expect(exchanges, 1);
    pending.complete(ok(loginData));
    await tester.pumpAndSettle();
    expect(find.byType(BrowseNeedsScreen), findsOneWidget);
    expect(exchanges, 1);
    await tester.pumpWidget(const SizedBox());
    router.dispose();
  });
  testWidgets('a failed handoff can be retried; it is not permanently marked consumed', (tester) async {
    var attempts = 0;
    final h = Harness((r) async { attempts++; return attempts == 1 ? unavailable() : ok(loginData); });
    await h.auth.bootstrap();
    await tester.pumpWidget(h.wrap(MaterialApp(theme: FgTheme.light(), home: TelegramSignInScreen(
      localeCode: 'en', onLocaleChange: noop, callbackUri: Uri.parse('https://example.test/?handoff_code=retry-code')))));
    await tester.pumpAndSettle();
    expect(attempts, 1);
    await tester.ensureVisible(find.text(fgText('en', 'confirm')).last);
    await tester.tap(find.text(fgText('en', 'confirm')).last);
    await tester.pumpAndSettle();
    expect(attempts, 2);
    expect(h.auth.isAuthenticated, isTrue);
  });
  testWidgets('disposing login before bootstrap removes its listener', (tester) async {
    var attempts = 0;
    final h = Harness((r) async { attempts++; return ok(loginData); });
    await tester.pumpWidget(h.wrap(MaterialApp(home: TelegramSignInScreen(localeCode: 'en', onLocaleChange: noop,
      callbackUri: Uri.parse('https://example.test/?handoff_code=late')))));
    await tester.pump();
    await tester.pumpWidget(const SizedBox());
    await h.auth.bootstrap();
    await tester.pump();
    expect(attempts, 0);
    expect(tester.takeException(), isNull);
  });
  final cases = <({Widget screen, String path, Object data, String visible})>[
    (screen: const AiComparisonScreen(localeCode: 'en', comparisonId: 'c1'), path: '/comparisons/c1',
      data: {'status': 'COMPLETED', 'results': [{'fit_explanation': 'Evidence-based fit', 'score': 8}]}, visible: 'Evidence-based fit'),
    (screen: const ComparisonHistoryScreen(localeCode: 'en', needId: 'n1'), path: '/needs/n1/comparisons',
      data: [{'id': 'c1', 'version_number': 1, 'status': 'COMPLETED'}], visible: 'View comparison 1'),
    (screen: const MessagesScreen(localeCode: 'en', offerId: 'o1'), path: '/offers/o1/messages',
      data: [{'content': 'Delivery tomorrow', 'sender': {'full_name': 'Provider'}}], visible: 'Delivery tomorrow'),
    (screen: const NotificationsScreen(localeCode: 'en'), path: '/notifications',
      data: [{'id': 'n1', 'title': 'New Offer', 'body': 'Received', 'read_at': '2026-10-06'}], visible: 'New Offer'),
  ];
  for (final c in cases) {
    testWidgets('${c.path} renders actual API content', (tester) async {
      final paths = <String>[];
      final h = Harness((r) async { paths.add(r.url.path); return ok(c.data); });
      await tester.pumpWidget(h.wrap(MaterialApp(theme: FgTheme.light(), home: c.screen)));
      await tester.pumpAndSettle();
      expect(paths, contains('/api/v1${c.path}'));
      expect(find.text(c.visible), findsOneWidget);
    });
    testWidgets('${c.path} recovers from failed GET through refresh', (tester) async {
      var requests = 0;
      final h = Harness((r) async => ++requests == 1 ? unavailable() : ok(c.data));
      await tester.pumpWidget(h.wrap(MaterialApp(theme: FgTheme.light(), home: c.screen)));
      await tester.pumpAndSettle();
      expect(find.text(fgText('en', 'loadRecovery')), findsOneWidget);
      await tester.tap(find.text(fgText('en', 'refresh')));
      await tester.pumpAndSettle();
      expect(find.text(c.visible), findsOneWidget);
    });
  }
}
