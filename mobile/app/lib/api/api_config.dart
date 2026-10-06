/// Felagi API configuration.
///
/// Matches Laravel backend routes (api.php) + response envelope
/// from BaseApiController.
class ApiConfig {
  ApiConfig._();

  /// Base URL — overridable via --dart-define=API_BASE_URL
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'https://zagcreativity.com',
  );

  /// API prefix
  static const String apiPrefix = '/api/v1';

  /// Full API base (baseUrl + apiPrefix)
  static String get apiBase => '$baseUrl$apiPrefix';

  // ── Envelope keys ──
  static const String kSuccess = 'success';
  static const String kData = 'data';
  static const String kMessage = 'message';
  static const String kMeta = 'meta';
  static const String kError = 'error';
  static const String kErrorCode = 'code';
  static const String kErrorDetails = 'details';
  static const String kRequestId = 'request_id';

  // ── Auth endpoints ──
  static const String authTelegramStart = '/auth/telegram/start';
  static const String authTelegramExchange = '/auth/telegram/exchange';
  static const String authLogout = '/auth/logout';
  static const String authMe = '/auth/me';
  static const String authRefresh = '/auth/refresh';

  // ── Public endpoints ──
  static const String categoriesList = '/categories';
  static const String configShow = '/config';
  static const String needsList = '/needs';
  static String needShow(String id) => '/needs/$id';
  static String needCancel(String id) => '/needs/$id/cancel';
  static String needComplete(String id) => '/needs/$id/complete';

  // ── Boost (L352) ──
  static const String boostPackages = '/boost-packages';
  static String needBoosts(String needId) => '/needs/$needId/boosts';
  static String paymentShow(String id) => '/payments/$id';

  // ── My resources (auth) — L354 ──
  static const String myNeeds = '/my/needs';
  static const String myOffers = '/my/offers';

  // ── Offers (auth) — L354 ──
  static String needOffers(String needId) => '/needs/$needId/offers';
  static String offerShow(String id) => '/offers/$id';
  static String offerAccept(String id) => '/offers/$id/accept';
  static String offerReject(String id) => '/offers/$id/reject';
  static String offerWithdraw(String id) => '/offers/$id/withdraw';

  // ── Comparisons (auth) — L355 ──
  static String needComparisons(String needId) => '/needs/$needId/comparisons';

  // ── Profile ──
  static const String profileUpdate = '/profile';

  // ── Timeouts ──
  static const Duration defaultTimeout = Duration(seconds: 30);
  static const Duration uploadTimeout = Duration(seconds: 120);
}
