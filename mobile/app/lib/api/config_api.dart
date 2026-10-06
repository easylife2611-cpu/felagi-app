import 'api_client.dart';
import 'api_config.dart';

/// Runtime config endpoints — see ConfigController.php.
class ConfigApi {
  ConfigApi(this._client);

  final ApiClient _client;

  /// GET /api/v1/config — public runtime config.
  Future<Map<String, dynamic>> fetch() async {
    final data = await _client.get(
      ApiConfig.configShow,
      authenticated: false,
    );
    return (data as Map).cast<String, dynamic>();
  }
}
