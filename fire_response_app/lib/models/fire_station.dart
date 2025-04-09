class FireStation {
  final int id;
  final String firestationName;
  final String firestationLocation;
  final String firestationContactNumber;

  FireStation({
    required this.id,
    required this.firestationName,
    required this.firestationLocation,
    required this.firestationContactNumber,
  });

  // Convert JSON to Firefighter object
  factory FireStation.fromJson(Map<String, dynamic> json) {
    return FireStation(
      id: json['id'],
      firestationName: json['firestationName'],
      firestationLocation: json['firestationLocation'],
      firestationContactNumber: json['firestationContactNumber'],
    );
  }

  // Convert Firefighter object to JSON for updates
  Map<String, dynamic> toJson() {
    return {
      'firestationName': firestationName,
      'firestationLocation': firestationLocation,
      'firestationContactNumber': firestationContactNumber,
    };
  }
}
