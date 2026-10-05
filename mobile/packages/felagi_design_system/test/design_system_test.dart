import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
void main() {
  testWidgets('primary target is at least56 and loading prevents dispatch', (tester) async {
    var taps = 0;
    await tester.pumpWidget(MaterialApp(theme: FgTheme.dark(), home: Scaffold(body: FgButton(label: 'Submit', isLoading: true, onPressed: () => taps++))));
    expect(tester.getSize(find.byType(OutlinedButton)).height, greaterThanOrEqualTo(56));
    await tester.tap(find.byType(OutlinedButton));
    expect(taps, 0);
  });
  test('canonical screen IDs unique and both locale keysets agree', () {
    expect(fgScreens.map((s) => s['id']).toSet().length, 46);
    expect((fgLocales['am'] as Map).keys.toSet(), (fgLocales['en'] as Map).keys.toSet());
    expect(FgTelegramState.values.map((s) => s.name), containsAll(['skipped','paused','retry']));
  });
}
