import 'package:flutter/foundation.dart';

import 'preview_mode_stub.dart'
    if (dart.library.js_interop) 'preview_mode_web.dart' as impl;

/// Global preview flag — read once at app startup.
///
/// Why the JS bridge?
///   Flutter's web URL strategy calls history.replaceState() during engine
///   bootstrap, which *drops the query string* before Dart's main() runs.
///   By the time we read Uri.base, ?preview=1 is already gone.
///
///   The fix: web/index.html captures ?preview=1 BEFORE Flutter loads, stores
///   it in sessionStorage, and exposes it as window.__FELAGI_PREVIEW__.
///   Dart reads that global here.
///
/// Usage:
///   https://example.com/test/?preview=1#/welcome
///   https://example.com/test/?preview=1#/profile
///
///   To disable: append ?preview=0 (clears the sessionStorage flag).
class PreviewMode {
  PreviewMode._();

  static bool _enabled = false;

  static bool get enabled => _enabled;

  /// Call once in `main()` before `runApp`.
  static void detectFromUrl() {
    if (!kIsWeb) {
      _enabled = false;
      return;
    }
    _enabled = impl.readPreviewFlag();
    debugPrint('[PreviewMode] enabled=$_enabled');
  }
}
