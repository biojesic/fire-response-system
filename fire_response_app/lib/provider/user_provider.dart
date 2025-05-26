import 'package:fire_response_app/api.dart';
import 'package:fire_response_app/models/user.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class UserProvider with ChangeNotifier {
  static const String api = API.baseUrl;
  User? _currentUser;

  User? get currentUser => _currentUser;

  bool _isNotificationVisible = false;

  bool get isNotificationVisible => _isNotificationVisible;

  // Fetch user data from MySQL via Laravel API
  Future<void> fetchUserData(String token) async {
    try {
      print("Fetching user data with token: $token"); // Debugging

      final response = await http.get(
        Uri.parse('$api/user'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      print("Response Status Code: ${response.statusCode}");
      print("Response Body: ${response.body}");

      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = jsonDecode(response.body);
        // Directly pass 'data' to 'User.fromJson()' since it's already the user object
        _currentUser = User.fromJson(data);
        notifyListeners();
      } else {
        print("Failed to fetch user data. Status Code: ${response.statusCode}");
      }
    } catch (e) {
      print('Error fetching user: $e');
    }
  }

  // Method to update user information
  Future<bool> updateUser(User updatedUser, String token) async {
    final url = Uri.parse('$api/user/${updatedUser.id}');

    try {
      final response = await http.put(
        url,
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: json.encode(updatedUser.toJson()),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        _currentUser = updatedUser;
        notifyListeners();
        return true;
      } else {
        print('Update failed: ${response.statusCode} - ${response.body}');
        return false;
      }
    } catch (error) {
      print('Error updating user data: $error');
      return false;
    }
  }

  Future<List> getOngoingIncidents() async {
    final response = await http.get(Uri.parse('$api/ongoing-incidents'));

    if (response.statusCode == 200) {
      List incidents = json.decode(response.body);
      return incidents;
    } else {
      throw Exception('Failed to load ongoing incidents');
    }
  }

  // You can use this function to trigger the notification visibility
  void toggleNotificationVisibility(bool isVisible) {
    _isNotificationVisible = isVisible;
    notifyListeners(); // Notify all listeners (UI updates)
  }

  // Future<void> saveFcmToken(String fcmToken) async {
  //   final response = await http.post(
  //     Uri.parse(
  //       '$api/save-fcm-token',
  //     ), // Adjust your endpoint
  //     body: json.encode({'fcm_token': fcmToken}),
  //     headers: {
  //       'Content-Type': 'application/json',
  //       'Authorization':
  //           'Bearer your_auth_token',
  //     },
  //   );

  //   if (response.statusCode == 200) {
  //     print('FCM Token saved successfully');
  //   } else {
  //     print('Failed to save FCM token');
  //   }
  // }
}
