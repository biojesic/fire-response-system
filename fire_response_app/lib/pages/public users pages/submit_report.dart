import 'package:fire_response_app/provider/submit_report_provider.dart';
import 'package:flutter/material.dart';
import 'package:latlong2/latlong.dart';
import 'package:provider/provider.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class SubmitReportPage extends StatefulWidget {
  @override
  _SubmitReportPageState createState() => _SubmitReportPageState();
}

class _SubmitReportPageState extends State<SubmitReportPage> {
  final _formKey = GlobalKey<FormState>();
  final locationController = TextEditingController();
  final landmarkController = TextEditingController();
  final descriptionController = TextEditingController();

  // Coordinates
  LatLng? firestationLatLng;
  double? existingLatitude;
  double? existingLongitude;

  @override
  void initState() {
    super.initState();
    // Initialize existing coordinates if available
    existingLatitude =
        0.0; // Replace with actual existing latitude if available
    existingLongitude =
        0.0; // Replace with actual existing longitude if available
  }

  Future<void> submitFireReport(BuildContext context) async {
    final submitReportProvider = Provider.of<SubmitReportProvider>(
      context,
      listen: false,
    );

    // Ensure we have valid coordinates before submitting
    double? latitude = firestationLatLng?.latitude ?? existingLatitude;
    double? longitude = firestationLatLng?.longitude ?? existingLongitude;

    if (latitude == null || longitude == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Coordinates are missing, cannot submit report.'),
        ),
      );
      return;
    }

    // Validate form
    if (_formKey.currentState!.validate()) {
      try {
        await submitReportProvider.submitFireReport(
          context,
          formKey: _formKey,
          locationController: locationController,
          landmarkController: landmarkController,
          descriptionController: descriptionController,
          clearFields: () {
            setState(() {
              descriptionController.clear();
              locationController.clear();
              landmarkController.clear();
            });
          },
          geocodedLocation:
              firestationLatLng != null
                  ? {
                    'latitude': firestationLatLng!.latitude,
                    'longitude': firestationLatLng!.longitude,
                  }
                  : null,
          existingLatitude: latitude,
          existingLongitude: longitude,
        );
      } catch (e) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error submitting fire report. Please try again.'),
          ),
        );
      }
    } else {
      // If form is invalid, show a validation message
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Please fill out all required fields.')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Submit Fire Report')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: ListView(
            children: [
              TextFormField(
                controller: locationController,
                decoration: InputDecoration(labelText: 'Location'),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter the location.';
                  }
                  return null;
                },
              ),
              TextFormField(
                controller: landmarkController,
                decoration: InputDecoration(labelText: 'Landmark'),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter a landmark.';
                  }
                  return null;
                },
              ),
              TextFormField(
                controller: descriptionController,
                decoration: InputDecoration(labelText: 'Description'),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter a description.';
                  }
                  return null;
                },
              ),
              SizedBox(height: 20),
              ElevatedButton(
                onPressed: () {
                  submitFireReport(context);
                },
                child: Text('Submit Report'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
