import 'dart:convert';
import 'package:fire_response_app/models/barangay.dart';
import 'package:fire_response_app/models/brgy_fire_reports.dart';
import 'package:fire_response_app/models/user.dart';
import 'package:http/http.dart' as http;
import 'package:flutter/material.dart';
import 'package:fire_response_app/models/brgy_fire_aid.dart';
import 'package:fire_response_app/api.dart';

class FireAidProvider with ChangeNotifier {
  static const String api = API.baseUrl;

  // FireAid object to hold data
  FireAid? _fireAid;
  Barangay? _barangay;
  User? _user;
  List<BrgyFireReports> _fireReports = [];
  BrgyFireReports? _fireReportDetails;

  FireAid? get fireAid => _fireAid;
  Barangay? get barangay => _barangay;
  User? get user => _user;
  List<BrgyFireReports> get fireReports => _fireReports;
  BrgyFireReports? get fireReportDetails => _fireReportDetails;

  Future<void> registerFireAid(
    FireAid fireAid, {
    required String userFirstName,
    required String userLastName,
    required String email,
    required String password,
    required String userContactNumber,
    required String userAddress,
    required String userBirthDate,
    required String barangayId,
    String? barangayIdPath,
    String? barangayCertificatePath,
    String? photoPath, // optional
  }) async {
    try {
      var uri = Uri.parse('$api/register/fire-aid');
      var request = http.MultipartRequest('POST', uri);

      // Headers to handle content type correctly
      request.headers.addAll({
        'Accept':
            'application/json', // Accept header to tell the server we want JSON response
        'Content-Type':
            'multipart/form-data', // Set the content type to multipart/form-data
      });

      // Form fields
      request.fields['userFirstName'] = userFirstName;
      request.fields['userLastName'] = userLastName;
      request.fields['email'] = email;
      request.fields['password'] = password;
      request.fields['userContactNumber'] = userContactNumber;
      request.fields['userAddress'] = userAddress;
      request.fields['userBirthDate'] = userBirthDate;
      request.fields['barangay_id'] = barangayId;

      // File fields (files to be uploaded)
      // request.files.add(
      //   await http.MultipartFile.fromPath('barangay_id_path', barangayIdPath),
      // );
      // request.files.add(
      //   await http.MultipartFile.fromPath(
      //     'barangay_certificate_path',
      //     barangayCertificatePath,
      //   ),
      // );

      // Optional photo
      if (barangayIdPath != null && barangayIdPath.isNotEmpty) {
        request.files.add(
          await http.MultipartFile.fromPath('barangay_id_path', barangayIdPath),
        );
      }

      // Optional photo
      if (barangayCertificatePath != null &&
          barangayCertificatePath.isNotEmpty) {
        request.files.add(
          await http.MultipartFile.fromPath(
            'barangay_certificate_path',
            barangayCertificatePath,
          ),
        );
      }

      // Optional photo
      if (photoPath != null && photoPath.isNotEmpty) {
        request.files.add(
          await http.MultipartFile.fromPath('photo', photoPath),
        );
      }

      // Sending the request to the server
      var streamedResponse = await request.send();
      var response = await http.Response.fromStream(streamedResponse);

      if (response.statusCode == 201) {
        // Parsing the response data
        var data = json.decode(response.body);
        _fireAid = FireAid.fromJson(data['data']);
        notifyListeners();
      } else {
        print("🔥 Server error: ${response.statusCode} - ${response.body}");
        throw Exception('Failed to register FireAid');
      }
    } catch (error) {
      print("🔥 Registration error: $error");
      throw error;
    }
  }

  Future<void> fetchFireAidData(String token) async {
    try {
      final url = '$api/fire-aid'; // Endpoint to fetch fire aid data
      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['fire_aid'] != null) {
          // Mapping the response to FireAid model
          _fireAid = FireAid.fromJson(data['fire_aid']);
          notifyListeners(); // Notify listeners when data changes
        } else {
          throw Exception('No fire aid data found.');
        }
      } else {
        throw Exception('Failed to fetch fire aid data');
      }
    } catch (error) {
      print("Error fetching fire aid data: $error");
    }
  }

  // Fetch fire incident reports for the barangay
  Future<void> fetchFireIncidents(String token, int barangayId) async {
    try {
      final url = '$api/barangay/$barangayId/fire-reports';
      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);

        if (data['fireReports'] != null) {
          _fireReports =
              (data['fireReports'] as List)
                  .map((incident) => BrgyFireReports.fromJson(incident))
                  .toList();
        } else {
          print("🔥 No fire reports found or 'fireReports' is null");
        }
        notifyListeners();
      } else {
        print("🔥 Failed to fetch fire reports: ${response.statusCode}");
      }
    } catch (error) {
      print("🔥 Fetch error: $error");
    }
  }

  // Method to fetch a specific fire report by its ID
  Future<void> fetchFireReportDetails(int fireReportId) async {
    try {
      final url =
          '$api/firereports/$fireReportId'; // Endpoint to fetch fire report details
      final response = await http.get(
        Uri.parse(url),
        headers: {'Accept': 'application/json'},
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        _fireReportDetails = BrgyFireReports.fromJson(data); // Store the result
        notifyListeners(); // Notify listeners that data is updated
      } else {
        throw Exception('Failed to fetch fire report details');
      }
    } catch (error) {
      print("Error fetching fire report details: $error");
    }
  }

  Future<void> markAsFalseAlarm(String token, int fireReportId) async {
    try {
      var headers = {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      };

      var uri = Uri.parse('$api/fire-aid/$fireReportId/mark-false-alarm');
      var request = http.MultipartRequest('POST', uri);
      request.headers.addAll(headers);

      var streamedResponse = await request.send();
      var response = await http.Response.fromStream(streamedResponse);

      if (response.statusCode == 200) {
        print("False alarm marked successfully");
        notifyListeners();
      } else {
        print("Failed to mark as false alarm: ${response.body}");
        throw Exception('Failed to mark false alarm');
      }
    } catch (error) {
      print("Error marking as false alarm: $error");
    }
  }
}
