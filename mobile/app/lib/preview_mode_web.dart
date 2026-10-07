import 'dart:js_interop';

@JS('__FELAGI_PREVIEW__')
external JSBoolean? get _jsPreviewFlag;

bool readPreviewFlag() {
  try {
    final v = _jsPreviewFlag;
    return v?.toDart ?? false;
  } catch (_) {
    return false;
  }
}
