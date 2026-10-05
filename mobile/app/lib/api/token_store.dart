import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// Secure token storage for Sanctum bearer tokens.
///
/// Mobile: Keychain (iOS) / EncryptedSharedPreferences (Android)
/// Web: WebCrypto (requires HTTPS)
class TokenStore {
  TokenStore({FlutterSecureStorage? storage})
      : _storage = storage ??
            const FlutterSecureStorage(
              aOptions: AndroidOptions(encryptedSharedPreferences: true),
            );

  final FlutterSecureStorage _storage;

  static const _kToken = 'felagi_token';
  static const _kUserId = 'felagi_user_id';

  /// Read the stored bearer token (null if not authenticated).
  Future<String?> readToken() async {
    try {
      return await _storage.read(key: _kToken);
    } catch (_) {
      return null;
    }
  }

  /// Read the stored user id (for cache scoping).
  Future<String?> readUserId() async {
    try {
      return await _storage.read(key: _kUserId);
    } catch (_) {
      return null;
    }
  }

  /// Save an access token + user id.
  Future<void> save({
    required String token,
    required String userId,
  }) async {
    await _storage.write(key: _kToken, value: token);
    await _storage.write(key: _kUserId, value: userId);
  }

  /// Clear all stored credentials (on logout / 401).
  Future<void> clear() async {
    await _storage.delete(key: _kToken);
    await _storage.delete(key: _kUserId);
  }

  /// Has a token?
  Future<bool> get hasToken async => (await readToken())?.isNotEmpty ?? false;
}
