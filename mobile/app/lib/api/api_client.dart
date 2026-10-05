import 'dart:async';
import 'dart:convert';

import 'package:http/http.dart' as http;

import 'api_config.dart';
import 'api_exception.dart';
import 'token_store.dart';

/// HTTP client for the Felagi Laravel API.
///
/// Handles:
///   - Bearer token injection (Sanctum)
///   - Response envelope unwrapping (`data` field)
///   - Error envelope parsing → ApiException
///   - Timeouts + network errors
///   - 401 → clears token
class ApiClient {
  ApiClient({http.Client? httpClient, TokenStore? tokenStore})
      : _http = httpClient ?? http.Client(),
        _tokens = tokenStore ?? TokenStore();

  final http.Client _http;
  final TokenStore _tokens;

  /// Expose the token store (used by AuthState).
  TokenStore get tokenStore => _tokens;

  // ─────────────────────────────────────────
  // Public helpers
  // ─────────────────────────────────────────

  /// GET that returns the FULL envelope `{data, meta, ...}` — for pagination.
  Future<Map<String, dynamic>> getEnvelope(
    String path, {
    Map<String, String>? query,
    bool authenticated = true,
  }) async {
    var url = Uri.parse('${ApiConfig.apiBase}$path');
    if (query != null && query.isNotEmpty) {
      url = url.replace(queryParameters: query);
    }

    final req = http.Request('GET', url);
    req.headers['Accept'] = 'application/json';

    if (authenticated) {
      final token = await _tokens.readToken();
      if (token != null && token.isNotEmpty) {
        req.headers['Authorization'] = 'Bearer $token';
      }
    }

    return _raw(req);
  }

  /// GET request — returns `data` from the envelope.
  Future<dynamic> get(
    String path, {
    Map<String, String>? query,
    bool authenticated = true,
  }) =>
      _send('GET', path, query: query, authenticated: authenticated);

  /// POST request — returns `data` from the envelope.
  Future<dynamic> post(
    String path, {
    Object? body,
    Map<String, String>? headers,
    bool authenticated = true,
  }) =>
      _send('POST', path,
          body: body, headers: headers, authenticated: authenticated);

  /// PUT request — returns `data` from the envelope.
  Future<dynamic> put(
    String path, {
    Object? body,
    Map<String, String>? headers,
    bool authenticated = true,
  }) =>
      _send('PUT', path,
          body: body, headers: headers, authenticated: authenticated);

  /// DELETE request — returns `data` from the envelope.
  Future<dynamic> delete(
    String path, {
    Map<String, String>? headers,
    bool authenticated = true,
  }) =>
      _send('DELETE', path, headers: headers, authenticated: authenticated);

  /// POST that returns the FULL envelope `{data, meta, ...}`.
  Future<Map<String, dynamic>> postEnvelope(
    String path, {
    Object? body,
    Map<String, String>? headers,
    bool authenticated = true,
  }) async {
    final url = Uri.parse('${ApiConfig.apiBase}$path');
    final req = http.Request('POST', url);
    req.headers.addAll({
      'Accept': 'application/json',
      if (body != null) 'Content-Type': 'application/json',
      ...?headers,
    });

    if (authenticated) {
      final token = await _tokens.readToken();
      if (token != null && token.isNotEmpty) {
        req.headers['Authorization'] = 'Bearer $token';
      }
    }

    if (body != null) req.body = jsonEncode(body);
    return _raw(req);
  }

  /// PUT that returns the FULL envelope.
  Future<Map<String, dynamic>> putEnvelope(
    String path, {
    Object? body,
    Map<String, String>? headers,
    bool authenticated = true,
  }) async {
    final url = Uri.parse('${ApiConfig.apiBase}$path');
    final req = http.Request('PUT', url);
    req.headers.addAll({
      'Accept': 'application/json',
      if (body != null) 'Content-Type': 'application/json',
      ...?headers,
    });

    if (authenticated) {
      final token = await _tokens.readToken();
      if (token != null && token.isNotEmpty) {
        req.headers['Authorization'] = 'Bearer $token';
      }
    }

    if (body != null) req.body = jsonEncode(body);
    return _raw(req);
  }

  /// Raw POST to `apiBase + path` (bypass envelope for auth flows).
  Future<Map<String, dynamic>> postRaw(
    String path, {
    Object? body,
    Map<String, String>? headers,
  }) async {
    final url = Uri.parse('${ApiConfig.apiBase}$path');
    final req = http.Request('POST', url);
    req.headers.addAll({
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...?headers,
    });
    if (body != null) req.body = jsonEncode(body);
    return _raw(req);
  }

  // ─────────────────────────────────────────
  // Internals
  // ─────────────────────────────────────────

  Future<dynamic> _send(
    String method,
    String path, {
    Map<String, String>? query,
    Object? body,
    Map<String, String>? headers,
    bool authenticated = true,
  }) async {
    var url = Uri.parse('${ApiConfig.apiBase}$path');
    if (query != null && query.isNotEmpty) {
      url = url.replace(queryParameters: query);
    }

    final req = http.Request(method, url);
    req.headers.addAll({
      'Accept': 'application/json',
      if (body != null) 'Content-Type': 'application/json',
      ...?headers,
    });

    if (authenticated) {
      final token = await _tokens.readToken();
      if (token != null && token.isNotEmpty) {
        req.headers['Authorization'] = 'Bearer $token';
      }
    }

    if (body != null) req.body = jsonEncode(body);

    final envelope = await _raw(req);
    return envelope[ApiConfig.kData];
  }

  /// Send a request, parse envelope, throw ApiException on error.
  Future<Map<String, dynamic>> _raw(http.Request req) async {
    http.Response res;
    try {
      res = await _http.send(req).then(http.Response.fromStream)
          .timeout(ApiConfig.defaultTimeout);
    } on TimeoutException {
      throw ApiException(
        code: ApiException.timeout,
        message: 'Request timed out',
      );
    } catch (e) {
      throw ApiException(
        code: ApiException.networkError,
        message: 'Network error: $e',
      );
    }

    return _parseEnvelope(res);
  }

  Map<String, dynamic> _parseEnvelope(http.Response res) {
    Map<String, dynamic> body;
    try {
      body = res.body.isEmpty
          ? <String, dynamic>{}
          : jsonDecode(res.body) as Map<String, dynamic>;
    } catch (_) {
      throw ApiException(
        code: ApiException.serverError,
        message: 'Invalid JSON response',
        statusCode: res.statusCode,
      );
    }

    final ok = body[ApiConfig.kSuccess] == true;
    if (ok) return body;

    // Error path
    final err = (body[ApiConfig.kError] as Map?)?.cast<String, dynamic>();
    if (res.statusCode == 401) {
      // Best-effort cleanup — do not await (avoid blocking error path)
      _tokens.clear();
    }

    throw ApiException(
      code: (err?[ApiConfig.kErrorCode] as String?) ??
          _statusToCode(res.statusCode),
      message: (err?[ApiConfig.kMessage] as String?) ??
          'HTTP ${res.statusCode}',
      statusCode: res.statusCode,
      details: (err?[ApiConfig.kErrorDetails] as Map?)?.cast<String, dynamic>(),
      requestId: body[ApiConfig.kRequestId] as String?,
    );
  }

  String _statusToCode(int status) => switch (status) {
        400 => 'BAD_REQUEST',
        401 => ApiException.unauthorized,
        403 => ApiException.forbidden,
        404 => ApiException.notFound,
        409 => ApiException.stateConflict,
        422 => ApiException.validationFailed,
        429 => ApiException.rateLimited,
        500 || 502 || 503 || 504 => ApiException.serverError,
        _ => 'HTTP_$status',
      };

  void dispose() => _http.close();
}
