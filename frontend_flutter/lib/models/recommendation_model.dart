import 'package:frontend_flutter/models/destination_model.dart';
import 'package:frontend_flutter/models/crowd_status_model.dart';

class RecommendationModel {
  final DestinationModel destination;
  final CrowdStatusModel crowdStatus;
  final double relevanceScore;

  RecommendationModel({
    required this.destination,
    required this.crowdStatus,
    required this.relevanceScore,
  });

  factory RecommendationModel.fromJson(Map<String, dynamic> json) {
    return RecommendationModel(
      destination: DestinationModel.fromJson(json['destination'] as Map<String, dynamic>),
      crowdStatus: CrowdStatusModel.fromJson(json['crowd_status'] as Map<String, dynamic>),
      relevanceScore: json['relevance_score'] != null ? double.parse(json['relevance_score'].toString()) : 0.0,
    );
  }
}
