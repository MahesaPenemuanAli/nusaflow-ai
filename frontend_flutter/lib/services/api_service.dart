import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:frontend_flutter/config/api_config.dart';
import 'package:frontend_flutter/models/destination_model.dart';
import 'package:frontend_flutter/models/crowd_status_model.dart';
import 'package:frontend_flutter/models/recommendation_model.dart';

class ApiService {
  final http.Client _client = http.Client();
  final Duration _timeout = const Duration(seconds: 10);

  Future<List<DestinationModel>> getDestinations({
    String? search,
    int? categoryId,
    String? categorySlug,
    String? crowdLevel,
    int limit = 20,
  }) async {
    final queryParameters = <String, String>{
      'limit': limit.toString(),
      if (search != null && search.isNotEmpty) 'search': search,
      if (categoryId != null) 'category_id': categoryId.toString(),
      if (categorySlug != null && categorySlug.isNotEmpty) 'category_slug': categorySlug,
      if (crowdLevel != null && crowdLevel.isNotEmpty) 'crowd_level': crowdLevel,
    };

    final uri = Uri.parse('${ApiConfig.baseUrl}/destinations').replace(queryParameters: queryParameters);

    try {
      final response = await _client.get(uri).timeout(_timeout);

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        if (decoded['success'] == true) {
          final data = decoded['data'] as List;
          return data.map((json) => DestinationModel.fromJson(json)).toList();
        } else {
          throw Exception(decoded['message'] ?? 'Gagal mengambil destinasi');
        }
      } else {
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Gagal menghubungi server: $e');
    }
  }

  Future<DestinationModel> getDestinationDetail(int id) async {
    final uri = Uri.parse('${ApiConfig.baseUrl}/destinations/$id');

    try {
      final response = await _client.get(uri).timeout(_timeout);

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        if (decoded['success'] == true) {
          return DestinationModel.fromJson(decoded['data']);
        } else {
          throw Exception(decoded['message'] ?? 'Gagal mengambil detail destinasi');
        }
      } else {
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Gagal menghubungi server: $e');
    }
  }

  Future<CrowdStatusModel> getCrowdStatus(
    int destinationId, {
    String? date,
    int? hour,
  }) async {
    final queryParameters = <String, String>{};
    if (date != null) {
      queryParameters['date'] = date;
    }
    if (hour != null) {
      queryParameters['hour'] = hour.toString();
    }

    final uri = Uri.parse('${ApiConfig.baseUrl}/destinations/$destinationId/crowd-status')
        .replace(queryParameters: queryParameters);

    try {
      final response = await _client.get(uri).timeout(_timeout);

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        if (decoded['success'] == true) {
          return CrowdStatusModel.fromJson(decoded['data']);
        } else {
          throw Exception(decoded['message'] ?? 'Gagal mengambil status keramaian');
        }
      } else {
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Gagal menghubungi server: $e');
    }
  }

  Future<List<RecommendationModel>> getRecommendations(
    int destinationId, {
    String? date,
    int? hour,
    int limit = 5,
  }) async {
    final queryParameters = <String, String>{
      'limit': limit.toString(),
    };
    if (date != null) {
      queryParameters['date'] = date;
    }
    if (hour != null) {
      queryParameters['hour'] = hour.toString();
    }

    final uri = Uri.parse('${ApiConfig.baseUrl}/destinations/$destinationId/recommendations')
        .replace(queryParameters: queryParameters);

    try {
      final response = await _client.get(uri).timeout(_timeout);

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        if (decoded['success'] == true) {
          final data = decoded['data'] as List;
          return data.map((json) => RecommendationModel.fromJson(json)).toList();
        } else {
          throw Exception(decoded['message'] ?? 'Gagal mengambil rekomendasi');
        }
      } else {
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Gagal menghubungi server: $e');
    }
  }
}
