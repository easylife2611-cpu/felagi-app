import 'api_client.dart';
import 'api_config.dart';
import 'models/boost_package.dart';

/// Boost/payment endpoints — L352 backend.
class BoostApi {
  BoostApi(this._client);

  final ApiClient _client;

  /// GET /boost-packages (public)
  Future<List<BoostPackage>> listPackages() async {
    final data = await _client.get(ApiConfig.boostPackages,
        authenticated: false);
    return (data as List)
        .map((e) => BoostPackage.fromJson((e as Map).cast<String, dynamic>()))
        .toList(growable: false);
  }

  /// POST /needs/{needId}/boosts
  ///
  /// Body: `{ package_id }`
  /// Headers: `Idempotency-Key` (opaque, generated client-side)
  /// Response: `{ boost: {...}, payment: {...} }`
  /// Errors: 409 BOOST_ACTIVE · 503 PAYMENTS_DISABLED
  Future<BoostInitResult> initiateBoost({
    required String needId,
    required String packageId,
    required String idempotencyKey,
  }) async {
    final envelope = await _client.postEnvelope(
      ApiConfig.needBoosts(needId),
      authenticated: true,
      headers: {'Idempotency-Key': idempotencyKey},
      body: {'package_id': packageId},
    );
    final data = (envelope[ApiConfig.kData] as Map).cast<String, dynamic>();
    return BoostInitResult.fromJson(data);
  }

  /// GET /payments/{id} (owner)
  Future<PaymentInfo> showPayment(String id) async {
    final data = await _client.get(ApiConfig.paymentShow(id));
    return PaymentInfo.fromJson((data as Map).cast<String, dynamic>());
  }
}
