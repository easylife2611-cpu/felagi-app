/// Typed API exception — mirrors Laravel BaseApiController error envelope.
///
/// Server format:
///   {
///     "success": false,
///     "error": { "code": "...", "message": "...", "details": {...} },
///     "request_id": "..."
///   }
class ApiException implements Exception {
  ApiException({
    required this.code,
    required this.message,
    this.statusCode,
    this.details,
    this.requestId,
  });

  final String code;
  final String message;
  final int? statusCode;
  final Map<String, dynamic>? details;
  final String? requestId;

  // ── Canonical codes (from backend) ──
  static const String networkError = 'NETWORK_ERROR';
  static const String timeout = 'TIMEOUT';
  static const String unauthorized = 'UNAUTHORIZED';
  static const String forbidden = 'FORBIDDEN';
  static const String notFound = 'NOT_FOUND';
  static const String validationFailed = 'VALIDATION_FAILED';
  static const String stateConflict = 'STATE_CONFLICT';
  static const String idempotencyConflict = 'IDEMPOTENCY_CONFLICT';
  static const String boostActive = 'BOOST_ACTIVE';
  static const String paymentsDisabled = 'PAYMENTS_DISABLED';
  static const String rateLimited = 'RATE_LIMITED';
  static const String serverError = 'SERVER_ERROR';

  bool get isNetwork => code == networkError || code == timeout;
  bool get isAuth => statusCode == 401 || code == unauthorized;
  bool get isValidation => statusCode == 422 || code == validationFailed;
  bool get isConflict => statusCode == 409;
  bool get isRateLimited => statusCode == 429 || code == rateLimited;

  /// Should client retry the same request?
  bool get isRetryable => isNetwork || statusCode == 503;

  @override
  String toString() =>
      'ApiException($code @ $statusCode): $message'
      '${requestId != null ? ' [req=$requestId]' : ''}';
}
