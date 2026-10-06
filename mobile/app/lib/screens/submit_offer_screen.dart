import 'package:flutter/material.dart';
import 'package:felagi_design_system/felagi_design_system.dart';
import 'package:go_router/go_router.dart';

import '../api/api_exception.dart';
import '../app_scope.dart';

/// S011 — Submit Offer (create offer).
class SubmitOfferScreen extends StatefulWidget {
  const SubmitOfferScreen({
    super.key,
    required this.localeCode,
    required this.onLocaleChange,
    required this.needId,
  });

  final String localeCode;
  final ValueChanged<String> onLocaleChange;
  final String needId;

  @override
  State<SubmitOfferScreen> createState() => _SubmitOfferScreenState();
}

class _SubmitOfferScreenState extends State<SubmitOfferScreen> {
  final _priceCtrl = TextEditingController();
  final _proposalCtrl = TextEditingController();
  final _deliveryCtrl = TextEditingController();
  final _availabilityCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();

  bool _submitting = false;
  String? _error;

  String _t(String key) => fgText(widget.localeCode, key);

  @override
  void dispose() {
    _priceCtrl.dispose();
    _proposalCtrl.dispose();
    _deliveryCtrl.dispose();
    _availabilityCtrl.dispose();
    _notesCtrl.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    final price = _priceCtrl.text.trim();
    if (price.isEmpty) return;

    setState(() {
      _submitting = true;
      _error = null;
    });

    try {
      final api = AppScope.of(context).offersApi;
      final offer = await api.store(
        widget.needId,
        offeredPrice: price,
        proposalMessage: _proposalCtrl.text.trim(),
        deliveryTimeText: _deliveryCtrl.text.trim(),
        availabilityText: _availabilityCtrl.text.trim(),
        additionalNotes: _notesCtrl.text.trim(),
      );
      if (!mounted) return;
      context.pushReplacement('/offers/${offer.id}');
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _error = e.message;
        _submitting = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = '$e';
        _submitting = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(_t('screenS011'))),
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 720),
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(FgTokens.space4),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  FgTextField(label: 'Offered price *', controller: _priceCtrl),
                  const SizedBox(height: FgTokens.space3),
                  FgTextField(label: 'Proposal message', controller: _proposalCtrl),
                  const SizedBox(height: FgTokens.space3),
                  FgTextField(label: 'Delivery time', controller: _deliveryCtrl),
                  const SizedBox(height: FgTokens.space3),
                  FgTextField(label: 'Availability', controller: _availabilityCtrl),
                  const SizedBox(height: FgTokens.space3),
                  FgTextField(label: 'Additional notes', controller: _notesCtrl),
                  if (_error != null) ...[
                    const SizedBox(height: FgTokens.space4),
                    FgStatePanel(
                      label: _t('stateError'),
                      message: _error!,
                      kind: FgStatusKind.error,
                    ),
                  ],
                  const SizedBox(height: FgTokens.space6),
                  FgButton(
                    label: _t('save'),
                    isLoading: _submitting,
                    onPressed: _submitting ? null : _submit,
                  ),
                  const SizedBox(height: FgTokens.space3),
                  FgButton(
                    label: _t('cancel'),
                    variant: FgButtonVariant.secondary,
                    onPressed: _submitting ? null : () => context.pop(),
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
