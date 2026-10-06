import 'dart:convert';
import 'dart:math';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import '../api/api_config.dart';
import '../app_scope.dart';
import 'remote_collection.dart';

class MessagesScreen extends StatefulWidget {
  const MessagesScreen({super.key, required this.localeCode, required this.offerId});
  final String localeCode, offerId;
  @override
  State<MessagesScreen> createState() => _MessagesScreenState();
}
class _MessagesScreenState extends State<MessagesScreen> {
  final _text = TextEditingController();
  final _storage = const FlutterSecureStorage();
  Future<void> _writes = Future.value();
  String? _storageKey, _error;
  bool _sending = false, _restoring = true;
  int _revision = 0;
  String t(String key) => fgText(widget.localeCode, key);
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _restore());
  }
  Future<void> _restore() async {
    if (!mounted) return;
    final user = AppScope.of(context).authState.user;
    if (user == null) { setState(() { _error = t('signIn'); _restoring = false; }); return; }
    _storageKey = 'message_draft:${user.id}:${widget.offerId}';
    try {
      final stored = await _storage.read(key: _storageKey!);
      if (!mounted) return;
      if (stored != null) {
        final draft = jsonDecode(stored) as Map<String, dynamic>;
        _text.text = draft['text'] as String? ?? '';
        _sendKey = draft['sendKey'] as String?;
        _sendBody = draft['sendBody'] as String?;
        _sendAt = DateTime.tryParse(draft['sendAt'] as String? ?? '');
      }
    }
    catch (_) { if (mounted) setState(() => _error = t('draftSaveFailed')); }
    if (!mounted) return;
    _text.addListener(_save);
    setState(() => _restoring = false);
  }
  void _save() {
    final value = jsonEncode({'text': _text.text, 'sendKey': _sendKey,
      'sendBody': _sendBody, 'sendAt': _sendAt?.toIso8601String()});
    final key = _storageKey;
    if (key == null) return;
    _writes = _writes.then((_) => _storage.write(key: key, value: value)).catchError((Object _) {
      if (mounted) setState(() => _error = t('draftSaveFailed'));
    });
  }
  // The same content keeps its key through a failed response. The server's
  // idempotency middleware prevents immediate retries from duplicating a send.
  String? _sendKey, _sendBody;
  DateTime? _sendAt;
  Future<void> _send() async {
    if (_sending || _storageKey == null || _text.text.trim().isEmpty) return;
    final body = _text.text.trim();
    if (_sendBody == body && _sendAt != null &&
        DateTime.now().difference(_sendAt!) >= const Duration(hours: 23)) {
      setState(() => _error = t('messageUncertain'));
      return; // Do not reuse a server-expired key and accidentally recharge/send.
    }
    if (_sendBody != body) {
      _sendKey = base64Url.encode(List<int>.generate(24, (_) => Random.secure().nextInt(256)));
      _sendBody = body;
      _sendAt = DateTime.now();
    }
    setState(() { _sending = true; _error = null; });
    try {
      final client = AppScope.of(context).client;
      _save();
      await _writes;
      if (!mounted) return;
      await client.post(ApiConfig.offerMessages(widget.offerId),
        body: {'content': body}, headers: {'Idempotency-Key': _sendKey!});
      if (!mounted) return;
      _sendKey = null; _sendBody = null; _sendAt = null;
      _text.clear();
      setState(() => _revision++);
    } catch (_) { if (mounted) setState(() => _error = t('messageUncertain')); }
    finally { if (mounted) setState(() => _sending = false); }
  }
  @override
  void dispose() { _text.removeListener(_save); _text.dispose(); super.dispose(); }
  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(title: Text(t('screenS017'))),
    body: SafeArea(child: Column(children: [
      Expanded(child: RemoteCollection(key: ValueKey(_revision), path: ApiConfig.offerMessages(widget.offerId),
        localeCode: widget.localeCode, paginated: true,
        row: (context, row, reload) => ListTile(
          title: Text(row['content']?.toString() ?? t('unknown')),
          subtitle: Text('${row['sender']?['full_name'] ?? t('unknown')} • ${row['created_at'] ?? t('unknown')}'),
        ),
      )),
      Padding(padding: const EdgeInsets.all(FgTokens.space4), child: Column(children: [
        if (_error != null) Text(_error!),
        TextField(controller: _text, enabled: !_restoring && !_sending && _storageKey != null,
          maxLength: 5000, minLines: 1, maxLines: 4, decoration: InputDecoration(labelText: t('messageBody'))),
        FgButton(label: t('sendMessage'), isLoading: _sending,
          onPressed: _restoring || _sending || _storageKey == null ? null : _send),
      ])),
    ])),
  );
}
