import 'dart:convert';

class BrgyFireReports {
  final int id;
  final String location;
  final String? landmark;
  final String? description;
  final String? contactInfo;
  final String? status;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final double? latitude;
  final double? longitude;
  final int? fireStationId;
  final int? barangayId;
  final String? markedAsFalseAlarmBy;
  final DateTime? markedAsFalseAlarmAt;
  final String? markedAsContainedById;
  final DateTime? markedAsContainedAt;

  BrgyFireReports({
    required this.id,
    required this.location,
    this.landmark,
    this.description,
    this.contactInfo,
    this.status,
    this.createdAt,
    this.updatedAt,
    this.latitude,
    this.longitude,
    this.fireStationId,
    this.barangayId,
    this.markedAsFalseAlarmBy,
    this.markedAsFalseAlarmAt,
    this.markedAsContainedById,
    this.markedAsContainedAt,
  });

  // fromMap - Converts a Map to a FireReport object
  factory BrgyFireReports.fromMap(Map<String, dynamic> map) {
    return BrgyFireReports(
      id: map['id'],
      location: map['location'],
      landmark: map['landmark'],
      description: map['description'],
      contactInfo: map['contact_info'],
      status: map['status'],
      createdAt:
          map['created_at'] != null ? DateTime.parse(map['created_at']) : null,
      updatedAt:
          map['updated_at'] != null ? DateTime.parse(map['updated_at']) : null,
      latitude: map['latitude'] != null ? double.parse(map['latitude']) : null,
      longitude:
          map['longitude'] != null ? double.parse(map['longitude']) : null,
      fireStationId: map['fireStationId'],
      barangayId: map['barangay_id'],
      markedAsFalseAlarmBy: map['marked_as_false_alarm_by'],
      markedAsFalseAlarmAt:
          map['marked_as_false_alarm_at'] != null
              ? DateTime.parse(map['marked_as_false_alarm_at'])
              : null,
      markedAsContainedById: map['marked_as_contained_by_id'],
      markedAsContainedAt:
          map['marked_as_contained_at'] != null
              ? DateTime.parse(map['marked_as_contained_at'])
              : null,
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'id': id,
      'location': location,
      'landmark': landmark,
      'description': description,
      'contact_info': contactInfo,
      'status': status,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'latitude': latitude,
      'longitude': longitude,
      'fireStationId': fireStationId,
      'barangay_id': barangayId,
      'marked_as_false_alarm_by': markedAsFalseAlarmBy,
      'marked_as_false_alarm_at': markedAsFalseAlarmAt?.toIso8601String(),
      'marked_as_contained_by_id': markedAsContainedById,
      'marked_as_contained_at': markedAsContainedAt?.toIso8601String(),
    };
  }

  factory BrgyFireReports.fromJson(Map<String, dynamic> json) {
    return BrgyFireReports.fromMap(json);
  }

  String toJson() {
    return json.encode(toMap());
  }
}
