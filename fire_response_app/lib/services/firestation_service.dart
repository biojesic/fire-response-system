import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:fire_response_app/models/fire_station.dart';
import 'package:fire_response_app/api.dart';

class FireStationService {
  static const String api = API.baseUrl;

  // Fetch all fire stations from the database
  Future<List<FireStation>> getFireStations() async {
    try {
      final response = await http.get(Uri.parse('$api/fire_stations'));

      if (response.statusCode == 200) {
        final List<dynamic> jsonData = json.decode(response.body);
        return jsonData.map((data) => FireStation.fromJson(data)).toList();
      } else {
        throw Exception('Failed to load fire stations');
      }
    } catch (e) {
      print('Error: $e');
      throw e;
    }
  }

  // Add a new fire station to the database
  Future<void> addFireStation(FireStation station) async {
    final response = await http.post(
      Uri.parse('$api/fire_stations/'),
      headers: {'Content-Type': 'application/json'},
      body: json.encode(station.toJson()..['id'] = null),
    );

    if (response.statusCode != 201) {
      throw Exception('Failed to add fire station');
    }
  }

  // Update an existing fire station in the database
  Future<void> updateFireStation(FireStation station) async {
    final response = await http.put(
      Uri.parse('$api/fire_stations/${station.id}'),
      headers: {'Content-Type': 'application/json'},
      body: json.encode(station.toJson()),
    );

    if (response.statusCode != 200) {
      throw Exception('Failed to update fire station');
    }
  }

  // Delete a fire station from the database
  Future<void> deleteFireStation(int id) async {
    final response = await http.delete(Uri.parse('$api/fire_stations/$id'));

    if (response.statusCode != 204) {
      throw Exception('Failed to delete fire station');
    }
  }
}
