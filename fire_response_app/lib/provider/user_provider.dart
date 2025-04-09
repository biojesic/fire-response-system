import 'package:fire_response_app/api.dart';
import 'package:fire_response_app/models/user.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class UserProvider with ChangeNotifier {
  static const String api = API.baseUrl;
  User? _currentUser;

  User? get currentUser => _currentUser;

  // Fetch user data from MySQL via Laravel API
  Future<void> fetchUserData(String token) async {
    print("Fetching user data with token: $token"); // Debugging

    final response = await http.get(
      Uri.parse('$api/user'),
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
    );

    print("Response Status Code: ${response.statusCode}");
    print("Response Body: ${response.body}");

    if (response.statusCode == 200 || response.statusCode == 201) {
      final Map<String, dynamic>? data = jsonDecode(response.body);

      if (data != null && data.isNotEmpty) {
        _currentUser = User.fromJson(data);
        notifyListeners();
      } else {
        print("Empty user data received.");
      }
    } else {
      print("Failed to fetch user data. Status Code: ${response.statusCode}");
    }
  }

  // Method to update user information
  Future<bool> updateUser(User updatedUser) async {
    final url = Uri.parse('$api/user/${updatedUser.id}');

    try {
      final response = await http.put(
        url,
        headers: {'Content-Type': 'application/json'},
        body: json.encode(updatedUser.toJson()),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        _currentUser = updatedUser;
        notifyListeners();
        return true;
      } else {
        return false;
      }
    } catch (error) {
      print('Error updating user data: $error');
      return false;
    }
  }
}
