import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import '../api/api_config.dart';
import 'remote_collection.dart';

class ComparisonHistoryScreen extends StatelessWidget {
  const ComparisonHistoryScreen({super.key, required this.localeCode, required this.needId});
  final String localeCode, needId;
  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(title: Text(fgText(localeCode, 'screenS016'))),
    body: SafeArea(child: RemoteCollection(path: ApiConfig.needComparisons(needId), localeCode: localeCode,
      row: (context, row, reload) => ListTile(
        title: Text('${fgText(localeCode, 'viewComparison')} ${row['version_number'] ?? fgText(localeCode, 'unknown')}'),
        subtitle: Text('${row['status'] ?? fgText(localeCode, 'unknown')} • ${row['requested_at'] ?? fgText(localeCode, 'unknown')}'),
        onTap: row['id'] == null ? null : () => context.push('/comparisons/${Uri.encodeComponent(row['id'].toString())}'),
      ),
    )),
  );
}
