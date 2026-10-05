import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
void main() => runApp(const FelagiExample());
class FelagiExample extends StatefulWidget {
  const FelagiExample({super.key});
  @override State<FelagiExample> createState() => _FelagiExampleState();
}
class _FelagiExampleState extends State<FelagiExample> {
  String locale = 'am';
  bool dark = false;
  String selected = 'S004';
  String text(String key) => fgText(locale, key);
  @override Widget build(BuildContext context) => MaterialApp(
    debugShowCheckedModeBanner: false,
    theme: FgTheme.light(), darkTheme: FgTheme.dark(), themeMode: dark ? ThemeMode.dark : ThemeMode.light,
    locale: Locale(locale), supportedLocales: const [Locale('am'), Locale('en')],
    localizationsDelegates: GlobalMaterialLocalizations.delegates,
    home: Builder(builder: (context) {
      final screen = fgScreens.firstWhere((s) => s['id'] == selected) as Map<String, dynamic>;
      return Scaffold(appBar: AppBar(title: Text(text('brand'))), body: SafeArea(child: SingleChildScrollView(
        padding: const EdgeInsets.all(FgTokens.space4),
        child: Center(child: ConstrainedBox(constraints: const BoxConstraints(maxWidth: 720), child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
          Text(text('previewNotice')),
          DropdownButtonFormField<String>(value: locale, decoration: InputDecoration(labelText: text('language')), items: const [DropdownMenuItem(value: 'am', child: Text('አማርኛ')), DropdownMenuItem(value: 'en', child: Text('English'))], onChanged: (v) => setState(() => locale = v!)),
          SwitchListTile(title: Text(text('dark')), value: dark, onChanged: (v) => setState(() => dark = v)),
          DropdownButtonFormField<String>(value: selected, isExpanded: true, decoration: InputDecoration(labelText: text('screen')), items: fgScreens.map((s) => DropdownMenuItem<String>(value: s['id'] as String, child: Text(text(s['title_key'] as String), softWrap: true))).toList(), onChanged: (v) => setState(() => selected = v!)),
          const SizedBox(height: FgTokens.space4),
          Text(text(screen['title_key'] as String), style: Theme.of(context).textTheme.titleLarge),
          Text(screen['path'] as String),
          FgTextField(label: text('fieldTitle'), helper: text('sampleOnly')),
          const SizedBox(height: FgTokens.space4),
          FgButton(label: text('continue'), onPressed: () => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(text('simulation'))))),
          const SizedBox(height: FgTokens.space4),
          FgTelegramDeliveryBadge(state: FgTelegramState.uncertain, label: text('stateUncertain')),
          Text(text('telegramHelp')),
        ]))),
      )));
    }),
  );
}
