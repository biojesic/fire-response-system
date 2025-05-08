class FireFighterReport {
  final int? reportId;
  final String location;
  final String landmark;
  final String status;
  final DateTime createdAt;
  final int markedAsContainedBy;
  final DateTime markedAsContainedAt;

  // Constructor
  FireFighterReport({
    required this.reportId,
    required this.location,
    required this.landmark,
    required this.status,
    required this.createdAt,
    required this.markedAsContainedBy,
    required this.markedAsContainedAt,
  });

  // Factory constructor to create a FireReport from JSON
  factory FireFighterReport.fromJson(Map<String, dynamic> json) {
    return FireFighterReport(
      reportId: json['report_id'] ?? 0, // Default to 0 if null
      location: json['location'] ?? '',
      landmark: json['landmark'] ?? '',
      status: json['status'] ?? '',
      createdAt:
          json['created_at'] != null
              ? DateTime.parse(json['created_at'])
              : DateTime.now(),
      markedAsContainedBy: json['marked_as_contained_by_id'] ?? 0,
      markedAsContainedAt:
          json['marked_as_contained_at'] != null
              ? DateTime.parse(json['marked_as_contained_at'])
              : DateTime.now(),
    );
  }

  // Method to convert FireReport to JSON (optional, for sending data back to API)
  Map<String, dynamic> toJson() {
    return {
      'report_id': reportId,
      'location': location,
      'landmark': landmark,
      'status': status,
      'created_at': createdAt.toIso8601String(),
      'marked_as_contained_by_id': markedAsContainedBy,
      'marked_as_contained_at': markedAsContainedAt,
    };
  }
}
