/// Felagi Offer — mirrors `OfferController@index|myOffers` response.
///
/// Backend fields (app/Models/Offer.php):
///   id, need_id, provider_id, offered_price, currency,
///   proposal_message, delivery_time_text, availability_text,
///   additional_notes, status, version, accepted_at, withdrawn_at,
///   created_at, updated_at
///
/// Relations eager-loaded:
///   - index(): provider{id, full_name, profile_photo_url,
///                       rating_score, rating_count}
///   - myOffers(): need{id, title, status, requester_id}
class Offer {
  const Offer({
    required this.id,
    required this.needId,
    required this.providerId,
    required this.offeredPrice,
    required this.currency,
    required this.status,
    this.proposalMessage,
    this.deliveryTimeText,
    this.availabilityText,
    this.additionalNotes,
    this.version = 1,
    this.acceptedAt,
    this.withdrawnAt,
    this.createdAt,
    // Provider (from index endpoint)
    this.providerName,
    this.providerPhotoUrl,
    this.providerRating,
    this.providerRatingCount,
    // Need (from myOffers endpoint)
    this.needTitle,
    this.needStatus,
  });

  final String id;
  final String needId;
  final String providerId;
  final String offeredPrice; // decimal as string (e.g. "1500.00")
  final String currency;
  final String status; // PENDING | ACCEPTED | REJECTED | WITHDRAWN
  final String? proposalMessage;
  final String? deliveryTimeText;
  final String? availabilityText;
  final String? additionalNotes;
  final int version;
  final DateTime? acceptedAt;
  final DateTime? withdrawnAt;
  final DateTime? createdAt;

  // Provider summary (nullable — only present when backend includes it)
  final String? providerName;
  final String? providerPhotoUrl;
  final double? providerRating;
  final int? providerRatingCount;

  // Need summary (nullable — only present in myOffers response)
  final String? needTitle;
  final String? needStatus;

  // ── Status helpers ──
  bool get isPending => status == 'PENDING';
  bool get isAccepted => status == 'ACCEPTED';
  bool get isRejected => status == 'REJECTED';
  bool get isWithdrawn => status == 'WITHDRAWN';

  /// "1500.00 ETB"
  String priceLabel() => '$offeredPrice $currency';

  factory Offer.fromJson(Map<String, dynamic> json) {
    final provider = json['provider'] as Map?;
    final need = json['need'] as Map?;

    return Offer(
      id: json['id'] as String,
      needId: (json['need_id'] as String?) ?? '',
      providerId: (json['provider_id'] as String?) ?? '',
      offeredPrice: json['offered_price']?.toString() ?? '0',
      currency: (json['currency'] as String?) ?? 'ETB',
      status: (json['status'] as String?) ?? 'PENDING',
      proposalMessage: json['proposal_message'] as String?,
      deliveryTimeText: json['delivery_time_text'] as String?,
      availabilityText: json['availability_text'] as String?,
      additionalNotes: json['additional_notes'] as String?,
      version: (json['version'] as int?) ?? 1,
      acceptedAt: json['accepted_at'] != null
          ? DateTime.tryParse(json['accepted_at'] as String)
          : null,
      withdrawnAt: json['withdrawn_at'] != null
          ? DateTime.tryParse(json['withdrawn_at'] as String)
          : null,
      createdAt: json['created_at'] != null
          ? DateTime.tryParse(json['created_at'] as String)
          : null,
      providerName: provider?['full_name'] as String?,
      providerPhotoUrl: provider?['profile_photo_url'] as String?,
      providerRating: provider?['rating_score'] != null
          ? double.tryParse(provider!['rating_score'].toString())
          : null,
      providerRatingCount: provider?['rating_count'] as int?,
      needTitle: need?['title'] as String?,
      needStatus: need?['status'] as String?,
    );
  }
}
