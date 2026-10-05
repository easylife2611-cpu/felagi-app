import 'category.dart';
import 'user.dart';

/// Felagi Need — mirrors `NeedController@index|show` response.
class Need {
  const Need({
    required this.id,
    required this.title,
    required this.description,
    required this.status,
    required this.currency,
    this.requesterId,
    this.categoryId,
    this.category,
    this.requester,
    this.locationText,
    this.budgetMin,
    this.budgetMax,
    this.quantity,
    this.deadlineAt,
    this.offerDeadlineAt,
    this.offerCount = 0,
    this.isOwner = false,
    this.version = 1,
    this.createdAt,
  });

  final String id;
  final String title;
  final String description;
  final String status; // OPEN | IN_PROGRESS | COMPLETED | CANCELLED
  final String currency;
  final String? requesterId;
  final String? categoryId;
  final Category? category;
  final User? requester;
  final String? locationText;
  final String? budgetMin;
  final String? budgetMax;
  final String? quantity;
  final DateTime? deadlineAt;
  final DateTime? offerDeadlineAt;
  final int offerCount;
  final bool isOwner;
  final int version;
  final DateTime? createdAt;

  bool get isOpen => status == 'OPEN';
  bool get hasBudget => (budgetMin ?? '').isNotEmpty || (budgetMax ?? '').isNotEmpty;

  /// Display budget string (uses min–max range if both exist).
  String budgetLabel() {
    final min = budgetMin;
    final max = budgetMax;
    if (min != null && max != null && min.isNotEmpty && max.isNotEmpty) {
      return '$min–$max $currency';
    }
    if (max != null && max.isNotEmpty) return '≤ $max $currency';
    if (min != null && min.isNotEmpty) return '≥ $min $currency';
    return '—';
  }

  /// Days until deadline (null if no deadline or in the past).
  int? daysUntilDeadline() {
    final d = deadlineAt;
    if (d == null) return null;
    final diff = d.difference(DateTime.now()).inDays;
    return diff < 0 ? null : diff;
  }

  factory Need.fromJson(Map<String, dynamic> json) => Need(
        id: json['id'] as String,
        title: (json['title'] as String?) ?? '',
        description: (json['description'] as String?) ?? '',
        status: (json['status'] as String?) ?? 'OPEN',
        currency: (json['currency'] as String?) ?? 'ETB',
        requesterId: json['requester_id'] as String?,
        categoryId: json['category_id'] as String?,
        category: json['category'] != null
            ? Category.fromJson((json['category'] as Map).cast<String, dynamic>())
            : null,
        requester: json['requester'] != null
            ? User.fromJson((json['requester'] as Map).cast<String, dynamic>())
            : null,
        locationText: json['location_text'] as String?,
        budgetMin: json['budget_min']?.toString(),
        budgetMax: json['budget_max']?.toString(),
        quantity: json['quantity']?.toString(),
        deadlineAt: json['deadline_at'] != null
            ? DateTime.tryParse(json['deadline_at'] as String)
            : null,
        offerDeadlineAt: json['offer_deadline_at'] != null
            ? DateTime.tryParse(json['offer_deadline_at'] as String)
            : null,
        offerCount: (json['offer_count'] as int?) ?? 0,
        isOwner: (json['is_owner'] as bool?) ?? false,
        version: (json['version'] as int?) ?? 1,
        createdAt: json['created_at'] != null
            ? DateTime.tryParse(json['created_at'] as String)
            : null,
      );
}
