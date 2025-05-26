class FireReport {
  final int id;
  final int reportedBy;
  final int? fireStationId;
  final String location;
  final double? latitude;
  final double? longitude;
  final String? landmark;
  final String? description;
  final String? contactInfo;
  final String status;
  final DateTime createdAt;
  final DateTime updatedAt;
  final int? markedAsContainedById;
  final DateTime? markedAsContainedAt;

  FireReport({
    required this.id,
    required this.reportedBy,
    this.fireStationId,
    required this.location,
    this.latitude,
    this.longitude,
    this.landmark,
    this.description,
    this.contactInfo,
    required this.status,
    required this.createdAt,
    required this.updatedAt,
    this.markedAsContainedById,
    this.markedAsContainedAt,
  });

  factory FireReport.fromJson(Map<String, dynamic> json) {
    return FireReport(
      id: json['id'],
      reportedBy: json['reported_by'],
      fireStationId:
          json['fireStationId'] != null ? json['fireStationId'] as int : null,
      location: json['location'],
      latitude:
          json['latitude'] != null
              ? (json['latitude'] is String
                  ? double.tryParse(json['latitude']) ?? 0.0
                  : json['latitude'])
              : null,
      longitude:
          json['longitude'] != null
              ? (json['longitude'] is String
                  ? double.tryParse(json['longitude']) ?? 0.0
                  : json['longitude'])
              : null,
      landmark: json['landmark'],
      description: json['description'],
      contactInfo: json['contact_info'],
      status: json['status'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
      markedAsContainedById:
          json['marked_as_contained_by_id'] != null
              ? json['marked_as_contained_by_id'] as int
              : null,
      markedAsContainedAt:
          json['marked_as_contained_at'] != null
              ? DateTime.parse(json['marked_as_contained_at'])
              : null,
    );
  }
}
