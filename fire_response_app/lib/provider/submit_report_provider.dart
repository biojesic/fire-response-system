import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:provider/provider.dart';
import 'package:http/http.dart' as http;
import 'package:fire_response_app/api.dart';
import 'package:fire_response_app/provider/auth_provider.dart';
// import 'package:flutter_timezone/flutter_timezone.dart' as tz;

class SubmitReportProvider extends ChangeNotifier {
  // // Function to get the current time in Philippine Time (Asia/Manila)
  // Future<String> _getPhilippineTime() async {
  //   final location = await tz.FlutterTimezone.getLocalTimezone();
  //   final now = DateTime.now();
  //   final localTime = await tz.FlutterTimezone.(
  //     now,
  //     location,
  //   );
  //   return localTime.toIso8601String();
  // }

  // 🔍 Method to fetch coordinates from Google Maps Geocoding API
  Future<Map<String, double>?> _getCoordinatesFromGoogleAPI(
    String address,
  ) async {
    try {
      String apiKey = 'AIzaSyAnst45VUe9XkXDduDBPmuPo7H3YmWDNJ4';
      String url =
          'https://maps.googleapis.com/maps/api/geocode/json?address=${Uri.encodeComponent(address)}&key=$apiKey';

      final response = await http.get(Uri.parse(url));
      if (response.statusCode == 200) {
        var data = json.decode(response.body);
        if (data['status'] == 'OK') {
          final lat = data['results'][0]['geometry']['location']['lat'];
          final lng = data['results'][0]['geometry']['location']['lng'];
          return {'latitude': lat, 'longitude': lng};
        }
      }
    } catch (e) {
      print("❌ Google API error: $e");
    }
    return null;
  }

  Future<void> submitFireReport(
    BuildContext context, {
    required GlobalKey<FormState> formKey,
    required TextEditingController locationController,
    required TextEditingController landmarkController,
    required TextEditingController descriptionController,
    required Function clearFields,
    double? existingLatitude,
    double? existingLongitude,
  }) async {
    const String api = API.baseUrl;
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final token = authProvider.token;
    final userId = authProvider.userId;

    if (formKey.currentState!.validate() && userId != null) {
      double? latitude = existingLatitude;
      double? longitude = existingLongitude;

      if ((latitude == null || longitude == null) &&
          locationController.text.trim().isNotEmpty) {
        final coords = await _getCoordinatesFromGoogleAPI(
          locationController.text.trim(),
        );
        if (coords != null) {
          latitude = coords['latitude'];
          longitude = coords['longitude'];
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                'Could not get coordinates from the address provided.',
              ),
            ),
          );
          return;
        }
      }

      if (latitude == null || longitude == null) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Location coordinates missing.')),
        );
        return;
      }

      try {
        final url = Uri.parse('$api/firereports');
        final response = await http.post(
          url,
          headers: {
            'Authorization': 'Bearer $token',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          body: jsonEncode({
            'reported_by': userId,
            'location': locationController.text,
            'landmark': landmarkController.text,
            'description': descriptionController.text,
            'latitude': latitude,
            'longitude': longitude,
          }),
        );

        if (response.statusCode == 201) {
          print("✅ Fire report submitted successfully!");
          ScaffoldMessenger.of(
            context,
          ).showSnackBar(SnackBar(content: Text('Fire report submitted!')));
          clearFields();
        } else {
          print("❌ Error submitting fire report: ${response.body}");
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Failed to submit report. Please try again.'),
            ),
          );
        }
      } catch (e) {
        print("❌ Exception: $e");
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('An error occurred. Please check your connection.'),
          ),
        );
      }
    } else {
      print("⚠️ Form is invalid or userId is null");
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Please fill out all required fields.')),
      );
    }
  }

  Future<void> submitFireReportUnauthenticated(
    BuildContext context, {
    required GlobalKey<FormState> formKey,
    required TextEditingController locationController,
    required TextEditingController landmarkController,
    required TextEditingController descriptionController,
    required TextEditingController contactinfoController,
    required Map<String, double>? geocodedLocation,
    required double? existingLatitude,
    required double? existingLongitude,
    required Function clearFields,
  }) async {
    const String api = API.baseUrl;

    if (formKey.currentState!.validate()) {
      double? latitude = geocodedLocation?['latitude'] ?? existingLatitude;
      double? longitude = geocodedLocation?['longitude'] ?? existingLongitude;

      if ((latitude == null || longitude == null) &&
          locationController.text.trim().isNotEmpty) {
        final coords = await _getCoordinatesFromGoogleAPI(
          locationController.text.trim(),
        );
        if (coords != null) {
          latitude = coords['latitude'];
          longitude = coords['longitude'];
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                'Could not get coordinates from the address provided.',
              ),
            ),
          );
          return;
        }
      }

      if (latitude == null || longitude == null) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Coordinates could not be determined.')),
        );
        return;
      }

      try {
        final url = Uri.parse('$api/firereports');
        final response = await http.post(
          url,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          body: jsonEncode({
            'location': locationController.text,
            'landmark': landmarkController.text,
            'description': descriptionController.text,
            'contact_info': contactinfoController.text,
            'latitude': latitude,
            'longitude': longitude,
          }),
        );

        if (response.statusCode == 201) {
          print("✅ Fire report submitted successfully!");
          ScaffoldMessenger.of(
            context,
          ).showSnackBar(SnackBar(content: Text('Fire report submitted!')));
          clearFields();
        } else {
          print("❌ Error submitting fire report: ${response.body}");
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Failed to submit report. Please try again.'),
            ),
          );
        }
      } catch (e) {
        print("❌ Exception: $e");
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('An error occurred. Please check your connection.'),
          ),
        );
      }
    } else {
      print("⚠️ Form is invalid");
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Please fill out all required fields.')),
      );
    }
  }

  Future<void> quickReport(BuildContext context) async {
    const String api = API.baseUrl;
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final token = authProvider.token;
    final userId = authProvider.userId;

    // Check if the user is authenticated
    if (userId != null) {
      try {
        // Step 1: Get User's Current Location (latitude and longitude)
        Position position = await Geolocator.getCurrentPosition(
          desiredAccuracy: LocationAccuracy.high,
        );
        double latitude = position.latitude;
        double longitude = position.longitude;

        // Step 2: Get User's Address based on latitude and longitude
        String? address = await _getAddressFromLatLng(latitude, longitude);

        if (address == null) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Could not fetch address from coordinates.'),
            ),
          );
          return;
        }

        // Step 3: Submit the report with the user's details
        final url = Uri.parse('$api/firereports');
        final response = await http.post(
          url,
          headers: {
            'Authorization': 'Bearer $token',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          body: jsonEncode({
            'reported_by': userId,
            'location': address,
            'latitude': latitude,
            'longitude': longitude,
          }),
        );

        if (response.statusCode == 201) {
          print("✅ Fire report submitted successfully!");
          ScaffoldMessenger.of(
            context,
          ).showSnackBar(SnackBar(content: Text('Fire report submitted!')));
        } else {
          print("❌ Error submitting fire report: ${response.body}");
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Failed to submit report. Please try again.'),
            ),
          );
        }
      } catch (e) {
        print("❌ Exception: $e");
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('An error occurred. Please check your connection.'),
          ),
        );
      }
    } else {
      print("⚠️ User is not authenticated.");
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('You must be logged in to submit a report.')),
      );
    }
  }

  // Method to get the user's address based on their latitude and longitude
  Future<String?> _getAddressFromLatLng(
    double latitude,
    double longitude,
  ) async {
    try {
      String apiKey = 'AIzaSyAnst45VUe9XkXDduDBPmuPo7H3YmWDNJ4';
      String url =
          'https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=$apiKey';

      final response = await http.get(Uri.parse(url));
      if (response.statusCode == 200) {
        var data = json.decode(response.body);
        if (data['status'] == 'OK') {
          return data['results'][0]['formatted_address'];
        }
      }
    } catch (e) {
      print("❌ Google API error: $e");
    }
    return null;
  }
}
