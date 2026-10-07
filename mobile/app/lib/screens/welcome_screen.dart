import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

/// S001 — Welcome (Premium design: navy header + hero card + 3 stats + 3 CTA).
class WelcomeScreen extends StatelessWidget {
  const WelcomeScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;

  String _t(String key) => fgText(localeCode, key);

  static const _navy950 = Color(0xFF001A33);
  static const _navy800 = Color(0xFF003366);
  static const _navy700 = Color(0xFF0B4D83);
  static const _canvas = Color(0xFFF4F7FA);
  static const _line300 = Color(0xFFD5DDE6);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _canvas,
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 480),
            child: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  _buildHeader(),
                  Padding(
                    padding: const EdgeInsets.fromLTRB(
                      FgTokens.space4, 0, FgTokens.space4, FgTokens.space6,
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Transform.translate(
                          offset: const Offset(0, -60),
                          child: _buildHeroCard(context),
                        ),
                        Transform.translate(
                          offset: const Offset(0, -40),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: [
                              _buildLoginMethods(context),
                              const SizedBox(height: FgTokens.space4),
                              Text(
                                _t('previewNotice'),
                                style: Theme.of(context).textTheme.labelSmall,
                                textAlign: TextAlign.center,
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      padding: const EdgeInsets.fromLTRB(
        FgTokens.space4, FgTokens.space4, FgTokens.space2, FgTokens.space16,
      ),
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [_navy950, _navy800],
        ),
      ),
      child: Row(
        children: [
          Container(
            width: 36, height: 36,
            decoration: const BoxDecoration(
              gradient: LinearGradient(colors: [_navy950, _navy700]),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.search, color: Colors.white, size: 20),
          ),
          const SizedBox(width: 10),
          Text(
            _t('brand'),
            style: const TextStyle(
              color: Colors.white, fontSize: 20, fontWeight: FontWeight.w900,
              letterSpacing: -0.5,
            ),
          ),
          const Spacer(),
          IconButton(
            tooltip: _t('language'),
            icon: const Icon(Icons.language, color: Colors.white),
            onPressed: () => onLocaleChange(localeCode == 'am' ? 'en' : 'am'),
          ),
        ],
      ),
    );
  }

  Widget _buildHeroCard(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(FgTokens.space6),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [_navy950, _navy800, _navy700],
        ),
        borderRadius: BorderRadius.circular(FgTokens.radiusSheet),
        boxShadow: [
          BoxShadow(
            color: _navy950.withValues(alpha: 0.28),
            blurRadius: 24, offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            _t('welcomeEyebrow').toUpperCase(),
            style: const TextStyle(
              color: Colors.white70, fontSize: 11,
              fontWeight: FontWeight.w800, letterSpacing: 1.4,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: FgTokens.space2),
          Text(
            _t('welcomeHeroTitle'),
            style: const TextStyle(
              color: Colors.white, fontSize: 28, fontWeight: FontWeight.w900,
              height: 1.15, letterSpacing: -0.5,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: FgTokens.space3),
          Text(
            _t('welcomeHeroBody'),
            style: const TextStyle(color: Colors.white70, fontSize: 13.5, height: 1.55),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: FgTokens.space6),
          _buildStatRow(),
          const SizedBox(height: FgTokens.space6),
          FgButton(
            label: '＋ ${_t('createNeed')}',
            onPressed: () => context.push('/needs/new'),
          ),
          const SizedBox(height: FgTokens.space3),
          _buildGhostButton(
            context,
            label: '${_t('browseNeeds')} →',
            onPressed: () => context.push('/browse'),
          ),
        ],
      ),
    );
  }

  Widget _buildStatRow() {
    return Row(
      children: [
        _buildStat('2.4K+', _t('statNeeds')),
        const SizedBox(width: FgTokens.space2),
        _buildStat('890+', _t('statProviders')),
        const SizedBox(width: FgTokens.space2),
        _buildStat('4.8★', _t('statRating')),
      ],
    );
  }

  Widget _buildStat(String value, String label) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(
          vertical: FgTokens.space3, horizontal: FgTokens.space2,
        ),
        decoration: BoxDecoration(
          color: Colors.white.withValues(alpha: 0.08),
          borderRadius: BorderRadius.circular(FgTokens.radiusSmall),
          border: Border.all(color: Colors.white.withValues(alpha: 0.12)),
        ),
        child: Column(
          children: [
            Text(
              value,
              style: const TextStyle(
                color: Colors.white, fontSize: 20, fontWeight: FontWeight.w900,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              label,
              style: const TextStyle(
                color: Colors.white70, fontSize: 10, fontWeight: FontWeight.w700,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLoginMethods(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        Text(
          _t('signInMethods'),
          style: const TextStyle(
            color: _navy800, fontSize: 15, fontWeight: FontWeight.w900,
            letterSpacing: -0.3,
          ),
        ),
        const SizedBox(height: FgTokens.space3),
        FgButton(
          label: '✈️  ${_t('signInTelegram')}',
          onPressed: () => context.push('/auth/telegram'),
        ),
        const SizedBox(height: FgTokens.space3),
        _buildGhostButton(
          context,
          label: _t('signInEmail'),
          onPressed: () {},
        ),
        const SizedBox(height: FgTokens.space3),
        _buildGhostButton(
          context,
          label: _t('continueAsGuest'),
          onPressed: () => context.push('/browse'),
        ),
      ],
    );
  }

  Widget _buildGhostButton(
    BuildContext context, {
    required String label,
    required VoidCallback onPressed,
  }) {
    return OutlinedButton(
      onPressed: onPressed,
      style: OutlinedButton.styleFrom(
        minimumSize: const Size.fromHeight(FgTokens.controlProminent),
        foregroundColor: _navy800,
        backgroundColor: Colors.white,
        side: const BorderSide(color: _line300),
        padding: const EdgeInsets.all(FgTokens.space3),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(FgTokens.radiusControl),
        ),
      ),
      child: Text(
        label,
        style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
      ),
    );
  }
}
