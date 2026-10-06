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

  // ═══════════════════════════════════════════════════════════
  //  L355 — S011 Submit, S012 Detail, S013 Withdraw
  // ═══════════════════════════════════════════════════════════

  /// POST /needs/{needId}/offers — S011 (submit offer).
  Future<Offer> store(
    String needId, {
    required String offeredPrice,
    String? proposalMessage,
    String? deliveryTimeText,
    String? availabilityText,
    String? additionalNotes,
  }) async {
    final body = <String, dynamic>{
      'offered_price': offeredPrice,
      if (proposalMessage != null && proposalMessage.isNotEmpty)
        'proposal_message': proposalMessage,
      if (deliveryTimeText != null && deliveryTimeText.isNotEmpty)
        'delivery_time_text': deliveryTimeText,
      if (availabilityText != null && availabilityText.isNotEmpty)
        'availability_text': availabilityText,
      if (additionalNotes != null && additionalNotes.isNotEmpty)
        'additional_notes': additionalNotes,
    };
    final data = await _client.post(
      ApiConfig.needOffers(needId),
      body: body,
    );
    return Offer.fromJson((data as Map).cast<String, dynamic>());
  }

  /// GET /offers/{id} — S012 (offer detail).
  Future<Offer> show(String offerId) async {
    final data = await _client.get(
      ApiConfig.offerShow(offerId),
      authenticated: true,
    );
    return Offer.fromJson((data as Map).cast<String, dynamic>());
  }

  /// PATCH /offers/{id} — S011 (edit offer).
  Future<Offer> update(
    String offerId, {
    required String offeredPrice,
    String? proposalMessage,
    String? deliveryTimeText,
    String? availabilityText,
    String? additionalNotes,
  }) async {
    final body = <String, dynamic>{
      'offered_price': offeredPrice,
      if (proposalMessage != null && proposalMessage.isNotEmpty)
        'proposal_message': proposalMessage,
      if (deliveryTimeText != null && deliveryTimeText.isNotEmpty)
        'delivery_time_text': deliveryTimeText,
      if (availabilityText != null && availabilityText.isNotEmpty)
        'availability_text': availabilityText,
      if (additionalNotes != null && additionalNotes.isNotEmpty)
        'additional_notes': additionalNotes,
    };
    final data = await _client.put(
      ApiConfig.offerShow(offerId),
      body: body,
    );
    return Offer.fromJson((data as Map).cast<String, dynamic>());
  }

  /// POST /offers/{id}/withdraw — S013 (withdraw my offer).
  Future<Offer> withdraw(String offerId) async {
    final data = await _client.post(ApiConfig.offerWithdraw(offerId));
    return Offer.fromJson((data as Map).cast<String, dynamic>());
  }
}
