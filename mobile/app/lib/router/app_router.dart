import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../screens/welcome_screen.dart';
import '../screens/telegram_signin_screen.dart';
import '../screens/profile_screen.dart';
import '../screens/browse_needs_screen.dart';
import '../screens/need_detail_screen.dart';
import '../screens/create_edit_need_screen.dart';
import '../screens/boost_screen.dart';
import '../screens/public_preview_screen.dart';
import '../screens/need_created_screen.dart';
import '../screens/my_needs_screen.dart';
import '../screens/received_offers_screen.dart';
import '../screens/submit_offer_screen.dart';
import '../screens/offer_detail_screen.dart';
import '../screens/my_offers_screen.dart';
import '../screens/compare_confirm_screen.dart';
import '../screens/ai_comparison_screen.dart';
import '../screens/comparison_history_screen.dart';
import '../screens/messages_screen.dart';
import '../screens/notifications_screen.dart';
import '../screens/rating_screen.dart';
import '../screens/report_screen.dart';
import '../screens/telegram_status_screen.dart';
import '../screens/placeholder_screen.dart';
import '../preview_mode.dart';
import '../state/auth_state.dart';
import '../screens/app_shell.dart';
import '../screens/offer_unlock_screen.dart';

/// Central route map for all 46 canonical Felagi screens.
///
/// Each route entry: (path, screenId, titleKey).
/// Real screens override the placeholder (S001 currently).
class AppRouter {
  AppRouter({
    required this.localeCode,
    required this.onLocaleChange,
    required this.authState,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;

  /// Live auth source. GoRouter listens to this via `refreshListenable`
  /// and re-evaluates `redirect` without recreating the router.
  final AuthState authState;

  bool get isAuthenticated => authState.isAuthenticated;

  static const List<({String path, String id, String titleKey})> screens = [
    // ── User screens (S001–S023) ──
    (path: '/welcome',                   id: 'S001', titleKey: 'screenS001'),
    (path: '/auth/telegram',             id: 'S002', titleKey: 'screenS002'),
    (path: '/profile',                   id: 'S003', titleKey: 'screenS003'),
    (path: '/browse',                    id: 'S004', titleKey: 'screenS004'),
    (path: '/needs/new',                 id: 'S005', titleKey: 'screenS005'),
    (path: '/needs/new/public-preview',  id: 'S006', titleKey: 'screenS006'),
    (path: '/needs/:id/created',         id: 'S007', titleKey: 'screenS007'),
    (path: '/needs/:id',                 id: 'S008', titleKey: 'screenS008'),
    (path: '/my/needs',                  id: 'S009', titleKey: 'screenS009'),
    (path: '/needs/:id/offers',          id: 'S010', titleKey: 'screenS010'),
    (path: '/needs/:id/offers/new',      id: 'S011', titleKey: 'screenS011'),
    (path: '/offers/:id',                id: 'S012', titleKey: 'screenS012'),
    (path: '/my/offers',                 id: 'S013', titleKey: 'screenS013'),
    (path: '/needs/:id/compare',         id: 'S014', titleKey: 'screenS014'),
    (path: '/comparisons/:id',           id: 'S015', titleKey: 'screenS015'),
    (path: '/needs/:id/comparisons',     id: 'S016', titleKey: 'screenS016'),
    (path: '/offers/:id/messages',       id: 'S017', titleKey: 'screenS017'),
    (path: '/notifications',             id: 'S018', titleKey: 'screenS018'),
    (path: '/needs/:id/boost',           id: 'S019', titleKey: 'screenS019'),
    (path: '/needs/:id/rating',          id: 'S020', titleKey: 'screenS020'),
    (path: '/support/report',            id: 'S021', titleKey: 'screenS021'),
    (path: '/needs/:id/publications',    id: 'S022', titleKey: 'screenS022'),
    (path: '/needs/:id/offers/unlock',   id: 'S023', titleKey: 'screenS023'),

    // ── Admin screens (A001–A023) ──
    (path: '/admin/dashboard',                   id: 'A001', titleKey: 'screenA001'),
    (path: '/admin/telegram',                    id: 'A002', titleKey: 'screenA002'),
    (path: '/admin/health',                      id: 'A003', titleKey: 'screenA003'),
    (path: '/admin/features',                    id: 'A004', titleKey: 'screenA004'),
    (path: '/admin/marketplace',                 id: 'A005', titleKey: 'screenA005'),
    (path: '/admin/ai',                          id: 'A006', titleKey: 'screenA006'),
    (path: '/admin/payments',                    id: 'A007', titleKey: 'screenA007'),
    (path: '/admin/users',                       id: 'A008', titleKey: 'screenA008'),
    (path: '/admin/content',                     id: 'A009', titleKey: 'screenA009'),
    (path: '/admin/notifications',               id: 'A010', titleKey: 'screenA010'),
    (path: '/admin/files',                       id: 'A011', titleKey: 'screenA011'),
    (path: '/admin/jobs',                        id: 'A012', titleKey: 'screenA012'),
    (path: '/admin/backups',                     id: 'A013', titleKey: 'screenA013'),
    (path: '/admin/integrity',                   id: 'A014', titleKey: 'screenA014'),
    (path: '/admin/security',                    id: 'A015', titleKey: 'screenA015'),
    (path: '/admin/audit',                       id: 'A016', titleKey: 'screenA016'),
    (path: '/admin/settings',                    id: 'A017', titleKey: 'screenA017'),
    (path: '/admin/recovery',                    id: 'A018', titleKey: 'screenA018'),
    (path: '/admin/safe-mode',                   id: 'A019', titleKey: 'screenA019'),
    (path: '/admin/monetization',                id: 'A020', titleKey: 'screenA020'),
    (path: '/admin/maintenance',                 id: 'A021', titleKey: 'screenA021'),
    (path: '/admin/reports',                     id: 'A022', titleKey: 'screenA022'),
    (path: '/admin/monetization/sponsored-ads',  id: 'A023', titleKey: 'screenA023'),
  ];


  /// Preview mode wins over auth for the *initial* landing screen.
  /// Once the app is running, `redirect` handles subsequent navigation.
  String _resolveInitialLocation() {
    if (PreviewMode.enabled) return '/welcome';
    return isAuthenticated ? '/browse' : '/welcome';
  }

  GoRouter build() {
    return GoRouter(
      initialLocation: _resolveInitialLocation(),
      // Live auth updates — GoRouter re-runs `redirect` in place,
      // without recreating the whole router tree.
      refreshListenable: authState,
      // Telegram Widget callback lands with malformed URL like:
      //   https://zagcreativity.com/test/tgAuthResult=...
      // Redirect it to /auth/telegram so the screen can handle the widget data.
      redirect: (context, state) {
        final loc = state.uri.toString();
        final path = state.uri.path;

        // Telegram widget auth result → route to sign-in screen
        if (loc.contains('tgAuthResult=')) {
          return '/auth/telegram';
        }

        // Preview mode: ?preview=1 ካለ ሁሉንም ስክሪኖች ማየት ይቻላል
        final previewMode = PreviewMode.enabled ||
            state.uri.queryParameters['preview'] == '1';

        // Root path — resolve by auth + preview.
        if (path == '/' || path.isEmpty) {
          if (previewMode) return '/welcome';
          return isAuthenticated ? '/browse' : '/welcome';
        }

        // Authenticated users must not stay on welcome/sign-in
        // (preview mode bypasses this guard so QA can open every screen).
        if (!previewMode &&
            isAuthenticated &&
            (path == '/welcome' || path == '/auth/telegram')) {
          return '/browse';
        }

        return null;
      },
      // Any unmatched route → show Telegram sign-in (which will decode the widget).
      errorBuilder: (context, state) => TelegramSignInScreen(
        localeCode: localeCode,
        onLocaleChange: onLocaleChange,
      ),
      routes: [
        GoRoute(
          path: '/welcome',
          builder: (context, state) => WelcomeScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
          ),
        ),

        // ── S023 Offer Unlock ──
        GoRoute(
          path: '/needs/:id/offers/unlock',
          builder: (context, state) => OfferUnlockScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
            needId: state.pathParameters['id'] ?? '',
          ),
        ),
        GoRoute(
          path: '/auth/telegram',
          builder: (context, state) => TelegramSignInScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
          ),
        ),

        // ── Persistent bottom-nav shell (S003/S004/S009/S013) ──
        StatefulShellRoute.indexedStack(
          builder: (context, state, navigationShell) => AppShell(
            navigationShell: navigationShell,
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
          ),
          branches: [
            StatefulShellBranch(routes: [
              GoRoute(
                path: '/browse',
                builder: (context, state) => BrowseNeedsScreen(
                  localeCode: localeCode,
                  onLocaleChange: onLocaleChange,
                ),
              ),
            ]),
            StatefulShellBranch(routes: [
              GoRoute(
                path: '/my/needs',
                builder: (context, state) => MyNeedsScreen(
                  localeCode: localeCode,
                  onLocaleChange: onLocaleChange,
                ),
              ),
            ]),
            StatefulShellBranch(routes: [
              GoRoute(
                path: '/my/offers',
                builder: (context, state) => MyOffersScreen(
                  localeCode: localeCode,
                  onLocaleChange: onLocaleChange,
                ),
              ),
            ]),
            StatefulShellBranch(routes: [
              GoRoute(
                path: '/profile',
                builder: (context, state) => ProfileScreen(
                  localeCode: localeCode,
                  onLocaleChange: onLocaleChange,
                ),
              ),
            ]),
          ],
        ),
        GoRoute(
          path: '/needs/new',
          builder: (context, state) => CreateEditNeedScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
          ),
        ),
        GoRoute(
          path: '/needs/new/public-preview',
          builder: (context, state) => PublicPreviewScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
          ),
        ),
        GoRoute(
          path: '/needs/:id/boost',
          builder: (context, state) => BoostScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
            needId: state.pathParameters['id'] ?? '',
          ),
        ),
        GoRoute(
          path: '/needs/:id/created',
          builder: (context, state) => NeedCreatedScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
            needId: state.pathParameters['id'] ?? '',
          ),
        ),
        GoRoute(
          path: '/needs/:id/offers',
          builder: (context, state) => ReceivedOffersScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
            needId: state.pathParameters['id'] ?? '',
          ),
        ),
        GoRoute(
          path: '/needs/:id',
          builder: (context, state) => NeedDetailScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
            needId: state.pathParameters['id'] ?? '',
          ),
        ),
        // ── L355: S011 Submit Offer ──
        GoRoute(
          path: '/needs/:id/offers/new',
          builder: (context, state) => SubmitOfferScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
            needId: state.pathParameters['id'] ?? '',
          ),
        ),
        // ── L355: S012 Offer Detail ──
        GoRoute(
          path: '/offers/:id',
          builder: (context, state) => OfferDetailScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
            offerId: state.pathParameters['id'] ?? '',
          ),
        ),
        // ── L355: S014 Compare Confirm ──
        GoRoute(
          path: '/needs/:id/compare',
          builder: (context, state) => CompareConfirmScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
            needId: state.pathParameters['id'] ?? '',
          ),
        ),
        // ── L356: S015 AI Comparison ──
        GoRoute(
          path: '/comparisons/:id',
          builder: (context, state) => AiComparisonScreen(
            localeCode: localeCode,
            comparisonId: state.pathParameters['id'] ?? '',
          ),
        ),
        // ── L356: S016 Comparison History ──
        GoRoute(
          path: '/needs/:id/comparisons',
          builder: (context, state) => ComparisonHistoryScreen(
            localeCode: localeCode,
            needId: state.pathParameters['id'] ?? '',
          ),
        ),
        // ── L356: S017 Messages ──
        GoRoute(
          path: '/offers/:id/messages',
          builder: (context, state) => MessagesScreen(
            localeCode: localeCode,
            offerId: state.pathParameters['id'] ?? '',
          ),
        ),
        // ── L356: S018 Notifications ──
        GoRoute(
          path: '/notifications',
          builder: (context, state) => NotificationsScreen(
            localeCode: localeCode,
          ),
        ),
        // ── L357: S020 Rating ──
        GoRoute(
          path: '/needs/:id/rating',
          builder: (context, state) => RatingScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
            needId: state.pathParameters['id'] ?? '',
            toUserId: state.uri.queryParameters['to_user_id'],
          ),
        ),
        // ── L357: S021 Report ──
        GoRoute(
          path: '/support/report',
          builder: (context, state) => ReportScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
            entityType: state.uri.queryParameters['entity_type'],
            entityId: state.uri.queryParameters['entity_id'],
          ),
        ),
        // ── L357: S022 Telegram Status ──
        GoRoute(
          path: '/needs/:id/publications',
          builder: (context, state) => TelegramStatusScreen(
            localeCode: localeCode,
            onLocaleChange: onLocaleChange,
            needId: state.pathParameters['id'] ?? '',
          ),
        ),
        for (final s in screens.where(
            (s) =>
                s.id != 'S001' &&
                s.id != 'S002' &&
                s.id != 'S003' &&
                s.id != 'S004' &&
                s.id != 'S005' &&
                s.id != 'S006' &&
                s.id != 'S007' &&
                s.id != 'S008' &&
                s.id != 'S009' &&
                s.id != 'S010' &&
                s.id != 'S011' &&
                s.id != 'S012' &&
                s.id != 'S013' &&
                s.id != 'S014' &&
                s.id != 'S015' &&
                s.id != 'S016' &&
                s.id != 'S017' &&
                s.id != 'S018' &&
                s.id != 'S019' &&
                s.id != 'S020' &&
                s.id != 'S021' &&
                s.id != 'S022' &&
                s.id != 'S023'))
          GoRoute(
            path: s.path,
            builder: (context, state) => PlaceholderScreen(
              screenId: s.id,
              titleKey: s.titleKey,
              path: s.path,
              localeCode: localeCode,
            ),
          ),
      ],
    );
  }
}
