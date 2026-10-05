/// Felagi user model — server-authoritative fields only.
///
/// Matches `/api/v1/auth/me` + `/api/v1/auth/telegram/exchange` responses.
class User {
  const User({
    required this.id,
    required this.fullName,
    this.telegramSubject,
    this.profilePhotoUrl,
    required this.status,
    this.ratingScore,
    this.ratingCount,
    this.roles = const [],
  });

  final String id;
  final String fullName;
  final String? telegramSubject;
  final String? profilePhotoUrl;
  final String status; // ACTIVE | SUSPENDED | BANNED
  final num? ratingScore;
  final int? ratingCount;
  final List<String> roles;

  bool get isActive => status == 'ACTIVE';
  bool get hasPhoto => (profilePhotoUrl ?? '').isNotEmpty;

  factory User.fromJson(Map<String, dynamic> json) => User(
        id: json['id'] as String,
        fullName: (json['full_name'] as String?) ?? '',
        telegramSubject: json['telegram_subject'] as String?,
        profilePhotoUrl: json['profile_photo_url'] as String?,
        status: (json['status'] as String?) ?? 'ACTIVE',
        ratingScore: json['rating_score'] as num?,
        ratingCount: json['rating_count'] as int?,
        roles: (json['roles'] as List?)?.cast<String>() ?? const [],
      );
}
