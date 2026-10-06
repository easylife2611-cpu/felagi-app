import 'api_client.dart';
import 'api_config.dart';
import 'models/auth_result.dart';
import 'models/user.dart';

/// Auth endpoints — see AuthController.php.
class AuthApi {
  AuthApi(this._client);

  final ApiClient _client;

  /// POST /auth/telegram/start
  ///
  /// Body: `return_uri` (required URL) + optional `scope`.
  /// Response: `{auth_url, attempt_id, expires_at}`.
  Future<AuthStart> startTelegram({
    required String returnUri,
    String? scope,
  }) async {
    final data = await _client.post(
      ApiConfig.authTelegramStart,
      authenticated: false,
      body: {
        'return_uri': returnUri,
        'scope': ?scope,
      },
    );
    return AuthStart.fromJson(
      (data as Map).cast<String, dynamic>(),
    );
  }


  /// POST /auth/telegram/widget/start — Login Widget flow.
  ///
  /// Body: `return_uri` (required URL).
  /// Response: `{bot_username, callback_url, attempt_id, expires_at}`.
  Future<Map<String, dynamic>> startTelegramWidget({
    required String returnUri,
  }) async {
    final data = await _client.post(
      ApiConfig.authTelegramWidgetStart,
      authenticated: false,
      body: {'return_uri': returnUri},
    );
    return (data as Map).cast<String, dynamic>();
  }

  /// POST /auth/telegram/exchange
  ///
  /// Body: `handoff_code` (required) + optional `device_name`.
  /// Response: `{access_token, token_type, user}`.
  Future<AuthResult> exchangeHandoff({
    required String handoffCode,
    String? deviceName,
  }) async {
    final data = await _client.post(
      ApiConfig.authTelegramExchange,
      authenticated: false,
      body: {
        'handoff_code': handoffCode,
        'device_name': ?deviceName,
      },
    );
    return AuthResult.fromJson(
      (data as Map).cast<String, dynamic>(),
    );
  }

  /// GET /auth/me — current authenticated user.
  Future<User> me() async {
    final data = await _client.get(ApiConfig.authMe);
    return User.fromJson(
      (data as Map).cast<String, dynamic>(),
    );
  }

  /// POST /auth/logout — 204 no content.
  Future<void> logout() async {
    await _client.post(ApiConfig.authLogout);
  }
}
