import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

import '../api/api_exception.dart';
import '../api/models/category.dart';
import '../app_scope.dart';

/// S005 — Create / Edit Need (real API).
///
/// Create: POST /api/v1/needs (+ Idempotency-Key) → 201 data (new Need)
/// Edit:   PUT /api/v1/needs/{id} (+ If-Match)
class CreateEditNeedScreen extends StatefulWidget {
  const CreateEditNeedScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    this.needId,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;
  final String? needId;

  @override
  State<CreateEditNeedScreen> createState() => _CreateEditNeedScreenState();
}

enum _Phase { loading, ready, saving, error }

class _CreateEditNeedScreenState extends State<CreateEditNeedScreen> {
  final _titleCtrl = TextEditingController();
  final _descCtrl = TextEditingController();
  final _budgetCtrl = TextEditingController();
  final _locationCtrl = TextEditingController();

  List<Category> _categories = const [];
  String? _categoryId;
  bool _acknowledged = false;
  bool _saving = false;
  _Phase _phase = _Phase.loading;
  String? _error;

  /// Generated once per screen instance — reused on retry.
  late final String _idemKey;

  String _t(String key) => fgText(widget.localeCode, key);

  bool get _isEdit => widget.needId != null;

  bool get _canSave =>
      _phase != _Phase.loading &&
      _phase != _Phase.saving &&
      _titleCtrl.text.trim().length >= 5 &&
      _descCtrl.text.trim().length >= 20 &&
      _categoryId != null &&
      (_isEdit || _acknowledged);

  @override
  void initState() {
    super.initState();
    _idemKey =
        'need-${widget.needId ?? "new"}-${DateTime.now().microsecondsSinceEpoch}';
    WidgetsBinding.instance.addPostFrameCallback((_) => _loadInitial());
  }

  @override
  void dispose() {
    _titleCtrl.dispose();
    _descCtrl.dispose();
    _budgetCtrl.dispose();
    _locationCtrl.dispose();
    super.dispose();
  }

  Future<void> _loadInitial() async {
    if (!mounted) return;
    setState(() {
      _phase = _Phase.loading;
      _error = null;
    });

    try {
      final scope = AppScope.of(context);

      // Always load categories (form needs them)
      final cats = await scope.needsApi.listCategories();

      // If editing, also load the Need
      if (_isEdit) {
        final need = await scope.needsApi.showNeed(widget.needId!);
        _titleCtrl.text = need.title;
        _descCtrl.text = need.description;
        _categoryId = need.categoryId;
        _locationCtrl.text = need.locationText ?? '';
        _budgetCtrl.text = need.budgetMax ?? need.budgetMin ?? '';
        _acknowledged = true; // already consented at create
      }

      if (!mounted) return;
      setState(() {
        _categories = cats.where((c) => c.active).toList(growable: false);
        _phase = _Phase.ready;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _error = e.message;
        _phase = _Phase.error;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = '$e';
        _phase = _Phase.error;
      });
    }
  }

  Future<void> _save() async {
    if (!_canSave || _categoryId == null) return;
    setState(() {
      _saving = true;
      _error = null;
    });

    try {
      final scope = AppScope.of(context);
      final budget = _budgetCtrl.text.trim();
      final location = _locationCtrl.text.trim();

      final result = _isEdit
          ? await scope.needsApi.updateNeed(
              id: widget.needId!,
              version: 1, // TODO: track version from GET
              title: _titleCtrl.text.trim(),
              description: _descCtrl.text.trim(),
              categoryId: _categoryId,
              locationText: location.isEmpty ? null : location,
              budgetMax: budget.isEmpty ? null : budget,
            )
          : await scope.needsApi.createNeed(
              title: _titleCtrl.text.trim(),
              description: _descCtrl.text.trim(),
              categoryId: _categoryId!,
              telegramPublicationAcknowledged: _acknowledged,
              idempotencyKey: _idemKey,
              locationText: location.isEmpty ? null : location,
              budgetMax: budget.isEmpty ? null : budget,
            );

      if (!mounted) return;
      setState(() => _saving = false);

      // Navigate to detail (or pop if no router available in tests)
      final router = GoRouter.maybeOf(context);
      if (router != null) {
        context.go('/needs/${result.id}');
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(_t('needSuccessNext'))),
        );
      }
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _saving = false;
        _error = e.message;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _saving = false;
        _error = '$e';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      appBar: AppBar(
        title: Text(_t('screenS005')),
        leading: Navigator.of(context).canPop()
            ? IconButton(
                icon: const Icon(Icons.arrow_back),
                onPressed: () => Navigator.of(context).pop(),
              )
            : null,
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
            constraints: const BoxConstraints(maxWidth: 720),
            child: _buildBody(theme),
          ),
        ),
      ),
    );
  }

  Widget _buildBody(ThemeData theme) {
    switch (_phase) {
      case _Phase.loading:
        return const Center(
          child: Padding(
            padding: EdgeInsets.all(FgTokens.space6),
            child: CircularProgressIndicator(),
          ),
        );
      case _Phase.error:
        return Padding(
          padding: const EdgeInsets.all(FgTokens.space4),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              FgStatePanel(
                label: _t('stateError'),
                message: _error ?? _t('unknown'),
                kind: FgStatusKind.error,
              ),
              const SizedBox(height: FgTokens.space3),
              FgButton(label: _t('retry'), onPressed: _loadInitial),
            ],
          ),
        );
      case _Phase.ready:
      case _Phase.saving:
        return _form(theme);
    }
  }

  Widget _form(ThemeData theme) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(FgTokens.space4),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          FgStatusBadge(
            label: _isEdit ? _t('stateDraft') : _t('stateReady'),
            kind: FgStatusKind.info,
          ),
          const SizedBox(height: FgTokens.space4),

          FgTextField(
            label: '${_t('fieldTitle')} *',
            controller: _titleCtrl,
            onChanged: (_) => setState(() {}),
          ),
          const SizedBox(height: FgTokens.space3),

          FgTextField(
            label: '${_t('fieldDescription')} *',
            controller: _descCtrl,
            maxLines: 4,
            onChanged: (_) => setState(() {}),
          ),
          const SizedBox(height: FgTokens.space3),

          Text('${_t('fieldCategoryId')} *',
              style: theme.textTheme.bodyMedium),
          const SizedBox(height: FgTokens.space2),
          Wrap(
            spacing: FgTokens.space2,
            runSpacing: FgTokens.space2,
            children: _categories
                .map((c) => ChoiceChip(
                      label: Text(c.name(widget.localeCode)),
                      selected: _categoryId == c.id,
                      onSelected: (sel) => setState(
                          () => _categoryId = sel ? c.id : null),
                    ))
                .toList(),
          ),
          const SizedBox(height: FgTokens.space3),

          FgTextField(
            label: _t('fieldBudgetMax'),
            controller: _budgetCtrl,
          ),
          const SizedBox(height: FgTokens.space3),

          FgTextField(
            label: _t('fieldLocationText'),
            controller: _locationCtrl,
          ),
          const SizedBox(height: FgTokens.space4),

          if (!_isEdit)
            CheckboxListTile(
              value: _acknowledged,
              onChanged: (v) => setState(() => _acknowledged = v ?? false),
              title: Text(_t('requiredConsent')),
              subtitle: Text(_t('publicHelp')),
              controlAffinity: ListTileControlAffinity.leading,
              contentPadding: EdgeInsets.zero,
            ),
          const SizedBox(height: FgTokens.space4),

          FgStatePanel(
            label: _t('statePreview'),
            message: _t('publicLinkPending'),
            kind: FgStatusKind.info,
          ),
          const SizedBox(height: FgTokens.space4),

          if (_error != null) ...[
            FgStatePanel(
              label: _t('stateError'),
              message: _error!,
              kind: FgStatusKind.error,
            ),
            const SizedBox(height: FgTokens.space3),
          ],

          FgButton(
            label: _t('save'),
            isLoading: _saving,
            onPressed: _canSave ? _save : null,
          ),
          const SizedBox(height: FgTokens.space3),

          FgButton(
            label: _t('cancel'),
            variant: FgButtonVariant.secondary,
            onPressed: () => Navigator.of(context).maybePop(),
          ),
        ],
      ),
    );
  }
}
