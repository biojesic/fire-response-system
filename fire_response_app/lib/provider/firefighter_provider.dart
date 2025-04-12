import 'dart:async';
import 'package:fire_response_app/api.dart';
import 'package:fire_response_app/models/user.dart';
import 'package:fire_response_app/provider/auth_provider.dart';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';

class FirefighterProvider extends ChangeNotifier {
  static const String api = API.baseUrl;
  Position? _currentLocation;
  String _status = "Off Duty"; // Default status
  StreamSubscription<Position>? _positionStream;
  String firefighterName = "";
  String firefighterRole = "";
  String team = "";
  int userId = 0;

  Position? get currentLocation => _currentLocation;
  String get status => _status;
  User? _currentUser;

  User? get currentUser => _currentUser;

  static Timer? _timer; // Timer para sa periodic updates

  Future<void> fetchFirefighterDetails(
    int firefighterId,
    BuildContext context,
  ) async {
    try {
      // Step 1: Load token from AuthProvider
      await Provider.of<AuthProvider>(context, listen: false).loadToken();
      final token = Provider.of<AuthProvider>(context, listen: false).token;

      if (token == null || token.isEmpty) {
        throw Exception('Token is missing or invalid');
      }

      // Step 2: Fetch user details first to get userId
      final userResponse = await http.get(
        Uri.parse('$api/user'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (userResponse.statusCode == 200) {
        final Map<String, dynamic> userData = jsonDecode(userResponse.body);

        // Access the user's first name
        firefighterName = userData['userFirstName'] ?? 'No name available';
        userId = userData['id']; // Set the userId for subsequent requests
      } else {
        // Log the user response body if user details fetching fails
        print("User response body: ${userResponse.body}");
        throw Exception('Failed to fetch user details');
      }

      // Step 3: Fetch firefighter details using the userId
      final firefighterResponse = await http.get(
        Uri.parse('$api/firefighters/$userId/details'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (firefighterResponse.statusCode == 200) {
        final Map<String, dynamic> firefighterData = jsonDecode(
          firefighterResponse.body,
        );

        // Extract firefighter position and team name
        firefighterRole = firefighterData['position']; // "Team Leader"
        team =
            firefighterData['team']['teamName'] ??
            'No team assigned'; // "Alpha"
        firefighterId = firefighterData['id'];

        // Notify listeners to update the UI
        notifyListeners();
        await fetchFirefighterStatus(firefighterId);
      } else {
        throw Exception("Failed to fetch firefighter details");
      }
    } catch (e) {
      // Catch and print any errors
      print("Error fetching firefighter details: $e");
    }
  }

  Future<void> fetchFirefighterStatus(firefighterId) async {
    try {
      final response = await http.get(
        Uri.parse('$api/firefighters/$firefighterId/team-status'),
      );

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = jsonDecode(response.body);
        String fetchedStatus = data['status']; // Adjust key if necessary

        // Update the status
        _status = fetchedStatus;
        notifyListeners();

        // Handle location tracking
        if (_status == "on_response" || _status == "standby") {
          print("Starting location tracking...");
          startLocationTracking(userId);
        } else {
          stopLocationTracking();
        }
      } else {
        throw Exception("Failed to fetch firefighter status:");
      }
    } catch (e) {
      print("Error fetching firefighter status: $e");
    }
  }

  // ✅ Get Current Location
  static Future<Position> getCurrentLocation() async {
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      throw Exception("GPS is disabled.");
    }

    LocationPermission permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.deniedForever) {
        throw Exception("Location permission permanently denied.");
      }
    }

    return await Geolocator.getCurrentPosition(
      desiredAccuracy: LocationAccuracy.high,
    );
  }

  // ✅ Update Location to Server
  static Future<void> updateLocation(int userId, double lat, double lng) async {
    try {
      final response = await http.post(
        Uri.parse("$api/update-location"),
        headers: {"Content-Type": "application/json"},
        body: jsonEncode({
          "user_id": userId,
          "latitude": lat,
          "longitude": lng,
        }),
      );

      if (response.statusCode == 200) {
        print("✅ Location updated successfully!");
      } else {
        print("❌ Error updating location: ${response.body}");
      }
    } catch (e) {
      print("❌ Network Error: $e");
    }
  }

  static void startLocationTracking(int userId) {
    _timer?.cancel(); // Cancel existing timer if any

    _timer = Timer.periodic(Duration(seconds: 120), (timer) async {
      try {
        Position position = await getCurrentLocation();
        await updateLocation(userId, position.latitude, position.longitude);
      } catch (e) {
        print("❌ Location tracking error: $e");
      }
    });
  }

  // ✅ Stop Tracking
  static void stopLocationTracking() {
    _timer?.cancel();
    print("🚫 Location tracking stopped.");
  }

  // Future<void> sendLocationToServer(int userId, double lat, double lng) async {
  //   try {
  //     final response = await http.post(
  //       Uri.parse("$api/update-location"),
  //       headers: {"Content-Type": "application/json"},
  //       body: jsonEncode({
  //         "user_id": userId,
  //         "latitude": lat,
  //         "longitude": lng,
  //       }),
  //     );

  //     if (response.statusCode == 200) {
  //       print("✅ Location updated successfully!");
  //     } else {
  //       print("❌ Error updating location: ${response.body}");
  //     }
  //   } catch (e) {
  //     print("❌ Network Error: $e");
  //   }
  // }
}
