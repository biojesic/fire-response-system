import 'dart:io';
// import 'package:fire_response_app/provider/fire_report_provider.dart';
import 'package:fire_response_app/provider/submit_report_provider.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';

class EmergencyReport extends StatefulWidget {
  const EmergencyReport({super.key});

  @override
  State<EmergencyReport> createState() => _EmergencyReportState();
}

class _EmergencyReportState extends State<EmergencyReport> {
  final _formKey = GlobalKey<FormState>();
  final locationController = TextEditingController();
  final landmarkController = TextEditingController();
  final descriptionController = TextEditingController();
  final contactinfoController = TextEditingController();
  XFile? _image;
  final ImagePicker _picker = ImagePicker();

  Future<void> _pickImage() async {
    final pickedFile = await _picker.pickImage(source: ImageSource.gallery);
    if (pickedFile != null) {
      setState(() {
        _image = pickedFile;
      });
    }
  }

  void submitFireReport() {
    if (_formKey.currentState!.validate()) {
      // Simulate API submission
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text('Submitting report...')));

      // Clear fields (simulate)
      setState(() {
        locationController.clear();
        landmarkController.clear();
        descriptionController.clear();
        contactinfoController.clear();
        _image = null;
      });

      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text('Report submitted successfully!')));
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Please fill out all required fields.')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Back to Login')),
      body: Stack(
        children: [
          SingleChildScrollView(
            child: Container(
              width: double.infinity,
              padding: EdgeInsets.fromLTRB(20, 25, 20, 20),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [Colors.red.shade800, Colors.black87],
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                ),
              ),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Center(
                      child: Text(
                        "Report a Fire Emergency",
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                          fontSize: 24,
                        ),
                      ),
                    ),
                    SizedBox(height: 30),

                    // Location
                    labelField("Location"),
                    textInput(locationController, 'Enter fire location...'),

                    // Landmark
                    labelField("Landmark"),
                    textInput(landmarkController, 'Enter nearest landmark...'),

                    // Description
                    labelField("Description"),
                    textInput(descriptionController, 'Brief description...'),

                    // Contact Info
                    labelField("Contact Info"),
                    textInput(contactinfoController, 'Your name & contact'),

                    // Image upload
                    SizedBox(height: 10),
                    labelField("Upload Image (optional)"),
                    Center(
                      child: SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          onPressed: _pickImage,
                          icon: Icon(Icons.photo, color: Colors.white),
                          label: Text(
                            'Select from Gallery',
                            style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w700,
                              fontSize: 16,
                            ),
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.white.withOpacity(0.2),
                            padding: EdgeInsets.symmetric(vertical: 16),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(8),
                            ),
                          ),
                        ),
                      ),
                    ),

                    if (_image != null) ...[
                      SizedBox(height: 10),
                      Image.file(
                        File(_image!.path),
                        height: 200,
                        width: double.infinity,
                        fit: BoxFit.cover,
                      ),
                    ],

                    SizedBox(height: 30),
                    Center(
                      child: SizedBox(
                        width: 250,
                        height: 55,
                        child: ElevatedButton(
                          onPressed: () {
                            final provider = Provider.of<SubmitReportProvider>(
                              context,
                              listen: false,
                            );
                            provider.submitFireReportUnauthenticated(
                              context,
                              formKey: _formKey,
                              locationController: locationController,
                              landmarkController: landmarkController,
                              descriptionController: descriptionController,
                              contactinfoController: contactinfoController,
                              clearFields: () {
                                setState(() {
                                  locationController.clear();
                                  landmarkController.clear();
                                  descriptionController.clear();
                                  contactinfoController.clear();
                                  _image = null;
                                });
                              },
                              geocodedLocation:
                                  null, // Optional: Set to null if backend handles it
                              existingLatitude:
                                  null, // Optional: Set to null if not needed
                              existingLongitude: null,
                            );
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.white,
                          ),
                          child: Text(
                            "Submit Report",
                            style: TextStyle(
                              color: Colors.red[600],
                              fontWeight: FontWeight.bold,
                              fontSize: 20,
                            ),
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  // Helper widgets for consistent style
  Widget labelField(String label) {
    return Padding(
      padding: const EdgeInsets.only(top: 12.0, bottom: 4),
      child: Text(label, style: TextStyle(color: Colors.white, fontSize: 16)),
    );
  }

  Widget textInput(TextEditingController controller, String hint) {
    return TextFormField(
      controller: controller,
      validator:
          (value) =>
              value == null || value.isEmpty ? 'This field is required.' : null,
      decoration: InputDecoration(
        hintText: hint,
        hintStyle: TextStyle(color: Colors.white54),
        border: InputBorder.none,
        filled: true,
        fillColor: Colors.white.withOpacity(0.2),
      ),
      style: TextStyle(color: Colors.white),
      cursorColor: Colors.white,
    );
  }
}
