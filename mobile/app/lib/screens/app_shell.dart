import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

/// Persistent bottom-nav shell for the four primary user tabs.
///
/// S004 Browse · S009 My Needs · S013 My Offers · S003 Profile.
///
/// Wrapped by [StatefulShellRoute.indexedStack] so each tab keeps its own
/// Navigator state (push depth, scroll, form data) while a single
/// [NavigationBar] stays anchored at the bottom.
class AppShell extends StatelessWidget {
  const AppShell({
    super.key,
    required this.navigationShell,
    required this.localeCode,
    required this.onLocaleChange,
  });

  final StatefulNavigationShell navigationShell;
  final String localeCode;
  final ValueChanged<String> onLocaleChange;

  String _t(String key) => fgText(localeCode, key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: navigationShell,
      bottomNavigationBar: NavigationBar(
        selectedIndex: navigationShell.currentIndex,
        onDestinationSelected: _goBranch,
        destinations: [
          NavigationDestination(
            icon: const Icon(Icons.search_outlined),
            selectedIcon: const Icon(Icons.search),
            label: _t('navBrowse'),
          ),
          NavigationDestination(
            icon: const Icon(Icons.list_alt_outlined),
            selectedIcon: const Icon(Icons.list_alt),
            label: _t('navMyNeeds'),
          ),
          NavigationDestination(
            icon: const Icon(Icons.local_offer_outlined),
            selectedIcon: const Icon(Icons.local_offer),
            label: _t('navMyOffers'),
          ),
          NavigationDestination(
            icon: const Icon(Icons.person_outline),
            selectedIcon: const Icon(Icons.person),
            label: _t('navProfile'),
          ),
        ],
      ),
    );
  }

  void _goBranch(int index) {
    navigationShell.goBranch(
      index,
      // Tapping the already-selected tab resets to its root
      initialLocation: index == navigationShell.currentIndex,
    );
  }
}
