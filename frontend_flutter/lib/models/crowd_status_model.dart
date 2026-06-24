class CrowdStatusModel {
  final int destinationId;
  final String? predictionDate;
  final int? predictionHour;
  final int? visitorCount;
  final int? maxCapacity;
  final double? crowdScore;
  final String? crowdLevel;
  final String? crowdLabel;
  final String? method;
  final bool? isWeekend;
  final bool? hasEvent;
  final String? eventImpact;

  CrowdStatusModel({
    required this.destinationId,
    this.predictionDate,
    this.predictionHour,
    this.visitorCount,
    this.maxCapacity,
    this.crowdScore,
    this.crowdLevel,
    this.crowdLabel,
    this.method,
    this.isWeekend,
    this.hasEvent,
    this.eventImpact,
  });

  factory CrowdStatusModel.fromJson(Map<String, dynamic> json) {
    final factors = json['factors'] as Map<String, dynamic>?;

    return CrowdStatusModel(
      destinationId: json['destination_id'] as int,
      predictionDate: json['prediction_date'] as String?,
      predictionHour: json['prediction_hour'] as int?,
      visitorCount: json['visitor_count'] as int?,
      maxCapacity: json['max_capacity'] as int?,
      crowdScore: json['crowd_score'] != null ? double.tryParse(json['crowd_score'].toString()) : null,
      crowdLevel: json['crowd_level'] as String?,
      crowdLabel: json['crowd_label'] as String?,
      method: json['method'] as String?,
      isWeekend: factors != null ? factors['is_weekend'] as bool? : null,
      hasEvent: factors != null ? factors['has_event'] as bool? : null,
      eventImpact: factors != null ? factors['event_impact'] as String? : null,
    );
  }
}
