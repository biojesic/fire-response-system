import 'package:fire_response_app/models/fire_station.dart';
import 'package:fire_response_app/services/firestation_service.dart';
import 'package:flutter/material.dart';

class FireStationProvider extends ChangeNotifier {
  List<FireStation> _fireStation = [];
  FireStation? _selectedStation;

  List<FireStation> get fireStation => _fireStation;
  FireStation? get selectedStation => _selectedStation;

  final FireStationService _fireStationService = FireStationService();

  // Fetch all fire stations from the database
  Future<void> fetchFireStations() async {
    _fireStation = await _fireStationService.getFireStations();
    notifyListeners(); // Notify UI
  }

  // Add new fire station
  Future<void> addFireStation(FireStation station) async {
    await _fireStationService.addFireStation(station);
    _fireStation.add(station); // Update local state
    notifyListeners(); // Notify UI
  }

  // Edit a fire station
  Future<void> updateFireStation(int index, FireStation updatedStation) async {
    await _fireStationService.updateFireStation(updatedStation);
    _fireStation[index] = updatedStation; // Update local state
    _selectedStation =
        updatedStation; // Optional: If you want to keep track of the selected station
    notifyListeners(); // Notify UI
  }

  // Delete a fire station
  Future<void> deleteFireStation(int stationId) async {
    await _fireStationService.deleteFireStation(
      stationId,
    ); // Delete from the database
    _fireStation.removeWhere(
      (station) => station.id == stationId,
    ); // Update local state
    notifyListeners(); // Notify UI
  }

  // Select a fire station to view details
  void selectStation(FireStation station) {
    _selectedStation = station;
    notifyListeners(); // Notify UI
  }
}
