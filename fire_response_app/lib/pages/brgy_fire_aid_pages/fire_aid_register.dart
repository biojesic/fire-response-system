import 'package:fire_response_app/models/brgy_fire_aid.dart';
import 'package:fire_response_app/provider/fire_aid_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'dart:io';
import 'package:image_picker/image_picker.dart';

class FireAidRegisterPage extends StatefulWidget {
  @override
  _FireAidRegisterPageState createState() => _FireAidRegisterPageState();
}

class _FireAidRegisterPageState extends State<FireAidRegisterPage> {
  final _formKey = GlobalKey<FormState>();

  TextEditingController userFirstNameController = TextEditingController();
  TextEditingController userLastNameController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController passwordController = TextEditingController();
  TextEditingController confirmPasswordController = TextEditingController();
  TextEditingController userContactNumberController = TextEditingController();
  TextEditingController userAddressController = TextEditingController();
  TextEditingController userBirthDateController = TextEditingController();
  TextEditingController barangayIdController = TextEditingController();
  TextEditingController firestationController = TextEditingController();

  File? _photoFile;
  File? _idFile;
  File? _certificateFile;

  String? _validateConfirmPassword(String? value) {
    if (value != passwordController.text) {
      return 'Passwords do not match';
    }
    return null;
  }

  Future<File?> _pickImage() async {
    final picked = await ImagePicker().pickImage(source: ImageSource.gallery);
    return picked != null ? File(picked.path) : null;
  }

  void _registerFireAid(BuildContext context) async {
    if (_formKey.currentState?.validate() ?? false) {
      try {
        // Call the provider to register FireAid
        await Provider.of<FireAidProvider>(
          context,
          listen: false,
        ).registerFireAid(
          FireAid(
            id: 0, // backend will assign
            userId: 0, // backend will assign
            barangayId: int.parse(barangayIdController.text),
            // contactNumber: userContactNumberController.text,
            photo: _photoFile?.path,
            barangayIdPath: _idFile?.path,
            barangayCertificatePath: _certificateFile?.path,
          ),
          userFirstName: userFirstNameController.text,
          userLastName: userLastNameController.text,
          email: emailController.text,
          password: passwordController.text,
          userContactNumber: userContactNumberController.text,
          userAddress: userAddressController.text,
          userBirthDate: userBirthDateController.text,
          barangayId: barangayIdController.text,
          barangayIdPath: _idFile!.path,
          barangayCertificatePath: _certificateFile!.path,
          photoPath: _photoFile?.path,
        );

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              'Your request was submitted. We will validate your credentials.',
            ),
          ),
        );
      } catch (error) {
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(SnackBar(content: Text('Error: $error')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Register FireAid')),
      body: SingleChildScrollView(
        child: Padding(
          padding: EdgeInsets.all(16),
          child: Form(
            key: _formKey,
            child: Column(
              children: [
                TextFormField(
                  controller: userFirstNameController,
                  decoration: InputDecoration(labelText: 'First Name'),
                ),
                TextFormField(
                  controller: userLastNameController,
                  decoration: InputDecoration(labelText: 'Last Name'),
                ),
                TextFormField(
                  controller: emailController,
                  decoration: InputDecoration(labelText: 'Email'),
                ),
                TextFormField(
                  controller: passwordController,
                  decoration: InputDecoration(labelText: 'Password'),
                ),
                TextFormField(
                  controller: confirmPasswordController,
                  decoration: InputDecoration(labelText: 'Confirm Password'),
                  validator: _validateConfirmPassword,
                  obscureText: true,
                ),
                TextFormField(
                  controller: userContactNumberController,
                  decoration: InputDecoration(labelText: 'Contact Number'),
                  keyboardType: TextInputType.phone,
                ),
                TextFormField(
                  controller: userAddressController,
                  decoration: InputDecoration(labelText: 'Address'),
                ),
                TextFormField(
                  controller: userBirthDateController,
                  decoration: InputDecoration(labelText: 'Birth Date'),
                ),
                TextFormField(
                  controller: barangayIdController,
                  decoration: InputDecoration(labelText: 'Barangay ID'),
                ),

                // TextFormField(
                //   controller: firestationController,
                //   decoration: InputDecoration(labelText: 'Fire Station'),
                // ),
                SizedBox(height: 7),
                ElevatedButton(
                  onPressed: () async {
                    final picked = await _pickImage();
                    if (picked != null) {
                      setState(() {
                        _photoFile = picked;
                      });
                    }
                  },
                  child: Text(
                    _photoFile == null
                        ? 'Upload Photo (Optional)'
                        : 'Photo Selected',
                  ),
                ),
                SizedBox(height: 7),
                ElevatedButton(
                  onPressed: () async {
                    final picked = await _pickImage();
                    if (picked != null) {
                      setState(() {
                        _idFile = picked;
                      });
                    }
                  },
                  child: Text(
                    _idFile == null ? 'Upload Barangay ID' : 'ID Selected',
                  ),
                ),
                SizedBox(height: 7),
                ElevatedButton(
                  onPressed: () async {
                    final picked = await _pickImage();
                    if (picked != null) {
                      setState(() {
                        _certificateFile = picked;
                      });
                    }
                  },
                  child: Text(
                    _certificateFile == null
                        ? 'Upload Barangay Certificate'
                        : 'Certificate Selected',
                  ),
                ),
                SizedBox(height: 20),
                ElevatedButton(
                  onPressed: () => _registerFireAid(context),
                  child: Text('Create Account'),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
