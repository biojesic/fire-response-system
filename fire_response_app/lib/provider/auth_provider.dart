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

  Future<String?> register({
    required String email,
    required String password,
    required String confirmPassword,
    required String firstname,
    required String lastname,
  }) async {
    if (password != confirmPassword) {
      return "Passwords do not match";
    }

    final response = await http.post(
      Uri.parse("$api/register"),
      headers: {"Content-Type": "application/json"},
      body: jsonEncode({
        "userEmail": email,
        "userPassword": password,
        "userPassword_confirmation": confirmPassword,
        "userFirstName": firstname,
        "userLastName": lastname,
      }),
    );

    if (response.statusCode == 201) {
      return null; // Success
    } else {
      final responseBody = jsonDecode(response.body);
      return responseBody["message"] ?? "Registration failed";
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
        body: jsonEncode({"userEmail": email, "userPassword": password}),
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
}
