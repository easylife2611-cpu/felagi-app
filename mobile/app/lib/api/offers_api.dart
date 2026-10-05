import 'api_client.dart';
import 'api_config.dart';
import 'models/offer.dart';

/// Offers endpoints — see OfferController.php.
class OffersApi {
  OffersApi(this._client);

  final ApiClient _client;

  /// GET /needs/{needId}/offers (auth + owner only).
  ///
  /// Returns ALL offers for the need (backend does NOT paginate this).
  /// Response envelope: `{success: true, data: [Offer], message: "..."}`.
  Future<List<Offer>> listForNeed(String needId) async {
    final data = await _client.get(
      ApiConfig.needOffers(needId),
      authenticated: true,
    );
    return (data as List)
        .map((e) => Offer.fromJson((e as Map).cast<String, dynamic>()))
        .toList(growable: false);
  }

  /// GET /my/offers (auth) — paginated.
  ///
  /// Used by S013 (My Offers). Included here so the offers API is complete.
  /// Response envelope: `{success: true, data: [...], meta: {...}}`.
  Future<List<Offer>> listMyOffers({
    String? status,
    int page = 1,
    int perPage = 20,
  }) async {
    final envelope = await _client.getEnvelope(
      ApiConfig.myOffers,
      query: {
        if (status != null && status.isNotEmpty) 'status': status,
        'page': page.toString(),
        'per_page': perPage.toString(),
      },
    );
    final data = (envelope[ApiConfig.kData] as List?) ?? const [];
    return data
        .map((e) => Offer.fromJson((e as Map).cast<String, dynamic>()))
        .toList(growable: false);
  }

  /// POST /offers/{id}/accept (auth + owner) — S012+ action.
  Future<Offer> accept(String offerId) async {
    final data = await _client.post(ApiConfig.offerAccept(offerId));
    return Offer.fromJson((data as Map).cast<String, dynamic>());
  }

  /// POST /offers/{id}/reject (auth + owner) — S012+ action.
  Future<Offer> reject(String offerId) async {
    final data = await _client.post(ApiConfig.offerReject(offerId));
    return Offer.fromJson((data as Map).cast<String, dynamic>());
  }
}
