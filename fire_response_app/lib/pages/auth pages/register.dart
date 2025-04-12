import 'package:fire_response_app/pages/auth%20pages/login.dart';
import 'package:fire_response_app/provider/auth_provider.dart';
import 'package:flutter/material.dart';
// import 'dart:convert';
// import 'package:crypto/crypto.dart';
import 'package:provider/provider.dart';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});

  // String hashPassword(String password) {
  //   final bytes = utf8.encode(password); // Convert to bytes
  //   final hashed = sha256.convert(bytes); // Hash using SHA-256
  //   return hashed.toString();
  // }

  @override
  State<RegisterPage> createState() => _RegisterPageState();
}

bool _isLoading = false;

class _RegisterPageState extends State<RegisterPage> {
  final TextEditingController firstNameController = TextEditingController();
  final TextEditingController lastNameController = TextEditingController();
  final TextEditingController emailController = TextEditingController();
  final TextEditingController addressController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  final TextEditingController confirmPasswordController =
      TextEditingController();

  bool _isObscured = true;
  bool _isObscuredConfirm = true;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black87,
      body: SingleChildScrollView(
        child: Container(
          constraints: BoxConstraints(
            minHeight:
                MediaQuery.of(context).size.height, // ✅ Para walang extra space
          ),
          padding: EdgeInsets.fromLTRB(35, 120, 35, 60),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: [Colors.red.shade800, Colors.black87], // Mas fiery effect
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
            ),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            mainAxisAlignment: MainAxisAlignment.start,
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Text(
                'Sign Up',
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 35,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 35),
              TextFormField(
                controller: firstNameController,
                decoration: InputDecoration(
                  labelText: 'First Name',
                  labelStyle: TextStyle(color: Colors.white),
                  focusedBorder: UnderlineInputBorder(
                    borderSide: BorderSide(color: Colors.white),
                  ),
                ),
                style: TextStyle(color: Colors.white),
                cursorColor: Colors.white,
              ),
              const SizedBox(height: 15),
              TextFormField(
                controller: lastNameController,
                decoration: InputDecoration(
                  labelText: 'Last Name',
                  labelStyle: TextStyle(color: Colors.white),
                  focusedBorder: UnderlineInputBorder(
                    borderSide: BorderSide(color: Colors.white),
                  ),
                ),
                style: TextStyle(color: Colors.white),
                cursorColor: Colors.white,
              ),
              const SizedBox(height: 15),
              TextFormField(
                controller: emailController,
                decoration: InputDecoration(
                  labelText: 'Email',
                  labelStyle: TextStyle(color: Colors.white),
                  focusedBorder: UnderlineInputBorder(
                    borderSide: BorderSide(color: Colors.white),
                  ),
                ),
                style: TextStyle(color: Colors.white),
                cursorColor: Colors.white,
              ),
              const SizedBox(height: 15),
              TextFormField(
                controller: addressController,
                decoration: InputDecoration(
                  labelText: 'Address',
                  labelStyle: TextStyle(color: Colors.white),
                  focusedBorder: UnderlineInputBorder(
                    borderSide: BorderSide(color: Colors.white),
                  ),
                ),
                style: TextStyle(color: Colors.white),
                cursorColor: Colors.white,
              ),
              const SizedBox(height: 15),
              TextFormField(
                controller: passwordController,
                obscureText: _isObscured, // Para sa password hiding
                decoration: InputDecoration(
                  labelText: 'Password',
                  labelStyle: TextStyle(color: Colors.white),
                  suffixIcon: IconButton(
                    icon: Icon(
                      _isObscured ? Icons.visibility_off : Icons.visibility,
                    ),
                    onPressed: () {
                      setState(() {
                        _isObscured = !_isObscured; // Toggle visibility
                      });
                    },
                  ),
                  focusedBorder: UnderlineInputBorder(
                    borderSide: BorderSide(color: Colors.white),
                  ),
                ),
                style: TextStyle(color: Colors.white),
                cursorColor: Colors.white,
              ),
              const SizedBox(height: 15),
              TextFormField(
                controller: confirmPasswordController,
                obscureText: _isObscuredConfirm, // Para sa password hiding
                decoration: InputDecoration(
                  labelText: 'Confirm Password',
                  labelStyle: TextStyle(color: Colors.white),
                  suffixIcon: IconButton(
                    icon: Icon(
                      _isObscuredConfirm
                          ? Icons.visibility_off
                          : Icons.visibility,
                    ),
                    onPressed: () {
                      setState(() {
                        _isObscuredConfirm =
                            !_isObscuredConfirm; // Toggle visibility
                      });
                    },
                  ),
                  focusedBorder: UnderlineInputBorder(
                    borderSide: BorderSide(color: Colors.white),
                  ),
                ),
                style: TextStyle(color: Colors.white),
                cursorColor: Colors.white,
              ),
              const SizedBox(height: 80),
              ElevatedButton(
                onPressed:
                    _isLoading
                        ? null
                        : () async {
                          FocusScope.of(context).unfocus();

                          if (firstNameController.text.trim().isEmpty ||
                              lastNameController.text.trim().isEmpty ||
                              emailController.text.trim().isEmpty ||
                              passwordController.text.trim().isEmpty ||
                              confirmPasswordController.text.trim().isEmpty ||
                              addressController.text.trim().isEmpty) {
                            _showErrorDialog("Please fill out all fields");
                            return;
                          }

                          if (passwordController.text.trim() !=
                              confirmPasswordController.text.trim()) {
                            _showErrorDialog("Passwords do not match");
                            return;
                          }

                          setState(() => _isLoading = true); // 🔥 START loading

                          final authProvider = Provider.of<AuthProvider>(
                            context,
                            listen: false,
                          );

                          String? result = await authProvider.register(
                            email: emailController.text.trim(),
                            password: passwordController.text.trim(),
                            confirmPassword:
                                confirmPasswordController.text.trim(),
                            firstname: firstNameController.text.trim(),
                            lastname: lastNameController.text.trim(),
                            address: addressController.text.trim(),
                          );

                          setState(() => _isLoading = false); // 🔥 END loading

                          if (result == null) {
                            Navigator.pushReplacement(
                              context,
                              MaterialPageRoute(
                                builder: (context) => LoginPage(),
                              ),
                            );
                          } else {
                            _showErrorDialog(result);
                          }
                        },

                style: ElevatedButton.styleFrom(minimumSize: Size(170, 40)),
                child:
                    _isLoading
                        ? SizedBox(
                          width: 22,
                          height: 22,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            valueColor: AlwaysStoppedAnimation<Color>(
                              Colors.black,
                            ),
                          ),
                        )
                        : Text(
                          "Create Account",
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            color: Colors.black,
                            fontSize: 18,
                          ),
                        ),
              ),
              InkWell(
                onTap: () {
                  // Navigate to Sign Up Page
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (context) => LoginPage()),
                  );
                },
                child: Padding(
                  padding: EdgeInsets.all(10),
                  child: Text(
                    "Login to your account",
                    style: TextStyle(color: Colors.white, fontSize: 14),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _showErrorDialog(String message) {
    showDialog(
      context: context,
      builder:
          (ctx) => AlertDialog(
            title: Text("Registration Failed"),
            content: Text(message),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(ctx).pop(),
                child: Text("OK"),
              ),
            ],
          ),
    );
  }
}
