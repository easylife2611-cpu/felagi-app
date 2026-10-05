/// Generic paginated result — matches `BaseApiController::success(..., meta)`.
class Paginated<T> {
  const Paginated({
    required this.items,
    required this.page,
    required this.perPage,
    required this.total,
    required this.hasMore,
  });

  final List<T> items;
  final int page;
  final int perPage;
  final int total;
  final bool hasMore;

  /// Empty page.
  factory Paginated.empty() =>
      Paginated(items: const [], page: 1, perPage: 20, total: 0, hasMore: false);

  /// Build from raw envelope: `data: [...]` + `meta: {...}`.
  factory Paginated.fromEnvelope({
    required List<dynamic> data,
    required Map<String, dynamic>? meta,
    required T Function(Map<String, dynamic>) itemFromJson,
  }) {
    final list = data
        .map((e) => itemFromJson((e as Map).cast<String, dynamic>()))
        .toList(growable: false);
    final m = meta ?? const {};
    return Paginated(
      items: list,
      page: (m['page'] as int?) ?? 1,
      perPage: (m['per_page'] as int?) ?? list.length,
      total: (m['total'] as int?) ?? list.length,
      hasMore: (m['has_more'] as bool?) ?? false,
    );
  }
}
