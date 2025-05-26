import 'dart:io';

import 'package:fire_response_app/api.dart';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class AuthProvider extends ChangeNotifier {
  String? _token;
  bool _isLoggedIn = false;
  String _userId = "";
  String get userId => _userId;
  String _userRole = ""; // Store user role
  String get userRole => _userRole;
  static const String api = API.baseUrl;
  bool get isLoggedIn => _isLoggedIn;
  String? get token => _token;

  Future<void> _saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }

  Future<void> _removeToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }

  Future<void> loadToken() async {
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString('auth_token');
    _isLoggedIn = _token != null;
    notifyListeners();
  }

  Future<void> saveFcmToken(String fcmToken) async {
    final prefs = await SharedPreferences.getInstance();
    String? authToken = _token; // Get current token from _token

    if (authToken == null) {
      print("No auth token found");
      return;
    }

    // Make sure you're sending the correct Authorization token (the one you already saved)
    final response = await http.post(
      Uri.parse('$api/save-fcm-token'),
      body: json.encode({'fcm_token': fcmToken}),
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $authToken',
      },
    );

    if (response.statusCode == 200) {
      print('FCM Token saved successfully');
    } else {
      print('Failed to save FCM token: ${response.statusCode}');
    }
  }

  Future<String?> register({
    required String email,
    required String password,
    required String confirmPassword,
    required String firstname,
    required String lastname,
    required String address,
    required File idImageFile,
    required File profileImageFile,
  }) async {
    if (password != confirmPassword) {
      return "Passwords do not match";
    }

    if (idImageFile.path.isEmpty || profileImageFile.path.isEmpty) {
      return "Both ID image and Profile image are required.";
    }

    try {
      var uri = Uri.parse("$api/register");

      var request =
          http.MultipartRequest('POST', uri)
            ..fields['email'] = email
            ..fields['password'] = password
            ..fields['password_confirmation'] = confirmPassword
            ..fields['userFirstName'] = firstname
            ..fields['userLastName'] = lastname
            ..fields['userAddress'] = address
            ..fields['userContactNumber'] = ""
            ..fields['userBirthDate'] = "";

      request.files.add(
        await http.MultipartFile.fromPath('id_image', idImageFile.path),
      );

      request.files.add(
        await http.MultipartFile.fromPath(
          'profile_image',
          profileImageFile.path,
        ),
      );

      request.headers.addAll({"Accept": "application/json"});

      var streamedResponse = await request.send();
      var response = await http.Response.fromStream(streamedResponse);

      print("Status Code: ${response.statusCode}");
      print("Response Body: ${response.body}");

      if (response.statusCode == 201) {
        return null; // Success
      } else {
        try {
          final responseBody = jsonDecode(response.body);
          return responseBody["message"] ?? "Registration failed";
        } catch (e) {
          return "Unexpected server response. Please try again later.";
        }
      }
    } catch (e) {
      return "An error occurred: $e";
    }
  }

  Future<String?> login({
    required String email,
    required String password,
  }) async {
    final url = Uri.parse("$api/login");

    // print("Sending request to: $url");
    // print("userEmail: $email, userPassword: $password");

    try {
      final response = await http.post(
        url,
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
        },
        body: jsonEncode({"email": email, "password": password}),
      );

      if (response.statusCode == 201) {
        final responseBody = jsonDecode(response.body);
        print("Response Body: $responseBody");

        _token = responseBody["token"];
        _userRole = responseBody["user"]["userRole"];
        _userId = responseBody["user"]["id"].toString();
        _isLoggedIn = true;

        // ✅ Save to SharedPreferences
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', _token!);
        await prefs.setString('user_role', _userRole);
        await prefs.setString('user_id', _userId);

        notifyListeners(); // 🔥 Notify UI that login was successful
        return null; // Success
      } else {
        print("Error: ${response.statusCode}, ${response.body}");
        return "Login failed";
      }
    } catch (e) {
      print("Request failed: $e");
      return "Something went wrong";
    }
  }

  Future<void> logout() async {
    await http.post(
      Uri.parse("$api/logout"),
      headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer $_token",
      },
    );

    _token = null;
    _isLoggedIn = false;
    await _removeToken();
    notifyListeners();
  }

  Future<void> saveAuthToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
    _token = token;
    _isLoggedIn = true;
    notifyListeners();
  }

  Future<void> loadUserSession() async {
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString('auth_token');
    _userRole = prefs.getString('user_role') ?? 'unknown';
    _userId = prefs.getString('user_id') ?? '';

    if (_token != null) {
      _isLoggedIn = true;
    } else {
      _isLoggedIn = false;
    }

    notifyListeners();
  }

  Future<int?> fetchFirefighterId() async {
    try {
      await loadToken(); // make sure token and user ID are loaded
      final token = _token;
      final userId = _userId;

      if (token == null || userId.isEmpty) {
        print("Missing token or userId");
        return null;
      }

      final response = await http.get(
        Uri.parse('$api/firefighter-id'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final firefighterId = data['firefighter_id'];
        print("Fetched firefighterId: $firefighterId");
        return firefighterId;
      } else {
        print("Failed to fetch firefighter ID. Status: ${response.statusCode}");
        return null;
      }
    } catch (e) {
      print("Error fetching firefighter ID: $e");
      return null;
    }
  }
}
