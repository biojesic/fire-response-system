import 'dart:convert';
import 'dart:io';

import 'package:fire_response_app/pages/auth%20pages/login.dart';
import 'package:fire_response_app/provider/auth_provider.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:http/http.dart' as http;
import 'package:fire_response_app/api.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';

class ReapplyScreen extends StatefulWidget {
  final String email;

  const ReapplyScreen({super.key, required this.email});

  @override
  State<ReapplyScreen> createState() => _ReapplyScreenState();
}

class _ReapplyScreenState extends State<ReapplyScreen> {
  final _formKey = GlobalKey<FormState>();

  TextEditingController firstNameController = TextEditingController();
  TextEditingController lastNameController = TextEditingController();
  TextEditingController addressController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController passwordController = TextEditingController();
  TextEditingController confirmPasswordController = TextEditingController();
  TextEditingController birthDateController = TextEditingController();
  TextEditingController contactNumberController = TextEditingController();

  bool _isObscured = true;
  bool _isObscuredConfirm = true;

  ImageProvider? _profileImage;
  ImageProvider? _idImage;

  DateTime? selectedDate;

  String? rejectionReason;

  bool isLoading = true;

  static const String api = API.baseUrl;

  @override
  void initState() {
    super.initState();
    loadRejectedData();
  }

  Future<void> loadRejectedData() async {
    final response = await http.get(
      Uri.parse('$api/civilian/rejected?email=${widget.email}'),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      setState(() {
        firstNameController.text = data['first_name'] ?? '';
        lastNameController.text = data['last_name'] ?? '';
        addressController.text = data['address'] ?? '';
        contactNumberController.text = data['contact_number'] ?? '';
        birthDateController.text = data['birth_date'] ?? '';
        emailController.text = data['email'] ?? '';
        rejectionReason = data['rejection_reason'] ?? '';

        // Load image URLs into memory for preview
        if (data['profile_image_url'] != null &&
            data['profile_image_url'].isNotEmpty) {
          _profileImage = NetworkImage(data['profile_image_url']);
        }

        if (data['id_image_url'] != null && data['id_image_url'].isNotEmpty) {
          _idImage = NetworkImage(data['id_image_url']);
        }

        isLoading = false;
      });
    } else {
      // handle error
      print('Failed to load rejected data: ${response.statusCode}');
    }
  }

  Future<void> submitReapplication() async {
    if (!_formKey.currentState!.validate()) return;

    // Validate required fields and images
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
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text(
            "Please fill out all fields and upload required images.",
            style: TextStyle(color: Colors.white),
          ),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (passwordController.text.trim() !=
        confirmPasswordController.text.trim()) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text(
            "Passwords do not match",
            style: TextStyle(color: Colors.white),
          ),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    final authProvider = Provider.of<AuthProvider>(context, listen: false);

    // Check if both images are selected
    if (_idImage == null || _profileImage == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text(
            "Please upload both ID image and profile image.",
            style: TextStyle(color: Colors.white),
          ),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    // If both images are selected and are of FileImage type, proceed with submission
    if (_idImage is FileImage && _profileImage is FileImage) {
      final idImagePath = (_idImage as FileImage).file.path;
      final profileImagePath = (_profileImage as FileImage).file.path;

      // Call the reapply method in the provider
      final result = await authProvider.reapply(
        email: emailController.text.trim(),
        password: passwordController.text.trim(),
        confirmPassword: confirmPasswordController.text.trim(),
        firstname: firstNameController.text.trim(),
        lastname: lastNameController.text.trim(),
        address: addressController.text.trim(),
        contactNumber: contactNumberController.text.trim(),
        birthDate: birthDateController.text.trim(),
        idImageFile: File(idImagePath), // Convert the path to File
        profileImageFile: File(profileImagePath), // Convert the path to File
      );

      if (result == null) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Reapplication Submitted Successfully!'),
            backgroundColor: Colors.green,
          ),
        );
        // Redirect to login page after successful submission
        await Future.delayed(const Duration(seconds: 2));
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => const LoginPage()),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result, style: TextStyle(color: Colors.white)),
            backgroundColor: Colors.red,
          ),
        );
      }
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text(
            "Invalid image types selected. Please select a valid profile and ID image.",
            style: TextStyle(color: Colors.white),
          ),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _selectDate(BuildContext context) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: selectedDate ?? DateTime(2000),
      firstDate: DateTime(1900),
      lastDate: DateTime.now(),
    );
    if (picked != null) {
      setState(() {
        selectedDate = picked;
        birthDateController.text =
            "${picked.toLocal()}".split(' ')[0]; // YYYY-MM-DD format
      });
    }
  }

  Future<void> _pickImage({required bool isProfile}) async {
    final ImagePicker picker = ImagePicker();

    // Show dialog for user to choose camera or gallery
    final source = await showDialog<ImageSource>(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text("Select image source"),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context, ImageSource.camera),
                child: const Text("Camera"),
              ),
              TextButton(
                onPressed: () => Navigator.pop(context, ImageSource.gallery),
                child: const Text("Gallery"),
              ),
            ],
          ),
    );

    if (source == null) return;

    try {
      final XFile? pickedFile = await picker.pickImage(
        source: source,
        imageQuality: 80,
      );

      if (pickedFile != null) {
        setState(() {
          if (isProfile) {
            _profileImage = FileImage(
              File(pickedFile.path),
            ); // Store it as FileImage
          } else {
            _idImage = FileImage(
              File(pickedFile.path),
            ); // Store it as FileImage
          }
        });
      } else {
        print("No image selected");
      }
    } catch (e) {
      print("Error picking image: $e");
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
              // Rejection Reason
              if (rejectionReason != null && rejectionReason!.isNotEmpty)
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: Colors.red.shade100,
                    borderRadius: BorderRadius.circular(5),
                  ),
                  child: Text(
                    'Application rejected due to $rejectionReason',
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Colors.red,
                    ),
                  ),
                ),
              const SizedBox(height: 15),
              // First Name
              _buildTextField(
                firstNameController,
                "Enter your first name",
                Icons.person_2_outlined,
              ),
              const SizedBox(height: 15),

              // Last Name
              _buildTextField(
                lastNameController,
                "Enter your last name",
                Icons.person_2_outlined,
              ),
              const SizedBox(height: 15),

              // Email
              _buildTextField(
                emailController,
                "Enter your email",
                Icons.email_outlined,
              ),
              const SizedBox(height: 15),

              // Address
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

              _buildDateField(
                birthDateController,
                "Select your birthdate",
                () => _selectDate(context),
              ),
              const SizedBox(height: 15),

              // Password
              _buildPasswordField(
                passwordController,
                "Enter your password",
                _isObscured,
                () {
                  setState(() => _isObscured = !_isObscured);
                },
              ),
              const SizedBox(height: 15),

              // Confirm Password
              _buildPasswordField(
                confirmPasswordController,
                "Confirm your password",
                _isObscuredConfirm,
                () {
                  setState(() => _isObscuredConfirm = !_isObscuredConfirm);
                },
              ),
              const SizedBox(height: 30),

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
                  child: Image(image: _idImage!, fit: BoxFit.cover),
                ),

              const SizedBox(height: 30),

              // Profile Image Upload Section
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
                  child: Image(image: _profileImage!, fit: BoxFit.cover),
                ),

              const SizedBox(height: 40),
              ElevatedButton(
                onPressed: () async {
                  await submitReapplication();
                },
                style: ElevatedButton.styleFrom(
                  minimumSize: const Size(170, 40),
                ),
                child: Text(
                  'Resubmit Application',
                  style: GoogleFonts.poppins(
                    fontWeight: FontWeight.bold,
                    color: Colors.black,
                    fontSize: 18,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
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
      readOnly: true,
      onTap: onTap,
      decoration: InputDecoration(
        border: InputBorder.none,
        contentPadding: const EdgeInsets.only(top: 14),
        hintText: hint,
        prefixIcon: const Icon(Icons.calendar_today, color: Colors.black54),
      ),
      style: const TextStyle(color: Colors.black),
      cursorColor: Colors.black,
    ),
  );
}

Widget _buildPasswordField(
  TextEditingController controller,
  String hint,
  bool isObscured,
  VoidCallback toggleVisibility,
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
        hintText: hint,
        prefixIcon: const Icon(Icons.lock_outline_rounded),
        suffixIcon: IconButton(
          icon: Icon(
            isObscured ? Icons.visibility_off : Icons.visibility,
            color: Colors.black54,
          ),
          onPressed: toggleVisibility,
        ),
      ),
      style: const TextStyle(color: Colors.black),
      cursorColor: Colors.black,
    ),
  );
}
