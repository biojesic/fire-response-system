import 'package:fire_response_app/api.dart';
import 'package:fire_response_app/models/firefighter_report.dart'; // Import FirefighterReport model
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class FirefighterReportsProvider with ChangeNotifier {
  List<FireFighterReport> _fireReports = [];
  FireFighterReport? _selectedReport; // To store the selected report details
  bool _isLoading = false;
  final String api = API.baseUrl;
  bool get isLoading => _isLoading;
  FireFighterReport? get selectedReport => _selectedReport;

  List<FireFighterReport> get fireReports => _fireReports;

  // Constructor accepts token
  final String token;

  FirefighterReportsProvider(this.token);

  // Fetch firefighter reports list
  Future<void> fetchFirefighterReports() async {
    final url = '$api/assigned-fire-reports';
    try {
      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Authorization': 'Bearer $token', // Use token for authorization
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body) as List;
        _fireReports =
            data
                .map((reportData) => FireFighterReport.fromJson(reportData))
                .toList();
        notifyListeners();
      } else {
        throw Exception(
          'Failed to load firefighter reports: ${response.statusCode}',
        );
      }
    } catch (error) {
      // Log the error for debugging
      print('Error fetching firefighter reports: $error');
      throw Exception('Error fetching firefighter reports: $error');
    }
  }

  // Fetch details of a specific fire report
  Future<void> fetchFireReportDetails(int reportId) async {
    final url = '$api/fire-reports/$reportId';
    try {
      _isLoading = true;
      notifyListeners();

      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);

        print('Fetched Fire Report Details: $data');

        _selectedReport = FireFighterReport.fromJson(
          data,
        ); // Set the selected report
        _isLoading = false;
        notifyListeners();
      } else {
        throw Exception(
          'Failed to load fire report details: ${response.statusCode}',
        );
      }
    } catch (error) {
      print('Error fetching fire report details: $error');
      _isLoading = false;
      notifyListeners();
      throw Exception('Error fetching fire report details: $error');
    }
  }

  // Select a report to view details
  void selectReport(FireFighterReport report) {
    _selectedReport = report;
    notifyListeners(); // Notify UI to update with the selected report details
  }

  Future<void> submitFinalReport(
    BuildContext context,
    int fireReportId,
    Map<String, String> body,
  ) async {
    final String apiUrl = '$api/fire-report/$fireReportId/final';
    final Map<String, String> headers = {
      'Content-Type': 'application/json',
      // 'Authorization': 'Bearer $token',
    };

    // Debugging: Print the API URL and the body of the request
    print('API URL: $apiUrl');
    print('Request Body: $body');
    print('Authorization Header: Bearer $token');

    try {
      final response = await http.post(
        Uri.parse(apiUrl),
        headers: headers,
        body: jsonEncode(body),
      );

      // Debugging: Print response status code and body
      print('Response Status Code: ${response.statusCode}');
      print('Response Body: ${response.body}');

      if (response.statusCode == 201) {
        // If the request was successful, show a success message
        final data = jsonDecode(response.body);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Report submitted successfully: ${data['message']}'),
          ),
        );
      } else {
        // If the request failed, show an error message
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              'Failed to submit the report. Please try again later.',
            ),
          ),
        );
        print(
          'Failed to submit the report. Status Code: ${response.statusCode}',
        );
        print('Response Body: ${response.body}');
      }
    } catch (error) {
      // Handle any errors that may occur during the API call
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('An error occurred. Please try again later.')),
      );
      print('Error submitting final report: $error');
    }
  }
}
