import 'api_client.dart';
import 'api_config.dart';
import 'models/category.dart';
import 'models/need.dart';
import 'models/paginated.dart';

/// Needs + categories endpoints — see NeedController.php.
class NeedsApi {
  NeedsApi(this._client);

  final ApiClient _client;

  /// GET /categories
  Future<List<Category>> listCategories() async {
    final data = await _client.get('/categories', authenticated: false);
    return (data as List)
        .map((e) => Category.fromJson((e as Map).cast<String, dynamic>()))
        .toList(growable: false);
  }

  /// GET /needs
  ///
  /// Query: keyword, category_id, location, page, per_page, sort.
  Future<Paginated<Need>> listNeeds({
    String? keyword,
    String? categoryId,
    String? location,
    int page = 1,
    int perPage = 20,
    String sort = 'newest',
  }) async {
    final envelope = await _client.getEnvelope(
      ApiConfig.needsList,
      authenticated: false,
      query: {
        if (keyword != null && keyword.isNotEmpty) 'keyword': keyword,
        if (categoryId != null && categoryId.isNotEmpty) 'category_id': categoryId,
        if (location != null && location.isNotEmpty) 'location': location,
        'page': page.toString(),
        'per_page': perPage.toString(),
        'sort': sort,
      },
    );

    final data = (envelope[ApiConfig.kData] as List?) ?? const [];
    final meta = (envelope[ApiConfig.kMeta] as Map?)?.cast<String, dynamic>();

    return Paginated<Need>.fromEnvelope(
      data: data,
      meta: meta,
      itemFromJson: Need.fromJson,
    );
  }

  /// GET /my/needs (auth) — paginated list of needs owned by current user.
  ///
  /// Query: status (optional), page, per_page.
  /// Response: {data: [Need], meta: {page, per_page, total}}.
  Future<Paginated<Need>> listMyNeeds({
    String? status,
    int page = 1,
    int perPage = 20,
  }) async {
    final envelope = await _client.getEnvelope(
      ApiConfig.myNeeds,
      query: {
        if (status != null && status.isNotEmpty) 'status': status,
        'page': page.toString(),
        'per_page': perPage.toString(),
      },
    );
    final data = (envelope[ApiConfig.kData] as List?) ?? const [];
    final meta = (envelope[ApiConfig.kMeta] as Map?)?.cast<String, dynamic>();
    return Paginated<Need>.fromEnvelope(
      data: data,
      meta: meta,
      itemFromJson: Need.fromJson,
    );
  }

  /// GET /needs/{id}
  Future<Need> showNeed(String id) async {
    final data = await _client.get(
      ApiConfig.needShow(id),
      authenticated: false,
    );
    return Need.fromJson((data as Map).cast<String, dynamic>());
  }

  /// POST /needs/{id}/cancel (auth required)
  Future<Need> cancelNeed(String id) async {
    final data = await _client.post(ApiConfig.needCancel(id));
    return Need.fromJson((data as Map).cast<String, dynamic>());
  }

  /// POST /needs/{id}/complete (auth required)
  Future<Need> completeNeed(String id) async {
    final data = await _client.post(ApiConfig.needComplete(id));
    return Need.fromJson((data as Map).cast<String, dynamic>());
  }

  /// POST /needs (auth) — create new Need.
  ///
  /// Body: title, description, category_id, telegram_publication_acknowledged
  ///       + optional location_text / budget_min / budget_max / currency.
  /// Header: Idempotency-Key (opaque, client-generated).
  Future<Need> createNeed({
    required String title,
    required String description,
    required String categoryId,
    required bool telegramPublicationAcknowledged,
    required String idempotencyKey,
    String? locationText,
    String? budgetMin,
    String? budgetMax,
    String currency = 'ETB',
    String? quantity,
    DateTime? deadlineAt,
    DateTime? offerDeadlineAt,
  }) async {
    final envelope = await _client.postEnvelope(
      ApiConfig.needsList,
      headers: {'Idempotency-Key': idempotencyKey},
      body: {
        'title': title,
        'description': description,
        'category_id': categoryId,
        'telegram_publication_acknowledged': telegramPublicationAcknowledged,
        'currency': currency,
        'location_text': ?locationText,
        'budget_min': ?budgetMin,
        'budget_max': ?budgetMax,
        'quantity': ?quantity,
        'deadline_at': ?deadlineAt?.toIso8601String(),
        'offer_deadline_at': ?offerDeadlineAt?.toIso8601String(),
      },
    );
    final data = (envelope[ApiConfig.kData] as Map).cast<String, dynamic>();
    return Need.fromJson(data);
  }

  /// PUT /needs/{id} (auth) — update existing Need.
  ///
  /// Header: If-Match: version (for optimistic locking).
  Future<Need> updateNeed({
    required String id,
    required int version,
    String? title,
    String? description,
    String? categoryId,
    String? locationText,
    String? budgetMin,
    String? budgetMax,
    String? currency,
  }) async {
    final envelope = await _client.putEnvelope(
      ApiConfig.needShow(id),
      headers: {'If-Match': version.toString()},
      body: {
        'title': ?title,
        'description': ?description,
        'category_id': ?categoryId,
        'location_text': ?locationText,
        'budget_min': ?budgetMin,
        'budget_max': ?budgetMax,
        'currency': ?currency,
      },
    );
    final data = (envelope[ApiConfig.kData] as Map).cast<String, dynamic>();
    return Need.fromJson(data);
  }
}
