import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:http/http.dart' as http;
// import 'package:geocoding/geocoding.dart';
import 'package:provider/provider.dart';
import 'package:image_picker/image_picker.dart';
import 'package:fire_response_app/provider/submit_report_provider.dart';

class SubmitReportPage extends StatefulWidget {
  @override
  _SubmitReportPageState createState() => _SubmitReportPageState();
}

class _SubmitReportPageState extends State<SubmitReportPage> {
  final _formKey = GlobalKey<FormState>();
  final locationController = TextEditingController();
  final landmarkController = TextEditingController();
  final descriptionController = TextEditingController();

  XFile? _image;
  final ImagePicker _picker = ImagePicker();
  bool isSubmitting = false;

  double? existingLatitude;
  double? existingLongitude;

  // Function to get the user's current location
  Future<void> _getCurrentLocation() async {
    bool serviceEnabled;
    LocationPermission permission;

    // Check if location services are enabled
    serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Location services are disabled. Please enable them.'),
        ),
      );
      return; // Exit the method if location services are disabled
    }

    // Check for location permissions
    permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission != LocationPermission.whileInUse &&
          permission != LocationPermission.always) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Location permission is denied.')),
        );
        return; // Exit the method if permission is denied
      }
    }

    // Get current position (latitude, longitude)
    Position position = await Geolocator.getCurrentPosition(
      desiredAccuracy: LocationAccuracy.high,
    );

    // Validate latitude and longitude values
    if (position.latitude == null || position.longitude == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Unable to fetch valid coordinates.')),
      );
      return;
    }

    // Get the address from the coordinates using Google Maps Geocoding API
    String apiKey =
        'AIzaSyAnst45VUe9XkXDduDBPmuPo7H3YmWDNJ4'; // Replace with your API key
    String url =
        'https://maps.googleapis.com/maps/api/geocode/json?latlng=${position.latitude},${position.longitude}&location_type=ROOFTOP&result_type=street_address&key=$apiKey';

    try {
      // Make HTTP request to the Geocoding API
      final response = await http.get(Uri.parse(url));

      if (response.statusCode == 200) {
        // Parse the response
        var data = json.decode(response.body);
        if (data['status'] == 'OK') {
          // Get the first result from the response
          String address = data['results'][0]['formatted_address'];

          // Update the location field with the address
          setState(() {
            locationController.text = address;
            existingLatitude = position.latitude;
            existingLongitude = position.longitude;
          });
        } else {
          // If no address found, show a message
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('No address found for the location.')),
          );
        }
      } else {
        // Handle error if the API request fails
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to fetch address from Google API.')),
        );
      }
    } catch (e) {
      // Catch errors if the API request fails
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Unable to get address for the location: $e')),
      );
    }
  }

  // Function to get coordinates using Google's Geocoding API
  Future<Map<String, double>?> getCoordinatesFromGoogle(String address) async {
    final apiKey =
        'AIzaSyAnst45VUe9XkXDduDBPmuPo7H3YmWDNJ4'; // Replace with your actual key
    final url = Uri.parse(
      'https://maps.googleapis.com/maps/api/geocode/json?address=${Uri.encodeComponent(address)}&key=$apiKey',
    );

    final response = await http.get(url);
    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      if (data['status'] == 'OK') {
        final location = data['results'][0]['geometry']['location'];
        return {'lat': location['lat'], 'lng': location['lng']};
      }
    }
    return null;
  }

  Future<void> _pickImage() async {
    final pickedFile = await _picker.pickImage(source: ImageSource.gallery);
    if (pickedFile != null) {
      setState(() {
        _image = pickedFile;
      });
    }
  }

  Future<void> _submitForm(BuildContext context) async {
    if (!_formKey.currentState!.validate()) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Please fill out all required fields.')),
      );
      return;
    }

    setState(() => isSubmitting = true);

    double? latitude;
    double? longitude;

    try {
      // Use Google Maps API to get coordinates from manually typed address
      final coords = await getCoordinatesFromGoogle(locationController.text);
      if (coords == null) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Unable to fetch coordinates from location.')),
        );
        setState(() => isSubmitting = false);
        return;
      }
      latitude = coords['lat']!;
      longitude = coords['lng']!;
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error while fetching coordinates.')),
      );
      setState(() => isSubmitting = false);
      return;
    }

    try {
      final provider = Provider.of<SubmitReportProvider>(
        context,
        listen: false,
      );

      await provider.submitFireReport(
        context,
        formKey: _formKey,
        locationController: locationController,
        landmarkController: landmarkController,
        descriptionController: descriptionController,
        clearFields: () {
          locationController.clear();
          landmarkController.clear();
          descriptionController.clear();
        },
        existingLatitude: latitude,
        existingLongitude: longitude,
      );
    } catch (e) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text('Error submitting fire report.')));
    } finally {
      setState(() => isSubmitting = false);
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
              labelField("Location"),
              Row(
                children: [
                  Expanded(
                    child: textInput(
                      locationController,
                      'Enter fire location...',
                      Icon(Icons.location_on, color: Colors.red),
                    ),
                  ),
                  IconButton(
                    icon: Icon(Icons.location_on),
                    onPressed: _getCurrentLocation,
                  ),
                ],
              ),

              // Landmark
              labelField("Landmark"),
              textInput(
                landmarkController,
                'Enter nearest landmark...',
                Icon(Icons.location_city, color: Colors.red),
              ),

              // Description
              labelField("Description"),
              textInput(
                descriptionController,
                'Brief description...',
                Icon(Icons.description, color: Colors.red),
              ),
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

              SizedBox(height: 20),
              Center(
                child: SizedBox(
                  width: 250,
                  height: 55,
                  child: ElevatedButton(
                    onPressed: isSubmitting ? null : () => _submitForm(context),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.white,
                    ),
                    child:
                        isSubmitting
                            ? CircularProgressIndicator()
                            : Text(
                              'Submit Report',
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
    );
  }
}

// Helper widgets for consistent style
Widget labelField(String label) {
  return Padding(
    padding: const EdgeInsets.only(top: 12.0, bottom: 4),
    child: Text(
      label,
      style: TextStyle(
        color: Colors.black,
        fontSize: 16,
        fontWeight: FontWeight.w700,
      ),
    ),
  );
}

Widget textInput(TextEditingController controller, String hint, Icon icon) {
  return Column(
    crossAxisAlignment: CrossAxisAlignment.start, // Align elements to the left
    children: [
      // Text field container
      Container(
        height: 50,
        decoration: BoxDecoration(
          color: Colors.grey.shade300,
          boxShadow: [
            BoxShadow(
              color: const Color.fromARGB(255, 187, 161, 161),
              blurRadius: 6,
              offset: Offset(3, 3),
            ),
          ],
        ),
        child: TextFormField(
          controller: controller,
          validator:
              (value) =>
                  value == null || value.isEmpty
                      ? 'This field is required.'
                      : null,
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: TextStyle(color: Colors.black),
            border: InputBorder.none,
            contentPadding: EdgeInsets.only(top: 14),
            prefixIcon: icon,
            filled: true,
          ),
          style: TextStyle(color: Colors.black),
          cursorColor: Colors.black,
        ),
      ),
      const SizedBox(
        height: 5,
      ), // Add space between the field and error message
    ],
  );
}
