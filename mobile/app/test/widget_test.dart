import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:felagi_design_system/felagi_design_system.dart';

import 'package:felagi_app/api/api_client.dart';
import 'package:felagi_app/api/token_store.dart';
import 'package:felagi_app/api/auth_api.dart';
import 'package:felagi_app/api/needs_api.dart';
import 'package:felagi_app/api/boost_api.dart';
import 'package:felagi_app/app_scope.dart';
import 'package:felagi_app/state/auth_state.dart';
import 'package:felagi_app/main.dart';
import 'package:felagi_app/screens/welcome_screen.dart';
import 'package:felagi_app/screens/telegram_signin_screen.dart';
import 'package:felagi_app/screens/profile_screen.dart';
import 'package:felagi_app/screens/browse_needs_screen.dart';
import 'package:felagi_app/screens/need_detail_screen.dart';
import 'package:felagi_app/screens/create_edit_need_screen.dart';
import 'package:felagi_app/screens/boost_screen.dart';
import 'package:felagi_app/router/app_router.dart';


/// In-memory token store for tests — no platform channels touched.
class _FakeTokenStore extends TokenStore {
  _FakeTokenStore();
  final _mem = <String, String>{};

  @override
  Future<String?> readToken() async => _mem['felagi_token'];
  @override
  Future<String?> readUserId() async => _mem['felagi_user_id'];
  @override
  Future<void> save({required String token, required String userId}) async {
    _mem['felagi_token'] = token;
    _mem['felagi_user_id'] = userId;
  }
  @override
  Future<void> clear() async => _mem.clear();
  @override
  Future<bool> get hasToken async => _mem.containsKey('felagi_token');
}

void main() {
  group('AppRouter — canonical screens', () {
    test('all 46 screens have unique IDs', () {
      expect(AppRouter.screens.map((s) => s.id).toSet().length, 46);
    });

    test('S001 is first user screen', () {
      expect(AppRouter.screens.first.id, 'S001');
    });

    test('all user screens present', () {
      final ids = AppRouter.screens.map((s) => s.id).toSet();
      for (var i = 1; i <= 23; i++) {
        expect(ids, contains('S${i.toString().padLeft(3, '0')}'));
      }
    });

    test('all admin screens present', () {
      final ids = AppRouter.screens.map((s) => s.id).toSet();
      for (var i = 1; i <= 23; i++) {
        expect(ids, contains('A${i.toString().padLeft(3, '0')}'));
      }
    });
  });

  group('FelagiApp — screens', () {
    testWidgets('initial route renders S001 Welcome', (tester) async {
      await tester.pumpWidget(const FelagiApp());
      await tester.pumpAndSettle();
      expect(find.byType(WelcomeScreen), findsOneWidget);
    });
  });

  group('S002 Telegram sign-in', () {
    testWidgets('renders sign-in button, cancel, and help text', (tester) async {
      // Wrap in a minimal AppScope so the screen can resolve DI if needed.
      // We do NOT tap "signIn" here — it would trigger a real network call.
      await tester.pumpWidget(_wrapWithScope(
        const TelegramSignInScreen(
          localeCode: 'am',
          onLocaleChange: _noop,
        ),
      ));
      expect(find.text(fgText('am', 'screenS002')), findsOneWidget);
      expect(find.text(fgText('am', 'signIn')), findsOneWidget);
      expect(find.text(fgText('am', 'cancel')), findsOneWidget);
      expect(find.text(fgText('am', 'authHelp')), findsOneWidget);
      expect(find.text(fgText('am', 'stateReady')), findsOneWidget);
    });
  });

  group('S003 Profile', () {
    testWidgets('renders form fields', (tester) async {
      await tester.pumpWidget(MaterialApp(
        theme: FgTheme.light(),
        home: ProfileScreen(
          localeCode: 'am',
          onLocaleChange: (_) {},
        ),
      ));
      expect(find.text(fgText('am', 'fieldFullName')), findsOneWidget);
      expect(find.text(fgText('am', 'fieldPhoneNumber')), findsOneWidget);
      expect(find.text(fgText('am', 'save')), findsOneWidget);
    });
  });

  group('S004 Browse Needs', () {
    testWidgets('renders search + real API list', (tester) async {
      await tester.pumpWidget(_wrapWithNeeds(
        const BrowseNeedsScreen(
          localeCode: 'am',
          onLocaleChange: _noop,
        ),
        [
          {
            'id': 'n-001',
            'title': 'API Need One',
            'description': 'First need',
            'status': 'OPEN',
            'currency': 'ETB',
            'budget_min': '1000.00',
            'budget_max': '5000.00',
            'offer_count': 2,
            'category': {'id': 'c1', 'name_en': 'Tech', 'name_am': 'ቴክ', 'slug': 'tech'},
          },
          {
            'id': 'n-002',
            'title': 'API Need Two',
            'description': 'Second need',
            'status': 'OPEN',
            'currency': 'ETB',
            'offer_count': 0,
          },
        ],
      ));
      // Wait for the postFrameCallback fetch to complete
      await tester.pump(); // trigger postFrameCallback
      await tester.pump(const Duration(milliseconds: 50)); // let future resolve
      await tester.pumpAndSettle();

      expect(find.text(fgText('am', 'screenS004')), findsOneWidget);
      expect(find.text(fgText('am', 'fieldKeyword')), findsOneWidget);
      expect(find.text('API Need One'), findsOneWidget);
      expect(find.text('API Need Two'), findsOneWidget);
    });

    testWidgets('shows empty state when API returns []', (tester) async {
      await tester.pumpWidget(_wrapWithNeeds(
        const BrowseNeedsScreen(
          localeCode: 'am',
          onLocaleChange: _noop,
        ),
        const [],
      ));
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 50));
      await tester.pumpAndSettle();

      expect(find.text(fgText('am', 'stateEmpty')), findsAtLeastNWidgets(1));
    });
  });

  group('S005 Create/Edit Need', () {
    testWidgets('renders form + acknowledgement checkbox', (tester) async {
      await tester.pumpWidget(_wrapWithCategories(
        const CreateEditNeedScreen(
          localeCode: 'am',
          onLocaleChange: _noop,
        ),
      ));
      // Wait for category fetch
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 50));
      await tester.pumpAndSettle();

      expect(find.text(fgText('am', 'screenS005')), findsOneWidget);
      expect(find.text("${fgText('am', 'fieldTitle')} *"), findsOneWidget);
      expect(find.text("${fgText('am', 'fieldDescription')} *"), findsOneWidget);
      expect(find.byType(CheckboxListTile), findsOneWidget);
      expect(find.text(fgText('am', 'requiredConsent')), findsOneWidget);
    });

    testWidgets('save button disabled until form valid', (tester) async {
      await tester.pumpWidget(_wrapWithCategories(
        const CreateEditNeedScreen(
          localeCode: 'am',
          onLocaleChange: _noop,
        ),
      ));
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 50));
      await tester.pumpAndSettle();

      final saveBtn = find.widgetWithText(OutlinedButton, fgText('am', 'save'));
      expect(saveBtn, findsOneWidget);
      expect(tester.widget<OutlinedButton>(saveBtn).onPressed, isNull);
    });
  });

  group('S019 Boost/Payments', () {
    testWidgets('renders packages list from API', (tester) async {
      await tester.pumpWidget(_wrapWithBoost(
        const BoostScreen(
          localeCode: 'am',
          onLocaleChange: _noop,
          needId: 'n-001',
        ),
        packages: const [
          {'id': 'bp-1', 'duration_days': 1, 'price': '25.00', 'currency': 'ETB', 'active': true},
          {'id': 'bp-3', 'duration_days': 3, 'price': '49.00', 'currency': 'ETB', 'active': true},
          {'id': 'bp-7', 'duration_days': 7, 'price': '99.00', 'currency': 'ETB', 'active': true},
        ],
      ));

      // Let the postFrameCallback + future resolve
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 50));
      await tester.pumpAndSettle();

      expect(find.text(fgText('am', 'screenS019')), findsOneWidget);
      expect(find.text('1 ${fgText('am', 'packageDays')}'), findsOneWidget);
      expect(find.text('3 ${fgText('am', 'packageDays')}'), findsOneWidget);
      expect(find.text('7 ${fgText('am', 'packageDays')}'), findsOneWidget);
      expect(find.text('25.00 ETB'), findsOneWidget);
      expect(find.text('99.00 ETB'), findsOneWidget);
    });

    testWidgets('checkout button disabled until package selected', (tester) async {
      await tester.pumpWidget(_wrapWithBoost(
        const BoostScreen(
          localeCode: 'am',
          onLocaleChange: _noop,
          needId: 'n-001',
        ),
        packages: const [
          {'id': 'bp-1', 'duration_days': 1, 'price': '25.00', 'currency': 'ETB', 'active': true},
        ],
      ));
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 50));
      await tester.pumpAndSettle();

      final checkoutBtn =
          find.widgetWithText(OutlinedButton, fgText('am', 'checkout'));
      expect(checkoutBtn, findsOneWidget);
      expect(tester.widget<OutlinedButton>(checkoutBtn).onPressed, isNull);
    });

    testWidgets('checkout transitions to pending', (tester) async {
      await tester.pumpWidget(_wrapWithBoost(
        const BoostScreen(
          localeCode: 'am',
          onLocaleChange: _noop,
          needId: 'n-001',
        ),
        packages: const [
          {'id': 'bp-1', 'duration_days': 1, 'price': '25.00', 'currency': 'ETB', 'active': true},
        ],
        boostResponse: const {
          'boost': {'id': 'b-1', 'status': 'PENDING'},
          'payment': {
            'id': 'p-1',
            'status': 'PENDING',
            'provider': 'null',
            'amount': '25.00',
            'currency': 'ETB',
            'checkout_url': null,
          },
        },
      ));
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 50));
      await tester.pumpAndSettle();

      // Select package
      await tester.tap(find.text('1 ${fgText('am', 'packageDays')}'));
      await tester.pump();

      // Tap checkout
      await tester.tap(
          find.widgetWithText(OutlinedButton, fgText('am', 'checkout')));
      await tester.pump(); // checkout phase

      // Let async POST resolve + setState(pending)
      await tester.pump(const Duration(milliseconds: 100));
      await tester.pumpAndSettle();

      expect(find.text(fgText('am', 'statePending')), findsAtLeastNWidgets(1));
      // checkout button should be gone (phase changed)
      expect(
        find.widgetWithText(OutlinedButton, fgText('am', 'checkout')),
        findsNothing,
      );
    });
  });

  group('S008 Need Detail', () {
    testWidgets('renders real API data', (tester) async {
      await tester.pumpWidget(_wrapWithNeed(
        const NeedDetailScreen(
          localeCode: 'am',
          onLocaleChange: _noop,
          needId: 'n-001',
        ),
        {
          'id': 'n-001',
          'title': 'Real API Need',
          'description': 'Fetched from mock backend',
          'status': 'OPEN',
          'currency': 'ETB',
          'budget_min': '1000.00',
          'budget_max': '5000.00',
          'offer_count': 3,
          'is_owner': true,
          'version': 1,
          'category': {'id': 'c1', 'name_en': 'Tech', 'name_am': 'ቴክ', 'slug': 'tech'},
          'location_text': 'Addis Ababa',
        },
      ));
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 50));
      await tester.pumpAndSettle();

      expect(find.text(fgText('am', 'screenS008')), findsOneWidget);
      expect(find.text('Real API Need'), findsOneWidget);
      expect(find.text('Fetched from mock backend'), findsOneWidget);
      // Owner actions visible (status OPEN + is_owner true)
      expect(find.text(fgText('am', 'completeNeed')), findsOneWidget);
      expect(find.text(fgText('am', 'cancelNeed')), findsOneWidget);
    });
  });
}

void _noop(String _) {}

/// Wrap a widget in AppScope with mocked dependencies (no real network).
Widget _wrapWithScope(Widget child) {
  final mockClient = MockClient((request) async {
    // Never actually hit the network in tests.
    return http.Response(
      '{"success":false,"error":{"code":"TEST","message":"stub"}}',
      500,
    );
  });
  final apiClient = ApiClient(
    httpClient: mockClient,
    tokenStore: _FakeTokenStore(),
  );
  final authApi = AuthApi(apiClient);
  final needsApi = NeedsApi(apiClient);
  final boostApi = BoostApi(apiClient);
  final authState = AuthState(api: authApi, tokenStore: apiClient.tokenStore);
  return MaterialApp(
    theme: FgTheme.light(),
    home: AppScope(
      client: apiClient,
      authApi: authApi,
      needsApi: needsApi,
      boostApi: boostApi,
      authState: authState,
      child: child,
    ),
  );
}

/// Wrap with a mock that returns a specific needs list.
Widget _wrapWithNeeds(Widget child, List<Map<String, dynamic>> needs) {
  final body = jsonEncode({
    'success': true,
    'data': needs,
    'meta': {
      'page': 1,
      'per_page': 20,
      'total': needs.length,
      'has_more': false,
    },
  });
  final mockClient = MockClient((request) async {
    if (request.url.path.endsWith('/needs')) {
      return http.Response(body, 200,
          headers: {'content-type': 'application/json'});
    }
    return http.Response(
      '{"success":false,"error":{"code":"NOT_FOUND","message":"stub"}}',
      404,
    );
  });
  final apiClient = ApiClient(
    httpClient: mockClient,
    tokenStore: _FakeTokenStore(),
  );
  final authApi = AuthApi(apiClient);
  final needsApi = NeedsApi(apiClient);
  final boostApi = BoostApi(apiClient);
  final authState = AuthState(api: authApi, tokenStore: apiClient.tokenStore);
  return MaterialApp(
    theme: FgTheme.light(),
    home: AppScope(
      client: apiClient,
      authApi: authApi,
      needsApi: needsApi,
      boostApi: boostApi,
      authState: authState,
      child: child,
    ),
  );
}

/// Wrap with a mock that returns a single need at /needs/{id}.
Widget _wrapWithNeed(Widget child, Map<String, dynamic> need) {
  final body = jsonEncode({
    'success': true,
    'data': need,
    'message': 'Need retrieved.',
  });
  final mockClient = MockClient((request) async {
    final path = request.url.path;
    if (path.contains('/needs/') || path.endsWith('/needs')) {
      return http.Response(body, 200,
          headers: {'content-type': 'application/json'});
    }
    return http.Response(
      '{"success":false,"error":{"code":"NOT_FOUND","message":"stub"}}',
      404,
    );
  });
  final apiClient = ApiClient(
    httpClient: mockClient,
    tokenStore: _FakeTokenStore(),
  );
  final authApi = AuthApi(apiClient);
  final needsApi = NeedsApi(apiClient);
  final boostApi = BoostApi(apiClient);
  final authState = AuthState(api: authApi, tokenStore: apiClient.tokenStore);
  return MaterialApp(
    theme: FgTheme.light(),
    home: AppScope(
      client: apiClient,
      authApi: authApi,
      needsApi: needsApi,
      boostApi: boostApi,
      authState: authState,
      child: child,
    ),
  );
}

/// Wrap with a MockClient serving boost-packages + POST boosts.
Widget _wrapWithBoost(
  Widget child, {
  List<Map<String, dynamic>> packages = const [],
  Map<String, dynamic>? boostResponse,
}) {
  final packagesBody = jsonEncode({
    'success': true,
    'data': packages,
    'meta': const {},
  });
  final boostBody = jsonEncode({
    'success': true,
    'data': boostResponse ??
        const {
          'boost': {'id': 'b-1', 'status': 'PENDING'},
          'payment': null,
        },
    'message': 'Boost created.',
  });

  final mockClient = MockClient((request) async {
    final path = request.url.path;
    if (request.method == 'GET' && path.endsWith('/boost-packages')) {
      return http.Response(packagesBody, 200,
          headers: {'content-type': 'application/json'});
    }
    if (request.method == 'POST' && path.contains('/boosts')) {
      return http.Response(boostBody, 201,
          headers: {'content-type': 'application/json'});
    }
    return http.Response(
      '{"success":false,"error":{"code":"NOT_FOUND","message":"stub"}}',
      404,
    );
  });

  final apiClient = ApiClient(
    httpClient: mockClient,
    tokenStore: _FakeTokenStore(),
  );
  final authApi = AuthApi(apiClient);
  final needsApi = NeedsApi(apiClient);
  final boostApi = BoostApi(apiClient);
  final authState = AuthState(api: authApi, tokenStore: apiClient.tokenStore);

  return MaterialApp(
    theme: FgTheme.light(),
    home: AppScope(
      client: apiClient,
      authApi: authApi,
      needsApi: needsApi,
      boostApi: boostApi,
      authState: authState,
      child: child,
    ),
  );
}

/// Wrap with a mock that serves categories + empty needs + accepts POST.
Widget _wrapWithCategories(Widget child) {
  final categoriesBody = jsonEncode({
    'success': true,
    'data': [
      {'id': 'c1', 'name_en': 'Tech', 'name_am': 'ቴክ', 'slug': 'tech', 'active': true},
      {'id': 'c2', 'name_en': 'Services', 'name_am': 'አገልግሎቶች', 'slug': 'services', 'active': true},
    ],
    'meta': const {},
  });

  final mockClient = MockClient((request) async {
    final path = request.url.path;
    if (request.method == 'GET' && path.endsWith('/categories')) {
      return http.Response(categoriesBody, 200,
          headers: {'content-type': 'application/json'});
    }
    return http.Response(
      '{"success":false,"error":{"code":"NOT_FOUND","message":"stub"}}',
      404,
    );
  });

  final apiClient = ApiClient(
    httpClient: mockClient,
    tokenStore: _FakeTokenStore(),
  );
  final authApi = AuthApi(apiClient);
  final needsApi = NeedsApi(apiClient);
  final boostApi = BoostApi(apiClient);
  final authState = AuthState(api: authApi, tokenStore: apiClient.tokenStore);

  return MaterialApp(
    theme: FgTheme.light(),
    home: AppScope(
      client: apiClient,
      authApi: authApi,
      needsApi: needsApi,
      boostApi: boostApi,
      authState: authState,
      child: child,
    ),
  );
}
