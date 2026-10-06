import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

import '../app_scope.dart';

/// S003 — Profile setup / edit.
///
/// Design contract:
///   - path: /profile
///   - primary_action: save
///   - states: loading, current, validation, saved, stale, suspended, offline
///   - fields: full_name, phone_number (optional), profile_photo_url
class ProfileScreen extends StatefulWidget {
  const ProfileScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _nameCtrl = TextEditingController(text: 'Test User');
  final _phoneCtrl = TextEditingController(text: '');
  bool _saving = false;

  String _t(String key) => fgText(widget.localeCode, key);

  @override
  void dispose() {
    _nameCtrl.dispose();
    _phoneCtrl.dispose();
    super.dispose();
  }

  void _save() {
    setState(() => _saving = true);
    // TODO (STEP 33): PATCH /api/v1/profile with If-Match
    Future.delayed(const Duration(milliseconds: 500), () {
      if (mounted) {
        setState(() => _saving = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(_t('draftSaved'))),
        );
      }
    });
  }

  Future<void> _logout() async {
    try {
      await AppScope.of(context).authState.signOut();
    } catch (_) {}
    if (!mounted) return;
    context.go('/welcome');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(_t('screenS003')),
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
                  FgStatusBadge(
                    label: _t('stateReady'),
                    kind: FgStatusKind.info,
                  ),
                  const SizedBox(height: FgTokens.space4),
                  FgTextField(
                    label: _t('fieldFullName'),
                    controller: _nameCtrl,
                  ),
                  const SizedBox(height: FgTokens.space3),
                  FgTextField(
                    label: _t('fieldPhoneNumber'),
                    controller: _phoneCtrl,
                  ),
                  const SizedBox(height: FgTokens.space6),
                  FgButton(
                    label: _t('save'),
                    isLoading: _saving,
                    onPressed: _saving ? null : _save,
                  ),
                  const SizedBox(height: FgTokens.space6),
                  const Divider(),
                  ListTile(
                    leading: const Icon(Icons.list_alt),
                    title: Text(_t('screenS009')),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => context.push('/my/needs'),
                  ),
                  ListTile(
                    leading: const Icon(Icons.local_offer_outlined),
                    title: Text(_t('screenS013')),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => context.push('/my/offers'),
                  ),
                  ListTile(
                    leading: const Icon(Icons.notifications_outlined),
                    title: Text(_t('screenS018')),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => context.push('/notifications'),
                  ),
                  const SizedBox(height: FgTokens.space4),
                  FgButton(
                    label: _t('logout'),
                    variant: FgButtonVariant.secondary,
                    onPressed: _logout,
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
