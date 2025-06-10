import 'dart:io';

import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';

import 'package:fire_response_app/pages/auth%20pages/login.dart';
import 'package:fire_response_app/provider/auth_provider.dart';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});

  @override
  State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final TextEditingController firstNameController = TextEditingController();
  final TextEditingController lastNameController = TextEditingController();
  final TextEditingController emailController = TextEditingController();
  final TextEditingController addressController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  final TextEditingController confirmPasswordController =
      TextEditingController();
  final TextEditingController contactNumberController = TextEditingController();
  final TextEditingController birthDateController = TextEditingController();

  bool _isLoading = false;
  bool _isObscured = true;
  bool _isObscuredConfirm = true;

  File? _idImage;
  File? _profileImage;

  final ImagePicker _picker = ImagePicker();

  Future<void> _pickImage({required bool isProfile}) async {
    showModalBottomSheet(
      context: context,
      builder:
          (ctx) => SafeArea(
            child: Wrap(
              children: [
                ListTile(
                  leading: const Icon(Icons.photo_camera),
                  title: const Text('Take Photo'),
                  onTap: () async {
                    Navigator.of(ctx).pop();
                    final picked = await _picker.pickImage(
                      source: ImageSource.camera,
                      imageQuality: 30,
                    );
                    if (picked != null) {
                      setState(() {
                        if (isProfile) {
                          _profileImage = File(picked.path);
                        } else {
                          _idImage = File(picked.path);
                        }
                      });
                    }
                  },
                ),
                ListTile(
                  leading: const Icon(Icons.photo_library),
                  title: const Text('Choose from Gallery'),
                  onTap: () async {
                    Navigator.of(ctx).pop();
                    final picked = await _picker.pickImage(
                      source: ImageSource.gallery,
                      imageQuality: 80,
                    );
                    if (picked != null) {
                      setState(() {
                        if (isProfile) {
                          _profileImage = File(picked.path);
                        } else {
                          _idImage = File(picked.path);
                        }
                      });
                    }
                  },
                ),
              ],
            ),
          ),
    );
  }

  void _showErrorDialog(String message) {
    showDialog(
      context: context,
      builder:
          (ctx) => AlertDialog(
            title: const Text("Registration Failed"),
            content: Text(message),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(ctx).pop(),
                child: const Text("OK"),
              ),
            ],
          ),
    );
  }

  Future<void> _selectDate(BuildContext context) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(1900),
      lastDate: DateTime.now(),
    );
    if (picked != null && picked != DateTime.now()) {
      setState(() {
        birthDateController.text =
            "${picked.toLocal()}".split(' ')[0]; // Formatting to YYYY-MM-DD
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios, color: Colors.black),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      backgroundColor: Colors.grey.shade400,
      body: SingleChildScrollView(
        child: Container(
          constraints: BoxConstraints(
            minHeight: MediaQuery.of(context).size.height,
          ),
          padding: const EdgeInsets.fromLTRB(35, 20, 35, 60),
          child: Column(
            children: [
              Align(
                alignment: Alignment.topLeft,
                child: Text(
                  'Create Account',
                  style: GoogleFonts.poppins(
                    fontWeight: FontWeight.w800,
                    fontSize: 26,
                    color: Colors.black,
                  ),
                ),
              ),
              const SizedBox(height: 40),
              _buildTextField(
                firstNameController,
                "Enter your first name",
                Icons.person_2_outlined,
              ),
              const SizedBox(height: 15),
              _buildTextField(
                lastNameController,
                "Enter your last name",
                Icons.person_2_outlined,
              ),
              const SizedBox(height: 15),
              _buildTextField(
                emailController,
                "Enter your email",
                Icons.email_outlined,
              ),
              const SizedBox(height: 15),
              _buildTextField(
                addressController,
                "Enter your address",
                Icons.home_outlined,
              ),
              const SizedBox(height: 15),
              _buildTextField(
                contactNumberController,
                "Enter your contact number",
                Icons.phone,
              ),
              const SizedBox(height: 15),
              _buildPasswordField(
                passwordController,
                "Enter your password",
                _isObscured,
                () {
                  setState(() => _isObscured = !_isObscured);
                },
              ),
              const SizedBox(height: 15),
              _buildPasswordField(
                confirmPasswordController,
                "Confirm your password",
                _isObscuredConfirm,
                () {
                  setState(() => _isObscuredConfirm = !_isObscuredConfirm);
                },
              ),
              const SizedBox(height: 15),
              _buildDateField(
                birthDateController,
                "Select your birthdate",
                () => _selectDate(context),
              ),
              const SizedBox(height: 20),

              /// Profile Image Section
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    "Upload Profile Photo:",
                    style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                  ),
                  ElevatedButton.icon(
                    onPressed: () => _pickImage(isProfile: true),
                    icon: const Icon(Icons.image),
                    label: const Text("Choose Image"),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.black,
                      foregroundColor: Colors.white,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              if (_profileImage != null)
                Container(
                  height: 150,
                  width: double.infinity,
                  decoration: BoxDecoration(
                    border: Border.all(color: Colors.black),
                  ),
                  child: Image.file(_profileImage!, fit: BoxFit.cover),
                ),

              const SizedBox(height: 20),

              // Valid ID Upload Section
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    "Upload Valid ID:",
                    style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                  ),
                  ElevatedButton.icon(
                    onPressed: () => _pickImage(isProfile: false),
                    icon: const Icon(Icons.image),
                    label: const Text("Choose Image"),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.black,
                      foregroundColor: Colors.white,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              if (_idImage != null)
                Container(
                  height: 150,
                  width: double.infinity,
                  decoration: BoxDecoration(
                    border: Border.all(color: Colors.black),
                  ),
                  child: Image.file(_idImage!, fit: BoxFit.cover),
                ),

              const SizedBox(height: 40),

              // Sign Up Button
              ElevatedButton(
                onPressed:
                    _isLoading
                        ? null
                        : () async {
                          FocusScope.of(context).unfocus();

                          if (_idImage == null ||
                              _profileImage == null ||
                              firstNameController.text.trim().isEmpty ||
                              lastNameController.text.trim().isEmpty ||
                              emailController.text.trim().isEmpty ||
                              passwordController.text.trim().isEmpty ||
                              confirmPasswordController.text.trim().isEmpty ||
                              addressController.text.trim().isEmpty ||
                              contactNumberController.text.trim().isEmpty ||
                              birthDateController.text.trim().isEmpty) {
                            _showErrorDialog(
                              "Please fill out all fields and upload required images.",
                            );
                            return;
                          }

                          if (passwordController.text.trim() !=
                              confirmPasswordController.text.trim()) {
                            _showErrorDialog("Passwords do not match");
                            return;
                          }

                          setState(() => _isLoading = true);

                          final authProvider = Provider.of<AuthProvider>(
                            context,
                            listen: false,
                          );
                          final result = await authProvider.register(
                            email: emailController.text.trim(),
                            password: passwordController.text.trim(),
                            confirmPassword:
                                confirmPasswordController.text.trim(),
                            firstname: firstNameController.text.trim(),
                            lastname: lastNameController.text.trim(),
                            address: addressController.text.trim(),
                            contactNumber: contactNumberController.text.trim(),
                            birthDate: birthDateController.text.trim(),
                            idImageFile: _idImage!,
                            profileImageFile: _profileImage!,
                          );

                          setState(() => _isLoading = false);

                          if (result == null) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text('Registration Successful!'),
                                backgroundColor: Colors.green,
                              ),
                            );
                            await Future.delayed(const Duration(seconds: 2));
                            Navigator.pushReplacement(
                              context,
                              MaterialPageRoute(
                                builder: (_) => const LoginPage(),
                              ),
                            );
                          } else {
                            _showErrorDialog(result);
                          }
                        },
                style: ElevatedButton.styleFrom(
                  minimumSize: const Size(170, 40),
                ),
                child:
                    _isLoading
                        ? const SizedBox(
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
                          "SIGN UP",
                          style: GoogleFonts.poppins(
                            fontWeight: FontWeight.bold,
                            color: Colors.black,
                            fontSize: 18,
                          ),
                        ),
              ),
              InkWell(
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const LoginPage()),
                  );
                },
                child: Padding(
                  padding: const EdgeInsets.all(10),
                  child: Text(
                    "Login to your account",
                    style: GoogleFonts.poppins(
                      decoration: TextDecoration.underline,
                      color: Colors.black,
                      fontSize: 14,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTextField(
    TextEditingController controller,
    String hint,
    IconData icon,
  ) {
    return Container(
      height: 50,
      decoration: BoxDecoration(
        color: Colors.grey.shade300,
        boxShadow: const [
          BoxShadow(
            color: Color.fromARGB(255, 187, 161, 161),
            blurRadius: 6,
            offset: Offset(3, 3),
          ),
        ],
      ),
      child: TextFormField(
        controller: controller,
        decoration: InputDecoration(
          border: InputBorder.none,
          contentPadding: const EdgeInsets.only(top: 14),
          prefixIcon: Icon(icon),
          hintText: hint,
        ),
        style: const TextStyle(color: Colors.black),
        cursorColor: Colors.black,
      ),
    );
  }

  Widget _buildDateField(
    TextEditingController controller,
    String hint,
    VoidCallback onTap,
  ) {
    return GestureDetector(
      onTap: onTap, // This triggers the date picker
      child: AbsorbPointer(
        // AbsorbPointer prevents manual text input, and ensures the field is interacted with only by tapping.
        child: Container(
          height: 50,
          decoration: BoxDecoration(
            color: Colors.grey.shade300,
            boxShadow: const [
              BoxShadow(
                color: Color.fromARGB(255, 187, 161, 161),
                blurRadius: 6,
                offset: Offset(3, 3),
              ),
            ],
          ),
          child: TextFormField(
            controller: controller,
            decoration: InputDecoration(
              border: InputBorder.none,
              contentPadding: const EdgeInsets.only(top: 14),
              prefixIcon: const Icon(Icons.calendar_today),
              hintText: hint,
            ),
            style: const TextStyle(color: Colors.black),
            cursorColor: Colors.black,
            readOnly: true, // Make it read-only to prevent manual typing
          ),
        ),
      ),
    );
  }

  Widget _buildPasswordField(
    TextEditingController controller,
    String hint,
    bool isObscured,
    VoidCallback toggle,
  ) {
    return Container(
      height: 50,
      decoration: BoxDecoration(
        color: Colors.grey.shade300,
        boxShadow: const [
          BoxShadow(
            color: Color.fromARGB(255, 187, 161, 161),
            blurRadius: 6,
            offset: Offset(3, 3),
          ),
        ],
      ),
      child: TextFormField(
        controller: controller,
        obscureText: isObscured,
        decoration: InputDecoration(
          border: InputBorder.none,
          contentPadding: const EdgeInsets.only(top: 14),
          prefixIcon: const Icon(Icons.lock_outline_rounded),
          hintText: hint,
          suffixIcon: IconButton(
            icon: Icon(isObscured ? Icons.visibility_off : Icons.visibility),
            onPressed: toggle,
          ),
        ),
        style: const TextStyle(color: Colors.black),
        cursorColor: Colors.black,
      ),
    );
  }
}
