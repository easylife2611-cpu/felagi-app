import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import '../api/api_config.dart';
import '../app_scope.dart';
import 'remote_collection.dart';

class NotificationsScreen extends StatelessWidget {
  const NotificationsScreen({super.key, required this.localeCode});
  final String localeCode;
  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(title: Text(fgText(localeCode, 'screenS018'))),
    body: SafeArea(child: RemoteCollection(path: ApiConfig.notifications, localeCode: localeCode, paginated: true,
      row: (context, row, reload) => ListTile(
        title: Text(row['title']?.toString() ?? fgText(localeCode, 'unknown')),
        subtitle: Text(row['body']?.toString() ?? fgText(localeCode, 'unknown')),
        trailing: row['read_at'] != null ? null : IconButton(
          tooltip: fgText(localeCode, 'markRead'), icon: const Icon(Icons.mark_email_read_outlined),
          onPressed: row['id'] == null ? null : () async {
            try {
              await AppScope.of(context).client.post(ApiConfig.notificationRead(row['id'].toString()));
              if (context.mounted) reload();
            } catch (_) {
              if (context.mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(fgText(localeCode, 'loadRecovery'))));
            }
          },
        ),
      ),
    )),
  );
}
