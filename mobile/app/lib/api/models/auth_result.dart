import 'user.dart';

/// Result of `/api/v1/auth/telegram/exchange`.
class AuthResult {
  const AuthResult({
    required this.accessToken,
    required this.tokenType,
    required this.user,
  });

  final String accessToken;
  final String tokenType; // "Bearer"
  final User user;

  factory AuthResult.fromJson(Map<String, dynamic> json) => AuthResult(
        accessToken: json['access_token'] as String,
        tokenType: (json['token_type'] as String?) ?? 'Bearer',
        user: User.fromJson(
          (json['user'] as Map).cast<String, dynamic>(),
        ),
      );
}

/// Result of `/api/v1/auth/telegram/start`.
class AuthStart {
  const AuthStart({
    required this.authUrl,
    required this.attemptId,
    required this.expiresAt,
  });

  final String authUrl;
  final String attemptId;
  final DateTime expiresAt;

  factory AuthStart.fromJson(Map<String, dynamic> json) => AuthStart(
        authUrl: json['auth_url'] as String,
        attemptId: json['attempt_id'] as String,
        expiresAt: DateTime.parse(json['expires_at'] as String),
      );
}
