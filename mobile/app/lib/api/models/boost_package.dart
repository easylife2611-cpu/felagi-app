/// BoostPackage — `GET /api/v1/boost-packages`.
class BoostPackage {
  const BoostPackage({
    required this.id,
    required this.durationDays,
    required this.price,
    required this.currency,
    this.active = true,
  });

  final String id;
  final int durationDays;
  final String price;
  final String currency;
  final bool active;

  String priceLabel() => '$price $currency';

  factory BoostPackage.fromJson(Map<String, dynamic> json) => BoostPackage(
        id: json['id'] as String,
        durationDays: (json['duration_days'] as int?) ?? 0,
        price: json['price']?.toString() ?? '0',
        currency: (json['currency'] as String?) ?? 'ETB',
        active: (json['active'] as bool?) ?? true,
      );
}

/// Payment summary — from `data.payment` in `POST /needs/{id}/boosts`.
class PaymentInfo {
  const PaymentInfo({
    required this.id,
    required this.status,
    required this.provider,
    required this.amount,
    required this.currency,
    this.checkoutUrl,
  });

  final String id;
  final String status; // PENDING | CONFIRMED | FAILED | REVIEW_REQUIRED
  final String provider;
  final String amount;
  final String currency;
  final String? checkoutUrl;

  bool get hasCheckout => (checkoutUrl ?? '').isNotEmpty;
  bool get isPending => status == 'PENDING';

  factory PaymentInfo.fromJson(Map<String, dynamic> json) => PaymentInfo(
        id: json['id'] as String,
        status: (json['status'] as String?) ?? 'PENDING',
        provider: (json['provider'] as String?) ?? '',
        amount: json['amount']?.toString() ?? '0',
        currency: (json['currency'] as String?) ?? 'ETB',
        checkoutUrl: json['checkout_url'] as String?,
      );
}

/// Result of `POST /needs/{id}/boosts` — L352 contract.
class BoostInitResult {
  const BoostInitResult({
    required this.boostId,
    required this.boostStatus,
    required this.payment,
  });

  final String boostId;
  final String boostStatus; // PENDING | ACTIVE
  final PaymentInfo? payment;

  factory BoostInitResult.fromJson(Map<String, dynamic> json) {
    final boost = (json['boost'] as Map?)?.cast<String, dynamic>();
    final paymentMap = (json['payment'] as Map?)?.cast<String, dynamic>();
    return BoostInitResult(
      boostId: (boost?['id'] as String?) ?? '',
      boostStatus: (boost?['status'] as String?) ?? 'PENDING',
      payment: paymentMap != null ? PaymentInfo.fromJson(paymentMap) : null,
    );
  }
}
